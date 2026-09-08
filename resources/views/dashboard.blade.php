@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">Hospital Management Dashboard</h2>
            </div>
            
            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div class="bg-white rounded-lg shadow hover:shadow-lg p-6 flex flex-col items-center">
                    <p class="text-gray-500 uppercase text-xs font-semibold mb-2">Total Patients</p>
                    <p class="text-4xl font-bold text-blue-600">{{ $patientsCount ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-lg shadow hover:shadow-lg p-6 flex flex-col items-center">
                    <p class="text-gray-500 uppercase text-xs font-semibold mb-2">Total Doctors</p>
                    <p class="text-4xl font-bold text-green-600">{{ $doctorsCount ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-lg shadow hover:shadow-lg p-6 flex flex-col items-center">
                    <p class="text-gray-500 uppercase text-xs font-semibold mb-2">Appointments</p>
                    <p class="text-4xl font-bold text-purple-600">{{ $appointmentsCount ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-lg shadow hover:shadow-lg p-6 flex flex-col items-center">
                    <p class="text-gray-500 uppercase text-xs font-semibold mb-2">Pending Bills</p>
                    <p class="text-4xl font-bold text-red-600">{{ $pendingBillsCount ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-lg shadow hover:shadow-lg p-6 flex flex-col items-center">
                    <p class="text-gray-500 uppercase text-xs font-semibold mb-2">Paid Bills</p>
                    <p class="text-4xl font-bold text-indigo-600">{{ $paidBillsCount ?? 0 }}</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <a href="{{ route('patients.index') }}" class="flex flex-col justify-center items-center p-8 bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg text-white transition">
                    <svg class="w-14 h-14 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
                        <path d="M6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2"></path>
                    </svg>
                    <span class="text-lg font-semibold">Patients</span>
                </a>

                <a href="{{ route('doctors.index') }}" class="flex flex-col justify-center items-center p-8 bg-green-600 hover:bg-green-700 rounded-lg shadow-lg text-white transition">
                    <svg class="w-14 h-14 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
                        <path d="M6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2"></path>
                    </svg>
                    <span class="text-lg font-semibold">Doctors</span>
                </a>

                <a href="{{ route('appointments.index') }}" class="flex flex-col justify-center items-center p-8 bg-purple-600 hover:bg-purple-700 rounded-lg shadow-lg text-white transition">
                    <svg class="w-14 h-14 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6"></path>
                        <path d="M12 9v6"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                    <span class="text-lg font-semibold">Appointments</span>
                </a>
            </div>

        </div>
    </div>
@endsection