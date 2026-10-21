<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Services\AppointmentSlotService;
use App\Services\Payments\PaymentService;
use App\Services\PrescriptionPdf;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * Patient portal: patient sirf APNA data dekhta hai.
 * Har query patient_id se bandhi hai, aur kisi doosre patient ka record
 * maangne par 404 milta hai (taake wo record maujood hone ka pata bhi na chale).
 */
class PortalController extends Controller
{
    private function patient(): Patient
    {
        $patient = Patient::where('user_id', Auth::id())->first();

        abort_if(! $patient, 403, 'No patient record is linked to this account.');

        return $patient;
    }

    private function ownPrescription(Prescription $prescription, Patient $patient): void
    {
        abort_unless((int) $prescription->patient_id === (int) $patient->id, 404);
    }

    public function dashboard()
    {
        $patient = $this->patient();

        $upcoming = Appointment::with('doctor.department')
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['Pending', 'Scheduled'])
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->limit(3)
            ->get();

        $prescriptions = Prescription::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest('prescribed_date')
            ->latest('id')
            ->limit(3)
            ->get();

        return Inertia::render('Portal/Dashboard', [
            'patient' => $patient->only(['id', 'name', 'email', 'phone', 'gender', 'dob']),
            'upcoming' => $upcoming,
            'prescriptions' => $prescriptions,
            'stats' => [
                'appointments' => Appointment::where('patient_id', $patient->id)->count(),
                'prescriptions' => Prescription::where('patient_id', $patient->id)->count(),
                'unpaid' => (float) Bill::where('patient_id', $patient->id)
                    ->whereIn('status', ['Pending', 'Unpaid'])
                    ->sum('amount'),
            ],
        ]);
    }

    public function appointments()
    {
        $patient = $this->patient();

        $appointments = Appointment::with('doctor.department')
            ->where('patient_id', $patient->id)
            ->latest('appointment_date')
            ->paginate(10);

        return Inertia::render('Portal/Appointments', [
            'appointments' => $appointments,
        ]);
    }

    public function createAppointment()
    {
        $this->patient();

        return Inertia::render('Portal/BookAppointment', [
            'departments' => Department::where('status', true)->get(['id', 'name']),
            'doctors' => Doctor::with('department:id,name')
                ->get(['id', 'name', 'specialization', 'department_id', 'consultation_fee']),
        ]);
    }

    public function storeAppointment(Request $request, AppointmentSlotService $slots)
    {
        $patient = $this->patient();

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $doctor = Doctor::findOrFail($validated['doctor_id']);

        if ((int) $doctor->department_id !== (int) $validated['department_id']) {
            return back()->withInput()->withErrors([
                'doctor_id' => 'Selected doctor does not belong to this department.',
            ]);
        }

        $start = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);

        // Patient hamesha login wala patient hai; browser se patient_id kabhi nahi lete.
        DB::transaction(function () use ($slots, $doctor, $start, $patient, $validated) {
            $slots->lockDoctor($doctor->id);
            $slots->assertAvailable($doctor->id, $start, 'appointment_time');

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => $start,
                'status' => 'Pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Public booking ki tarah consultation fee ka bill saath banta hai.
            Bill::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_id' => $appointment->id,
                'amount' => $doctor->consultation_fee ?? 0,
                'status' => 'Unpaid',
                'bill_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('portal.appointments')
            ->with('success', 'Your appointment request has been received. You can pay the consultation fee from My Bills.');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        $patient = $this->patient();

        abort_unless((int) $appointment->patient_id === (int) $patient->id, 404);

        if (
            ! in_array($appointment->status, ['Pending', 'Scheduled'], true)
            || Carbon::parse($appointment->appointment_date)->isPast()
        ) {
            return back()->with('error', 'This appointment can no longer be cancelled online. Please contact the hospital.');
        }

        $appointment->update(['status' => 'Cancelled']);

        return back()->with('success', 'Your appointment has been cancelled.');
    }

    public function prescriptions()
    {
        $patient = $this->patient();

        $prescriptions = Prescription::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest('prescribed_date')
            ->latest('id')
            ->paginate(10);

        return Inertia::render('Portal/Prescriptions', [
            'prescriptions' => $prescriptions,
        ]);
    }

    public function showPrescription(Prescription $prescription)
    {
        $this->ownPrescription($prescription, $this->patient());

        $prescription->load('doctor.department', 'items');

        return Inertia::render('Portal/PrescriptionShow', [
            'prescription' => $prescription,
        ]);
    }

    public function prescriptionPdf(Prescription $prescription, PrescriptionPdf $pdf)
    {
        $this->ownPrescription($prescription, $this->patient());

        return $pdf->download($prescription);
    }

    public function bills(PaymentService $payments)
    {
        $patient = $this->patient();

        $bills = Bill::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest('bill_date')
            ->latest('id')
            ->paginate(10);

        $unpaid = (float) Bill::where('patient_id', $patient->id)
            ->whereIn('status', ['Pending', 'Unpaid'])
            ->sum('amount');

        // Har bill par: JazzCash payment review mein hai? ya reject hui (wajah ke saath)?
        $billIds = $bills->pluck('id');

        $pendingBillIds = Payment::where('gateway', Payment::GATEWAY_JAZZCASH_MANUAL)
            ->where('status', Payment::PENDING)
            ->whereIn('bill_id', $billIds)
            ->pluck('bill_id')
            ->all();

        $rejectionNotes = Payment::where('gateway', Payment::GATEWAY_JAZZCASH_MANUAL)
            ->where('status', Payment::FAILED)
            ->whereNotNull('review_note')
            ->whereIn('bill_id', $billIds)
            ->latest('id')
            ->get()
            ->unique('bill_id')
            ->pluck('review_note', 'bill_id');

        $bills->getCollection()->transform(function ($bill) use ($pendingBillIds, $rejectionNotes) {
            $bill->setAttribute('has_pending_payment', in_array((int) $bill->id, array_map('intval', $pendingBillIds), true));
            $bill->setAttribute('rejection_note', $rejectionNotes[$bill->id] ?? null);

            return $bill;
        });

        return Inertia::render('Portal/Bills', [
            'bills' => $bills,
            'unpaidTotal' => $unpaid,
            'payments' => [
                'enabled' => $payments->isEnabled(),
                'sandbox' => $payments->isSandbox(),
                'manual' => $payments->manualJazzcashEnabled(),
            ],
        ]);
    }

    // ------------------------------------------------------------ Online payment

    /** Bill ki payment shuru karta hai aur patient ko gateway ke checkout par bhejta hai. */
    public function payBill(Bill $bill, PaymentService $payments)
    {
        $patient = $this->patient();

        abort_unless((int) $bill->patient_id === (int) $patient->id, 404);

        try {
            $payment = $payments->start($bill, $patient);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return Inertia::location($payments->checkoutUrl($payment));
    }

    /** Sandbox (test) checkout page. */
    public function sandboxCheckout(Payment $payment, PaymentService $payments)
    {
        $this->guardSandboxPayment($payment, $payments);

        $payment->load('bill.doctor');

        return Inertia::render('Portal/SandboxCheckout', [
            'payment' => $payment,
        ]);
    }

    public function sandboxComplete(Request $request, Payment $payment, PaymentService $payments)
    {
        $this->guardSandboxPayment($payment, $payments);

        $request->validate(['outcome' => ['required', 'in:paid,failed']]);

        $paid = $request->outcome === 'paid';

        $payments->complete($payment, $paid, ['mode' => 'sandbox', 'outcome' => $request->outcome]);

        return redirect()->route('portal.bills')->with(
            $paid ? 'success' : 'error',
            $paid ? 'Payment received. Thank you!' : 'The payment was not completed. Your bill is still unpaid.'
        );
    }

    private function guardSandboxPayment(Payment $payment, PaymentService $payments): void
    {
        abort_unless($payments->isSandbox() && $payments->isEnabled(), 404);
        abort_unless((int) $payment->patient_id === (int) $this->patient()->id, 404);
        abort_unless($payment->status === Payment::PENDING, 404);
    }
}
