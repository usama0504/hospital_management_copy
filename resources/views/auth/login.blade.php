@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-6 text-center">Login</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="email" class="block mb-1 font-medium">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        </div>

        <div class="mb-6">
            <label for="password" class="block mb-1 font-medium">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
            Login
        </button>
    </form>

    <p class="mt-4 text-center text-sm">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Register here</a>.
    </p>
</div>
@endsection
