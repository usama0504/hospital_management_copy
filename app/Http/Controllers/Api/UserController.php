<?php

namespace App\Http\Controllers\Api; // Namespace check kar liyega

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // 1. Saare users ki list JSON mein dene ke liye
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(5);

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    // 2. User ko approve karne ke liye
    public function approve($id)
    {
        $user = User::findOrFail($id);

        $user->forceFill(['is_approved' => true])->save();

        return response()->json([
            'status' => 'success',
            'message' => $user->name . ' has been approved.'
        ]);
    }

    // 3. User ko delete/remove karne ke liye
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot remove your own account.'
            ], 403); // 403 Forbidden status code
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User removed.'
        ]);
    }
}