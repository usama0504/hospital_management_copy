@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header & Action Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ __('Appointments List') }}</h2>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Manage and monitor all hospital appointments efficiently.</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Today's Available Doctors Button -->
                    <a href="{{ route('receptionist.today') }}" class="inline-flex items-center gap-2 bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50 transition shadow-2xs">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Today's Available Doctors
                    </a>

                    @can('manage appointments')
                    <div>
                        <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-xl font-semibold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Add Appointment
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table Card Container -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs font-medium text-gray-600">
                        <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                            <tr>
                                <th class="py-3.5 px-6">Patient</th>
                                <th class="py-3.5 px-6">Doctor</th>
                                <th class="py-3.5 px-6">Date & Time</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-4 px-6 font-bold text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($appointment->patient->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <span>{{ $appointment->patient->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-gray-700 font-semibold">
                                    Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-gray-500 font-medium">
                                    {{ \Illuminate\Support\Carbon::parse($appointment->appointment_date)->format('d M, Y - h:i A') }}
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $statusClass = match(strtolower($appointment->status)) {
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-[11px] font-bold capitalize shadow-2xs {{ $statusClass }}">
                                        {{ $appointment->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        @can('manage appointments')
                                        <a href="{{ route('appointments.edit', $appointment->id) }}" class="p-2 rounded-xl bg-gray-50 text-blue-600 hover:bg-blue-50 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125"/></svg>
                                        </a>
                                        @endcan
                                        
                                        @role('admin')
                                        <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-gray-50 text-rose-600 hover:bg-rose-50 transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-400 py-12">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                        <p class="text-xs font-semibold text-gray-500">No appointments found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $appointments->links() }}
            </div>

        </div>
    </div>
@endsection