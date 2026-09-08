@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Appointments List') }}</h2>
            </div>

            <div class="flex justify-between mb-4">
                <a href="{{ route('appointments.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add Appointment</a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <table class="min-w-full bg-white shadow rounded overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left">Patient</th>
                        <th class="py-2 px-4 text-left">Doctor</th>
                        <th class="py-2 px-4 text-left">Date</th>
                        <th class="py-2 px-4 text-left">Status</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $appointment->patient->name }}</td>
                        <td class="py-2 px-4">{{ $appointment->doctor->name }}</td>
                        <td class="py-2 px-4">{{ \Illuminate\Support\Carbon::parse($appointment->appointment_date)->format('d M, Y H:i') }}</td>
                        <td class="py-2 px-4">{{ $appointment->status }}</td>
                        <td class="py-2 px-4 whitespace-nowrap">
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                            <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 p-4">No appointments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
              {{ $appointments->links('pagination::tailwind') }}
            </div>

        </div>
    </div>
@endsection