<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Database se users fetch karein (pagination ke sath)
        $users = User::latest()->paginate(10);

        // JSON response return karein
        return response()->json([
            'status' => 'success',
            'message' => 'Users list fetched successfully',
            'data' => $users
        ]);
    }
}