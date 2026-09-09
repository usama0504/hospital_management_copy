@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section with Action Button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ __('Patients List') }}</h2>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Manage and monitor all registered hospital patients.</p>
                </div>
                @can('manage patients')
                <div>
                    <a href="{{ route('patients.create') }}" class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/25 hover:bg-orange-600 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Patient
                    </a>
                </div>
                @endcan
            </div>

            <!-- Success Message Alert -->
            @if(session('success'))
                <div class="mb-6 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table Card Container -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Name</th>
                                <th class="py-3.5 px-6">Email</th>
                                <th class="py-3.5 px-6">Phone</th>
                                <th class="py-3.5 px-6">Date of Birth</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                            @forelse($patients as $patient)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-6 font-bold text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($patient->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <span>{{ $patient->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-gray-500">{{ $patient->email }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $patient->phone ?? 'N/A' }}</td>
                                <td class="py-4 px-6 text-gray-500">
                                    {{ $patient->dob ? \Illuminate\Support\Carbon::parse($patient->dob)->format('d M, Y') : '-' }}
                                </td>
                                <td class="py-4 px-6 text-right space-x-3 whitespace-nowrap">
                                    @can('manage patients')
                                    <a href="{{ route('patients.edit', $patient->id) }}" class="font-bold text-gray-600 hover:text-orange-600 transition">Edit</a>
                                    @endcan
                                    @role('admin')
                                    <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this patient?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-rose-500 hover:text-rose-700 transition">Delete</button>
                                    </form>
                                    @endrole
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400 font-medium text-xs">
                                    No patients found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Container -->
                @if($patients->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $patients->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection