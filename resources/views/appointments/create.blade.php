@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Add Appointment') }}</h2>
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

            <form action="{{ route('appointments.store') }}" method="POST" class="bg-white p-6 rounded shadow">
                @csrf
                <div class="mb-4">
                    <label for="patient_id" class="block mb-1 font-medium">Patient</label>
                    <select name="patient_id" id="patient_id" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select Patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="doctor_id" class="block mb-1 font-medium">Doctor</label>
                    <select name="doctor_id" id="doctor_id" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="appointment_date" class="block mb-1 font-medium">Appointment Date & Time</label>
                    <input type="datetime-local" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                </div>

                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">Status</label>
                    <select name="status" id="status" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="Scheduled" {{ old('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">Save Appointment</button>
                    <a href="{{ route('appointments.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>

        </div>
    </div>
@endsection