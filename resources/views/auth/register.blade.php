@extends('layouts.app')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full bg-white shadow-xl shadow-gray-100 rounded-3xl border border-gray-100 p-8 sm:p-10">
            
            <!-- Heading -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Create an Account</h2>
                <p class="text-sm text-gray-500 mt-1">Enter your details to register for E-Health Tips</p>
            </div>

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl text-sm text-red-700">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Two Column Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" placeholder="John Doe" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" placeholder="name@example.com" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" placeholder="••••••••" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" placeholder="••••••••" />
                    </div>
                </div>

                <!-- Role (Full Width) -->
                <div>
                    <label for="role" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Register as</label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        <option value="receptionist" {{ old('role', 'receptionist') == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                        <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/20 transition duration-200">
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Footer Link -->
            <div class="mt-8 text-center text-sm text-gray-600 font-medium">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-orange-600 hover:text-orange-700 underline font-semibold ml-1">Login</a>
            </div>

        </div>
    </div>
@endsection