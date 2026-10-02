<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $messages = ContactMessage::query()
            ->when($status === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($status === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ContactMessages/Index', [
            'messages' => $messages,
            'filters' => ['status' => $status, 'search' => $search],
            'unreadCount' => ContactMessage::whereNull('read_at')->count(),
        ]);
    }

    public function markRead(ContactMessage $contactMessage)
    {
        if (! $contactMessage->read_at) {
            $contactMessage->forceFill(['read_at' => now()])->save();
        }

        return back();
    }

    public function markUnread(ContactMessage $contactMessage)
    {
        $contactMessage->forceFill(['read_at' => null])->save();

        return back();
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return back()->with('success', 'Message deleted.');
    }
}
