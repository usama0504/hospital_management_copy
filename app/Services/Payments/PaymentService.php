<?php

namespace App\Services\Payments;

use App\Models\Bill;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    // Gateway lazily resolve hota hai, taake manual JazzCash flow online gateway ki
    // config par depend na kare.
    private function gateway(): PaymentGateway
    {
        return app(PaymentGateway::class);
    }

    public function driver(): string
    {
        return $this->gateway()->name();
    }

    /** Sandbox sirf test mode mein chalta hai. Production mein online payment band. */
    public function isEnabled(): bool
    {
        if ($this->gateway()->name() === 'sandbox' && app()->environment('production')) {
            return false;
        }

        return true;
    }

    public function isSandbox(): bool
    {
        return $this->gateway()->name() === 'sandbox';
    }

    public function isPayable(Bill $bill): bool
    {
        return in_array($bill->status, ['Unpaid', 'Pending'], true) && (float) $bill->amount > 0;
    }

    /**
     * Naya pending payment banata hai. Amount hamesha bill se aata hai.
     *
     * @throws DomainException agar bill pay nahi ho sakta
     */
    public function start(Bill $bill, Patient $patient): Payment
    {
        if (! $this->isEnabled()) {
            throw new DomainException('Online payment is not available right now. Please pay at the hospital reception.');
        }

        if ((int) $bill->patient_id !== (int) $patient->id) {
            throw new DomainException('This bill does not belong to you.');
        }

        if (! $this->isPayable($bill)) {
            throw new DomainException('This bill is already paid or has nothing to pay.');
        }

        return Payment::create([
            'bill_id' => $bill->id,
            'patient_id' => $patient->id,
            'gateway' => $this->gateway()->name(),
            'reference' => 'PAY-' . strtoupper(Str::random(14)),
            'amount' => $bill->amount,
            'currency' => config('payments.currency', 'PKR'),
            'status' => Payment::PENDING,
        ]);
    }

    public function checkoutUrl(Payment $payment): string
    {
        return $this->gateway()->checkoutUrl($payment);
    }

    /**
     * Payment ka nateeja darj karta hai. Do baar bulane se kuch dobara nahi hota.
     */
    public function complete(Payment $payment, bool $success, array $response = [], array $attributes = []): Payment
    {
        return DB::transaction(function () use ($payment, $success, $response, $attributes) {
            $locked = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== Payment::PENDING) {
                return $locked; // pehle hi tay ho chuka
            }

            if (! $success) {
                $locked->update(array_merge($attributes, [
                    'status' => Payment::FAILED,
                    'gateway_response' => $response,
                ]));

                return $locked;
            }

            $bill = Bill::withTrashed()->whereKey($locked->bill_id)->lockForUpdate()->first();

            // Do pending payments mein se doosri bhi kamyab ho jaye to paisa record
            // ho jata hai magar bill dobara "Paid" nahi hota; response mein nishan lag jata hai.
            if ($bill && $bill->status === 'Paid') {
                $response['duplicate'] = true;
            } elseif ($bill) {
                $bill->update(['status' => 'Paid']);
            }

            $locked->update(array_merge($attributes, [
                'status' => Payment::PAID,
                'paid_at' => now(),
                'gateway_response' => $response,
            ]));

            return $locked;
        });
    }

    // ------------------------------------------------------- Manual JazzCash

    public function manualJazzcashEnabled(): bool
    {
        $config = config('payments.manual_jazzcash', []);

        return (bool) ($config['enabled'] ?? false) && filled($config['number'] ?? null);
    }

    /** @return array{number: string, name: string} */
    public function manualJazzcashDetails(): array
    {
        return [
            'number' => (string) config('payments.manual_jazzcash.number'),
            'name' => (string) config('payments.manual_jazzcash.name'),
        ];
    }

    public static function billReference(Bill $bill): string
    {
        return 'BILL-' . str_pad((string) $bill->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Patient ne JazzCash se paise bhej kar Transaction ID di: pending payment
     * banata hai (bill abhi "Unpaid" hi rehta hai jab tak staff approve na kare).
     *
     * @throws DomainException
     */
    public function submitManual(Bill $bill, Patient $patient, string $transactionId, ?string $senderNumber = null): Payment
    {
        if (! $this->manualJazzcashEnabled()) {
            throw new DomainException('JazzCash payment is not available right now. Please pay at the hospital reception.');
        }

        if ((int) $bill->patient_id !== (int) $patient->id) {
            throw new DomainException('This bill does not belong to you.');
        }

        return DB::transaction(function () use ($bill, $patient, $transactionId, $senderNumber) {
            $locked = Bill::whereKey($bill->id)->lockForUpdate()->first();

            if (! $locked || ! $this->isPayable($locked)) {
                throw new DomainException('This bill is already paid or has nothing to pay.');
            }

            $alreadySubmitted = Payment::where('bill_id', $locked->id)
                ->where('gateway', Payment::GATEWAY_JAZZCASH_MANUAL)
                ->where('status', Payment::PENDING)
                ->exists();

            if ($alreadySubmitted) {
                throw new DomainException('You have already submitted a payment for this bill. Please wait for the hospital to confirm it.');
            }

            return Payment::create([
                'bill_id' => $locked->id,
                'patient_id' => $patient->id,
                'gateway' => Payment::GATEWAY_JAZZCASH_MANUAL,
                'reference' => 'PAY-' . strtoupper(Str::random(14)),
                'transaction_id' => $transactionId,
                'sender_number' => $senderNumber,
                'amount' => $locked->amount,
                'currency' => config('payments.currency', 'PKR'),
                'status' => Payment::PENDING,
            ]);
        });
    }

    /**
     * Staff ne JazzCash app mein paise dekh liye: payment aur bill "Paid".
     *
     * @throws DomainException
     */
    public function approve(Payment $payment, User $reviewer): Payment
    {
        $this->assertManualPending($payment);

        $bill = Bill::withTrashed()->find($payment->bill_id);

        if (! $bill || $bill->trashed()) {
            throw new DomainException('The bill for this payment no longer exists.');
        }

        // Patient ke paise bhejne ke baad bill ki amount badal gayi ho to galat bill
        // "Paid" na ho; staff pehle khud dekh le.
        if (round((float) $bill->amount, 2) !== round((float) $payment->amount, 2)) {
            throw new DomainException('The bill amount changed after the patient paid. Reject this payment or fix the bill first.');
        }

        return $this->complete(
            $payment,
            true,
            ['mode' => 'manual', 'approved_by' => $reviewer->id],
            ['reviewed_by' => $reviewer->id, 'reviewed_at' => now()],
        );
    }

    /**
     * Payment nahi mili ya Transaction ID ghalat: reject, bill "Unpaid" hi rehta hai.
     *
     * @throws DomainException
     */
    public function reject(Payment $payment, User $reviewer, string $note): Payment
    {
        $this->assertManualPending($payment);

        return $this->complete(
            $payment,
            false,
            ['mode' => 'manual', 'rejected_by' => $reviewer->id],
            ['reviewed_by' => $reviewer->id, 'reviewed_at' => now(), 'review_note' => $note],
        );
    }

    private function assertManualPending(Payment $payment): void
    {
        if ($payment->gateway !== Payment::GATEWAY_JAZZCASH_MANUAL) {
            throw new DomainException('This is not a manual JazzCash payment.');
        }

        if ($payment->status !== Payment::PENDING) {
            throw new DomainException('This payment has already been reviewed.');
        }
    }
}
