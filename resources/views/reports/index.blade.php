@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Hospital Reports') }}</h2>
            </div>

            <!-- Date Filter Form -->
            <form method="GET" action="{{ route('reports.index') }}" class="mb-6 flex space-x-4 items-end">
                <div>
                    <label for="from" class="block mb-1 font-medium">From</label>
                    <input type="date" id="from" name="from" value="{{ $from ?? '' }}"
                        class="border rounded px-3 py-2 focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div>
                    <label for="to" class="block mb-1 font-medium">To</label>
                    <input type="date" id="to" name="to" value="{{ $to ?? '' }}"
                        class="border rounded px-3 py-2 focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-gray-500">Total Patients</div>
                    <div class="text-3xl font-bold">{{ $patientsCount }}</div>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-gray-500">Total Doctors</div>
                    <div class="text-3xl font-bold">{{ $doctorsCount }}</div>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-gray-500">Total Appointments</div>
                    <div class="text-3xl font-bold">{{ $appointmentsCount }}</div>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-gray-500">Pending Bills</div>
                    <div class="text-3xl font-bold text-red-600">{{ $pendingBillsCount }}</div>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-gray-500">Paid Bills</div>
                    <div class="text-3xl font-bold text-green-600">{{ $paidBillsCount }}</div>
                </div>
            </div>

        </div>
    </div>
@endsection