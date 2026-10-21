<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Patient;
use App\Services\Payments\PaymentService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * Portal: patient JazzCash se paise bhej kar Transaction ID submit karta hai.
 * Bill tab tak "Unpaid" rehta hai jab tak staff payment approve na kare.
 */
class ManualPaymentController extends Controller
{
    private function patient(): Patient
    {
        $patient = Patient::where('user_id', Auth::id())->first();

        abort_if(! $patient, 403, 'No patient record is linked to this account.');

        return $patient;
    }

    private function ownBill(Bill $bill, Patient $patient): void
    {
        abort_unless((int) $bill->patient_id === (int) $patient->id, 404);
    }

    public function show(Bill $bill, PaymentService $payments)
    {
        $patient = $this->patient();
        $this->ownBill($bill, $patient);

        abort_unless($payments->manualJazzcashEnabled(), 404);

        if (! $payments->isPayable($bill)) {
            return redirect()->route('portal.bills')->with('error', 'This bill is already paid or has nothing to pay.');
        }

        $bill->load('doctor');

        return Inertia::render('Portal/ManualPayment', [
            'bill' => $bill->only(['id', 'amount', 'bill_date']) + [
                'doctor' => $bill->doctor?->only(['id', 'name']),
                'reference' => PaymentService::billReference($bill),
            ],
            'account' => $payments->manualJazzcashDetails(),
        ]);
    }

    public function store(Request $request, Bill $bill, PaymentService $payments)
    {
        $patient = $this->patient();
        $this->ownBill($bill, $patient);

        abort_unless($payments->manualJazzcashEnabled(), 404);

        // Spaces hata kar capital kar dete hain taake "ab 123" aur "AB123" ek hi ginay jayen.
        $request->merge([
            'transaction_id' => strtoupper(preg_replace('/\s+/', '', (string) $request->input('transaction_id'))),
        ]);

        $validated = $request->validate([
            'transaction_id' => [
                'required',
                'regex:/^[A-Z0-9]{6,30}$/',
                Rule::unique('payments', 'transaction_id'),
            ],
            'sender_number' => ['nullable', 'regex:/^[0-9+\-\s]{7,20}$/'],
        ], [
            'transaction_id.regex' => 'Enter the Transaction ID exactly as shown in your JazzCash receipt (letters and numbers only).',
            'transaction_id.unique' => 'This Transaction ID has already been used.',
            'sender_number.regex' => 'Enter a valid phone number.',
        ]);

        try {
            $payments->submitManual($bill, $patient, $validated['transaction_id'], $validated['sender_number'] ?? null);
        } catch (DomainException $e) {
            return redirect()->route('portal.bills')->with('error', $e->getMessage());
        }

        return redirect()->route('portal.bills')
            ->with('success', 'Payment details submitted. The hospital will confirm your payment shortly.');
    }
}
