@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Account Profile</h1>
                <p class="text-xs text-gray-400 mt-1">Update your account settings, personal details, and profile picture.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-50 text-red-600 text-xs font-bold border border-red-100">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center gap-4">
            
                @if ($user->profile_photo_path ?? false)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}"
                        class="w-16 h-16 rounded-2xl object-cover shadow-sm border border-gray-200">
                @else
                    <div
                        class="w-16 h-16 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-black text-xl shadow-inner">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif

                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                    {{-- <p class="text-xs text-gray-400">{{ $user->email }}</p> --}}
                    <span
                        class="inline-block mt-2 px-2.5 py-0.5 rounded-full bg-orange-50 text-orange-600 text-[10px] font-bold uppercase">
                       {{ $user->roles->first()?->name ?? 'User' }}
                    </span>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Email Address</label>
                        <input type="email" value="{{ $user->email }}"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm text-gray-500 shadow-sm cursor-not-allowed"
                            disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Phone Number</label>
                        <input type="text" name="phone"
                            value="{{ old('phone', $user->phone ?? ($doctor->phone ?? '')) }}"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                            placeholder="+92 300 1234567">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Gender</label>
                        <select name="gender"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male
                            </option>
                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female
                            </option>
                            <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other
                            </option>
                        </select>
                    </div>

                    @if (isset($doctor) && $doctor)
  
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Specialization</label>
                            <input type="text" name="specialization"
                                value="{{ old('specialization', $doctor->specialization ?? '') }}"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        </div>
                    @endif

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Profile Picture</label>
                        <input type="file" name="profile_photo"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition cursor-pointer border border-gray-200 rounded-xl bg-gray-50/50">
                        <p class="text-[11px] text-gray-400 mt-1">Recommended: PNG, JPG, or JPEG (Max 2MB)</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                        placeholder="Enter full address...">{{ old('address', $user->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Bio / About</label>
                    <textarea name="bio" rows="3"
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                        placeholder="Write a short bio...">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Update Password</h2>
            <p class="text-xs text-gray-400 mt-0.5">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        @if(session('password_success'))
            <div class="m-6 mb-0 p-4 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                {{ session('password_success') }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Current Password -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Current Password</label>
                    <input type="password" name="current_password" class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm" required>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">New Password</label>
                    <input type="password" name="password" class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm" required>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm" required>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20">
                    Update Password
                </button>
            </div>
        </form>
    </div>  

    </div>

    
@endsection
