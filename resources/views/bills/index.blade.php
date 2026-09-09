@extends('layouts.app')

@section('content')
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <!-- Header Section & Add Button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ __('Bills List') }}</h2>
                    <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Manage and track all hospital billing records</p>
                </div>

                @can('manage bills')
                    <div>
                        <a href="{{ route('bills.create') }}"
                            class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-xl shadow-lg shadow-orange-500/20 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Bill
                        </a>
                    </div>
                @endcan
            </div>

            <!-- Success Message Alert -->
            @if (session('success'))
                <div class="mb-6 flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Table Card Container -->
            <div class="bg-white border border-gray-100 shadow-xl shadow-gray-100/80 rounded-3xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead class="bg-gray-50/70">
                            <tr>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Doctor</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Bill Date</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm font-medium text-gray-700">
                            @forelse($bills as $bill)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-semibold text-gray-900">
                                        {{ $bill->patient->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 font-medium">
                                        Dr. {{ $bill->doctor->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-900">
                                        ${{ number_format($bill->amount, 2) }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @php
                                            $statusClass =
                                                strtolower($bill->status) == 'paid'
                                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                    : 'bg-amber-50 text-amber-700 border-amber-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusClass }}">
                                            {{ ucfirst($bill->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 text-xs">
                                        {{ \Illuminate\Support\Carbon::parse($bill->bill_date)->format('d M, Y') }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-right space-x-2">
                                        
                                        <!-- View Receipt Button -->
                                        <a href="{{ route('bills.receipt', $bill->id) }}"
                                            class="inline-flex items-center text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-100 px-3 py-1.5 rounded-lg transition">
                                            Receipt
                                        </a>

                                        @can('manage bills')
                                            <a href="{{ route('bills.edit', $bill->id) }}"
                                                class="inline-flex items-center text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition">
                                                Edit
                                            </a>
                                        @endcan

                                        @role('admin')
                                            <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this bill?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition">
                                                    Delete
                                                </button>
                                            </form>
                                        @endrole
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 px-6 text-center text-gray-400 font-medium text-sm">
                                        No bills found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                @if ($bills->hasPages())
                    <div class="py-4 px-6 bg-gray-50/50 border-t border-gray-100">
                        {{ $bills->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection