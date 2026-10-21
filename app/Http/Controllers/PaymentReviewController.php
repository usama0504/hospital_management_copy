<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\PaymentService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Staff (admin/receptionist, 'manage bills'): patients ki JazzCash payments check
 * karke approve ya reject karta hai.
 */
class PaymentReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->input('status'), [Payment::PENDING, Payment::PAID, Payment::FAILED], true)
            ? $request->input('status')
            : Payment::PENDING;

        $payments = Payment::with('patient', 'bill.doctor', 'reviewer')
            ->where('gateway', Payment::GATEWAY_JAZZCASH_MANUAL)
            ->where('status', $status)
            ->oldest()
            ->paginate(15)
            ->withQueryString();

        $counts = Payment::where('gateway', Payment::GATEWAY_JAZZCASH_MANUAL)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'status' => $status,
            'counts' => [
                'pending' => (int) ($counts[Payment::PENDING] ?? 0),
                'paid' => (int) ($counts[Payment::PAID] ?? 0),
                'failed' => (int) ($counts[Payment::FAILED] ?? 0),
            ],
        ]);
    }

    public function approve(Payment $payment, PaymentService $payments)
    {
        try {
            $payments->approve($payment, Auth::user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payment approved and the bill is marked as paid.');
    }

    public function reject(Request $request, Payment $payment, PaymentService $payments)
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:500'],
        ]);

        try {
            $payments->reject($payment, Auth::user(), $validated['note']);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payment rejected. The bill stays unpaid.');
    }
}
