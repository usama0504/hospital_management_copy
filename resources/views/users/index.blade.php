@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">

            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">Manage Users</h2>
                <p class="text-gray-500 mt-1">Approve new staff registrations and manage existing accounts.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="text-left py-3 px-5">Name</th>
                            <th class="text-left py-3 px-5">Email</th>
                            <th class="text-left py-3 px-5">Role</th>
                            <th class="text-left py-3 px-5">Status</th>
                            <th class="text-left py-3 px-5">Registered</th>
                            <th class="text-left py-3 px-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr>
                                <td class="py-3 px-5 font-semibold text-gray-800">{{ $user->name }}</td>
                                <td class="py-3 px-5 text-gray-600">{{ $user->email }}</td>
                                <td class="py-3 px-5 capitalize">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                                <td class="py-3 px-5">
                                    @if($user->is_approved)
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">Approved</span>
                                    @else
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-5 whitespace-nowrap">
                                    @if(!$user->is_approved)
                                        <form action="{{ route('users.approve', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:underline font-semibold mr-3">Approve</button>
                                        </form>
                                    @endif
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block"
                                            onsubmit="return confirm('Remove this user permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline font-semibold">Remove</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-400">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>

        </div>
    </div>
@endsection