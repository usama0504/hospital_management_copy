@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Add New Doctor') }}</h2>
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

            <form action="{{ route('doctors.store') }}" method="POST" class="bg-white p-6 rounded shadow">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block mb-1 font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>

                <div class="mb-4">
                    <label for="email" class="block mb-1 font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>

                <div class="mb-4">
                    <label for="phone" class="block mb-1 font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>

                <div class="mb-4">
                    <label for="specialization" class="block mb-1 font-medium text-gray-700">Specialization</label>
                    <input type="text" name="specialization" id="specialization" value="{{ old('specialization') }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>

                <div class="flex items-center">
                    <button type="submit" class="bg-green-600 text-white rounded px-4 py-2 hover:bg-green-700 transition duration-150">
                        Save Doctor
                    </button>
                    <a href="{{ route('doctors.index') }}" class="ml-4 text-gray-600 hover:underline transition duration-150">
                        Cancel
                    </a>
                </div>
            </form>

        </div>
    </div>
@endsection