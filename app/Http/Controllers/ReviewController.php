<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $search = $request->input('search');

        $reviews = Review::with('doctor:id,name')
            ->when($status === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($status === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('patient_name', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%")
                        ->orWhereHas('doctor', fn ($d) => $d->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'filters' => ['status' => $status, 'search' => $search],
            'pendingCount' => Review::where('is_approved', false)->count(),
        ]);
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved. It is now visible on the doctor profile.');
    }

    public function hide(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review hidden from the public website.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
