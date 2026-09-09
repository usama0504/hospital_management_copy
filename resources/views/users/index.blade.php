@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="mb-6">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Manage Users</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Approve new staff registrations and manage existing accounts.</p>
            </div>

            <!-- Session Alerts -->
            @if(session('success'))
                <div class="mb-4 flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-2xl text-xs font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 flex items-center bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs font-semibold shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Users Table Card -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-5">Name</th>
                                <th class="py-3.5 px-5">Email</th>
                                <th class="py-3.5 px-5">Role</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5">Registered</th>
                                <th class="py-3.5 px-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-5 font-bold text-gray-900">{{ $user->name }}</td>
                                    <td class="py-4 px-5 text-gray-600">{{ $user->email }}</td>
                                    <td class="py-4 px-5 capitalize text-gray-600">
                                        <span class="bg-gray-100 px-2.5 py-1 rounded-lg text-[11px] font-semibold">
                                            {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5">
                                        @if($user->is_approved)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="py-4 px-5 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center gap-2">
                                            @if(!$user->is_approved)
                                                <form action="{{ route('users.approve', $user->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1.5 rounded-xl font-bold text-[11px] hover:bg-emerald-100 transition">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block"
                                                    onsubmit="return confirm('Remove this user permanently?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1.5 rounded-xl font-bold text-[11px] hover:bg-rose-100 transition">
                                                        Remove
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-gray-400 font-medium">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>

        </div>
    </div>
@endsection