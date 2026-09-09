@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Manage Availability</h1>
                <p class="text-xs text-gray-400 mt-1">Set your working days and time slots for patient appointments.</p>
            </div>
            <!-- 🔥 Back Button to Doctors Index Page -->
            <div>
                <a href="{{ route('doctors.index') }}"
                    class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-semibold text-xs hover:bg-gray-200 transition">
                    &larr; Back to Doctors List
                </a>
            </div>
        </div>

        <!-- Success / Error Messages -->
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-red-50 text-red-600 text-xs font-bold border border-red-100">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-50 text-red-600 text-xs font-bold border border-red-100">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Add Availability Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Add New Time Slot</h2>
            </div>

            <form action="{{ route('doctor.availability.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Day of Week -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Day of Week</label>
                        <select name="day_of_week"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                            required>
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Start Time</label>
                        <input type="time" name="start_time"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                            required>
                    </div>

                    <!-- End Time -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">End Time</label>
                        <input type="time" name="end_time"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                            required>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20">
                        Add Slot
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Availabilities List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Your Active Slots</h2>
                    <p class="text-xs text-gray-400 mt-0.5">List of all currently configured working schedules.</p>
                </div>
                <span
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-100">
                    {{ $availabilities->count() }} Slots Active
                </span>
            </div>

            <div class="p-6">
                @if ($availabilities->isEmpty())
                    <div class="text-center py-10 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                        <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">No availability slots added yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-4 rounded-l-xl">Day of Week</th>
                                    <th class="py-3.5 px-4">Timing Slot</th>
                                    <th class="py-3.5 px-4 text-right rounded-r-xl">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-xs font-medium text-gray-700">
                                @foreach ($availabilities as $slot)
                                    <tr class="hover:bg-orange-50/30 transition">
                                        <td class="py-4 px-4">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-800">
                                                {{ $slot->day_of_week }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 font-semibold text-gray-600 flex items-center gap-1.5 mt-1">
                                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                            <span class="text-gray-400 font-normal">to</span>
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <form action="{{ route('doctor.availability.destroy', $slot->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this slot?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 font-bold hover:bg-rose-100 transition shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
