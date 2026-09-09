@extends('layouts.app')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full bg-white shadow-xl rounded-2xl border border-gray-100 p-8">
            
            <!-- Heading -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Create an Account</h2>
                <p class="text-sm text-gray-500 mt-1">Enter your details to register</p>
            </div>

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-700">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Two Column Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
                    </div>
                </div>

                <!-- Role (Full Width) -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Register as</label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                        <option value="receptionist" {{ old('role', 'receptionist') == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                        <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-lg shadow-md transition duration-200">
                        Register
                    </button>
                </div>
            </form>

            <!-- Footer Link -->
            <div class="mt-6 text-center text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-orange-600 hover:underline font-medium">Log in</a>
            </div>

        </div>
    </div>
@endsection