@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Doctors List') }}</h2>
            </div>

            <div class="flex justify-between mb-4">
                <a href="{{ route('doctors.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Doctor</a>
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
                        <th class="py-2 px-4">Specialization</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $doctor->name }}</td>
                        <td class="py-2 px-4">{{ $doctor->email }}</td>
                        <td class="py-2 px-4">{{ $doctor->phone }}</td>
                        <td class="py-2 px-4">{{ $doctor->specialization }}</td>
                        <td class="py-2 px-4">
                            <a href="{{ route('doctors.edit', $doctor->id) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                            <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure to delete?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No doctors available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $doctors->links('pagination::tailwind') }}
            </div>

        </div>
    </div>
@endsection