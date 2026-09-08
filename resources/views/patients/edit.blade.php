@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Edit Patient') }}</h2>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="bg-white p-6 rounded shadow">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block mb-1 font-medium">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $patient->name) }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label for="email" class="block mb-1 font-medium">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $patient->email) }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label for="phone" class="block mb-1 font-medium">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label for="dob" class="block mb-1 font-medium">Date of Birth</label>
                    <input type="date" name="dob" id="dob"
                        value="{{ old('dob', $patient->dob ? \Illuminate\Support\Carbon::parse($patient->dob)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label for="address" class="block mb-1 font-medium">Address</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('address', $patient->address) }}</textarea>
                </div>

                <div class="flex items-center">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700 transition duration-150">
                        Update Patient
                    </button>
                    <a href="{{ route('patients.index') }}"
                        class="ml-4 text-gray-600 hover:underline transition duration-150">Cancel</a>
                </div>
            </form>

        </div>
    </div>
@endsection