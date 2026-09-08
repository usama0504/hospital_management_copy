@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Patients List') }}</h2>
            </div>

            <div class="flex justify-between mb-4">
                @can('manage patients')
                <a href="{{ route('patients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add Patient
                </a>
                @endcan
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <table class="min-w-full bg-white shadow rounded overflow-hidden">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="py-2 px-4">Name</th>
                        <th class="py-2 px-4">Email</th>
                        <th class="py-2 px-4">Phone</th>
                        <th class="py-2 px-4">Date of Birth</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $patient->name }}</td>
                        <td class="py-2 px-4">{{ $patient->email }}</td>
                        <td class="py-2 px-4">{{ $patient->phone }}</td>
                        <td class="py-2 px-4">
                            {{ $patient->dob ? \Illuminate\Support\Carbon::parse($patient->dob)->format('d M, Y') : '-' }}
                        </td>
                        <td class="py-2 px-4 whitespace-nowrap">
                            @can('manage patients')
                            <a href="{{ route('patients.edit', $patient->id) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                            @endcan
                            @role('admin')
                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this patient?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                            @endrole
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No patients found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $patients->links('pagination::tailwind') }}
            </div>
            
        </div>
    </div>
@endsection