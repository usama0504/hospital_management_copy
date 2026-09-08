@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-md mx-auto px-6">
            
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Register</h2>

                @if($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block mb-1 font-medium text-sm text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block mb-1 font-medium text-sm text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block mb-1 font-medium text-sm text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block mb-1 font-medium text-sm text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition font-medium">
                        Register
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Login here</a>.
                </p>
            </div>

        </div>
    </div>
@endsection