@extends('layouts.app')

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Greeting Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">
                         Welcome, {{ auth()->user()->name }} 👋
                    </h2>
                    {{-- <p class="text-gray-500 mt-1">
                        Here's what's happening at your hospital today, {{ now()->format('l, d M Y') }}.
                    </p> --}}
                </div>
                <span class="inline-flex items-center self-start sm:self-auto px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-700 capitalize">
                    {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                </span>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">

                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Total Patients</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $patientsCount ?? 0 }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Total Doctors</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $doctorsCount ?? 0 }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Appointments</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $appointmentsCount ?? 0 }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Pending Bills</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingBillsCount ?? 0 }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Paid Bills</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $paidBillsCount ?? 0 }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-10">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

                    <a href="{{ route('patients.index') }}" class="flex flex-col items-center justify-center gap-2 p-5 bg-white hover:bg-blue-50 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Patients</span>
                    </a>

                    <a href="{{ route('doctors.index') }}" class="flex flex-col items-center justify-center gap-2 p-5 bg-white hover:bg-green-50 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Doctors</span>
                    </a>

                    <a href="{{ route('appointments.index') }}" class="flex flex-col items-center justify-center gap-2 p-5 bg-white hover:bg-purple-50 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Appointments</span>
                    </a>

                    <a href="{{ route('bills.index') }}" class="flex flex-col items-center justify-center gap-2 p-5 bg-white hover:bg-indigo-50 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Billing</span>
                    </a>

                    <a href="{{ route('reports.index') }}" class="flex flex-col items-center justify-center gap-2 p-5 bg-white hover:bg-amber-50 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m4 8V5m4 12v-6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Reports</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                <!-- Recent Appointments -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">Recent Appointments</h3>
                        <a href="{{ route('appointments.index') }}" class="text-sm text-indigo-600 hover:underline font-semibold">View all</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentAppointments as $appointment)
                            <div class="flex items-center justify-between px-5 py-3">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $appointment->patient->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">
                                        Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                                        &middot;
                                        {{ \Illuminate\Support\Carbon::parse($appointment->appointment_date)->format('d M, H:i') }}
                                    </p>
                                </div>
                                <span @class([
                                    'text-xs font-semibold px-2.5 py-1 rounded-full',
                                    'bg-green-100 text-green-700' => $appointment->status === 'Completed',
                                    'bg-red-100 text-red-700' => $appointment->status === 'Cancelled',
                                    'bg-yellow-100 text-yellow-700' => !in_array($appointment->status, ['Completed', 'Cancelled']),
                                ])>
                                    {{ $appointment->status }}
                                </span>
                            </div>
                        @empty
                            <p class="px-5 py-6 text-center text-sm text-gray-400">No appointments yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Patients -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">Recently Added Patients</h3>
                        <a href="{{ route('patients.index') }}" class="text-sm text-indigo-600 hover:underline font-semibold">View all</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentPatients as $patient)
                            <div class="flex items-center gap-3 px-5 py-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold shrink-0">
                                    {{ strtoupper(substr($patient->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $patient->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $patient->email }} &middot; {{ $patient->phone }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="px-5 py-6 text-center text-sm text-gray-400">No patients yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection