@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Add New Patient') }}</h2>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('patients.store') }}" method="POST" class="bg-white p-6 rounded shadow">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1 font-medium" for="name">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium" for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium" for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium" for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="{{ old('dob') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium" for="address">Address</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('address') }}</textarea>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">Save Patient</button>
                    <a href="{{ route('patients.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>

        </div>
    </div>
@endsection