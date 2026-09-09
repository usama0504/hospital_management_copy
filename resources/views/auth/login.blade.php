@extends('layouts.app')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full bg-white border border-gray-100 shadow-sm rounded-2xl p-8 sm:p-10">
            
            <!-- Header Section -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Welcome Back</h2>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Please sign in to your hospital account</p>
            </div>

            <!-- Success Message Alert -->
            @if(session('success'))
                <div class="mb-5 flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-xs sm:text-sm font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message Alert -->
            @if($errors->any())
                <div class="mb-5 flex items-center bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-xs sm:text-sm font-semibold shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" placeholder="name@example.com" />
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" placeholder="••••••••" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center bg-orange-500 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-orange-500/25 hover:bg-orange-600 transition">
                        Login
                    </button>
                </div>
            </form>

            <!-- Register Link Footer -->
            <p class="mt-6 text-center text-xs sm:text-sm text-gray-500 font-medium">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700 transition ml-1">Register here</a>
            </p>

        </div>
    </div>
@endsection