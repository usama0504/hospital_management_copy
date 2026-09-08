@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Bills List') }}</h2>
            </div>

            <div class="flex justify-between mb-4">
                <a href="{{ route('bills.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Add Bill</a>
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
                        <th class="py-2 px-4 text-left">Amount</th>
                        <th class="py-2 px-4 text-left">Status</th>
                        <th class="py-2 px-4 text-left">Bill Date</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $bill->patient->name }}</td>
                        <td class="py-2 px-4">{{ number_format($bill->amount, 2) }}</td>
                        <td class="py-2 px-4">{{ $bill->status }}</td>
                        <td class="py-2 px-4">{{ \Illuminate\Support\Carbon::parse($bill->bill_date)->format('d M, Y') }}</td>
                        <td class="py-2 px-4 whitespace-nowrap">
                            <a href="{{ route('bills.edit', $bill->id) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                            <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No bills found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $bills->links('pagination::tailwind') }}
            </div>

        </div>
    </div>
@endsection