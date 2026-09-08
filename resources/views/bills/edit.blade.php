@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-6">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">{{ __('Edit Bill') }}</h2>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bills.update', $bill->id) }}" method="POST" class="bg-white p-6 rounded shadow">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="patient_id" class="block mb-1 font-medium">Patient</label>
                    <select name="patient_id" id="patient_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-purple-400">
                        <option value="">Select Patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $bill->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block mb-1 font-medium">Amount</label>
                    <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $bill->amount) }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-purple-400" />
                </div>

                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">Status</label>
                    <select name="status" id="status" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-purple-400">
                        <option value="Unpaid" {{ old('status', $bill->status) == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="Paid" {{ old('status', $bill->status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Pending" {{ old('status', $bill->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="bill_date" class="block mb-1 font-medium">Bill Date</label>
                    <input type="date" name="bill_date" id="bill_date" value="{{ old('bill_date', $bill->bill_date->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-purple-400" />
                </div>

                <div>
                    <button type="submit" class="bg-purple-600 text-white rounded px-4 py-2 hover:bg-purple-700">Update Bill</button>
                    <a href="{{ route('bills.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>

        </div>
    </div>
@endsection