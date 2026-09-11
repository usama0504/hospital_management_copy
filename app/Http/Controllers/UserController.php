<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(5);

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);

        $user->forceFill(['is_approved' => true])->save();

        return redirect()->route('users.index')->with('success', $user->name . ' has been approved.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot remove your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User removed.');
    }
}