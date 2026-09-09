@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">{{ __('Add New Patient') }}</h2>
                    <p class="text-[11px] text-gray-500 font-medium">Register a new patient into the hospital system.</p>
                </div>
                <a href="{{ route('patients.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    &larr; Back
                </a>
            </div>

            <!-- Error Messages Alert -->
            @if($errors->any())
                <div class="mb-4 flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-xl text-xs font-semibold">
                    <span class="font-bold">Please fix the following errors:</span>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card Container (2-Column Grid) -->
            <form action="{{ route('patients.store') }}" method="POST" class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 sm:p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                    </div>

                    <!-- Date of Birth Field -->
                    <div>
                        <label for="dob" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Date of Birth</label>
                        <input type="date" name="dob" id="dob" value="{{ old('dob') }}"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                    </div>

                    <!-- Address Field (Full Width) -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Address</label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">{{ old('address') }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center justify-center bg-orange-500 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                        Save Patient
                    </button>
                    <a href="{{ route('patients.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition px-3 py-2">
                        Cancel
                    </a>
                </div>
            </form>

        </div>
    </div>
@endsection