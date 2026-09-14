<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane walay props
const props = defineProps({
    user: Object,
    doctor: Object, // Optional if user is a doctor
});

const page = usePage();

// Helper to check user initial avatar
const userInitials = computed(() => {
    const name = props.user?.name || 'User';
    return name.substring(0, 2).toUpperCase();
});

// User Role display
const userRoleName = computed(() => {
    return props.user?.roles?.[0]?.name || 'User';
});

const profileForm = useForm({
    _method: 'PUT', // 👈 Yeh line add karna zaroori hai
    name: props.user?.name || '',
    phone: props.user?.phone || props.doctor?.phone || '',
    date_of_birth: props.user?.date_of_birth || '',
    gender: props.user?.gender || '',
    specialization: props.doctor?.specialization || '',
    profile_photo: null,
    address: props.user?.address || '',
    bio: props.user?.bio || '',
});

const updateProfile = () => {
    profileForm.post(route('profile.update'), {
        forceFormData: true, // File upload ke liye zaroori hai (PUT/PATCH over POST with file)
        preserveScroll: true,
    });
};

// Password Update Form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Page Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Account Profile</h1>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Update your account settings, personal details, and profile picture.</p>
                    </div>
                </div>

                <!-- Flash Success Messages -->
                <div v-if="$page.props.flash?.success" class="flex items-center gap-2 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $page.props.flash.success }}</span>
                </div>

                <!-- Global Errors Alert -->
                <div v-if="Object.keys(profileForm.errors).length > 0 || Object.keys(passwordForm.errors).length > 0" class="flex flex-col gap-1 p-4 rounded-2xl bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-200 shadow-sm">
                    <span class="font-bold">Please fix the following errors:</span>
                    <ul class="list-disc pl-5 space-y-0.5">
                        <li v-for="(error, key) in { ...profileForm.errors, ...passwordForm.errors }" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <!-- Profile Card Container -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    
                    <!-- Avatar Header Area -->
                    <div class="p-6 sm:p-8 border-b border-gray-100 flex items-center gap-4">
                        <template v-if="user?.profile_photo_path">
                            <img :src="`/storage/${user.profile_photo_path}`" :alt="user.name"
                                class="w-16 h-16 rounded-2xl object-cover shadow-sm border border-gray-200">
                        </template>
                        <template v-else>
                            <div class="w-16 h-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xl shadow-inner">
                                {{ userInitials }}
                            </div>
                        </template>

                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ user?.name }}</h2>
                            <span class="inline-block mt-1.5 px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-[10px] font-bold uppercase tracking-wider">
                                {{ userRoleName }}
                            </span>
                        </div>
                    </div>

                    <!-- Profile Form -->
                    <form @submit.prevent="updateProfile" class="p-6 sm:p-8 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            <!-- Full Name -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Full Name</label>
                                <input type="text" v-model="profileForm.name" required
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <div v-if="profileForm.errors.name" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.name }}</div>
                            </div>

                            <!-- Email Address (Disabled) -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email Address</label>
                                <input type="email" :value="user?.email" disabled
                                    class="w-full rounded-xl border-gray-200 bg-gray-100 text-xs font-medium text-gray-500 shadow-sm cursor-not-allowed px-4 py-3">
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                                <input type="text" v-model="profileForm.phone" placeholder="+92 300 1234567"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <div v-if="profileForm.errors.phone" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.phone }}</div>
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Date of Birth</label>
                                <input type="date" v-model="profileForm.date_of_birth"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <div v-if="profileForm.errors.date_of_birth" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.date_of_birth }}</div>
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Gender</label>
                                <select v-model="profileForm.gender"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div v-if="profileForm.errors.gender" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.gender }}</div>
                            </div>

                            <!-- Specialization (If Doctor) -->
                            <div v-if="doctor">
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Specialization</label>
                                <input type="text" v-model="profileForm.specialization"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <div v-if="profileForm.errors.specialization" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.specialization }}</div>
                            </div>

                            <!-- Profile Picture File Input -->
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Profile Picture</label>
                                <input type="file" @input="profileForm.profile_photo = $event.target.files[0]"
                                    class="w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition cursor-pointer border border-gray-200 rounded-xl bg-gray-50/50">
                                <p class="text-[11px] text-gray-400 mt-1">Recommended: PNG, JPG, or JPEG (Max 2MB)</p>
                                <div v-if="profileForm.errors.profile_photo" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.profile_photo }}</div>
                            </div>
                        </div>

                        <!-- Address Textarea -->
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Address</label>
                            <textarea v-model="profileForm.address" rows="2" placeholder="Enter full address..."
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"></textarea>
                            <div v-if="profileForm.errors.address" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.address }}</div>
                        </div>

                        <!-- Bio Textarea -->
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bio / About</label>
                            <textarea v-model="profileForm.bio" rows="3" placeholder="Write a short bio..."
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"></textarea>
                            <div v-if="profileForm.errors.bio" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ profileForm.errors.bio }}</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" :disabled="profileForm.processing"
                                class="px-6 py-3 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20 disabled:opacity-50">
                                <span v-if="profileForm.processing">Saving Changes...</span>
                                <span v-else>Save Changes</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Update Password Card Container -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 sm:p-8 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Update Password</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Ensure your account is using a long, random password to stay secure.</p>
                    </div>

                    <!-- Password Flash Success -->
                    <div v-if="$page.props.flash?.password_success" class="mx-6 sm:mx-8 mt-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200 shadow-sm flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $page.props.flash.password_success }}</span>
                    </div>

                    <!-- Password Form -->
                    <form @submit.prevent="updatePassword" class="p-6 sm:p-8 space-y-6">
                        <div class="space-y-4">
                            <!-- Current Password -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Current Password</label>
                                <input type="password" v-model="passwordForm.current_password" required
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <div v-if="passwordForm.errors.current_password" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ passwordForm.errors.current_password }}</div>
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">New Password</label>
                                <input type="password" v-model="passwordForm.password" required
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <div v-if="passwordForm.errors.password" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ passwordForm.errors.password }}</div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                                <input type="password" v-model="passwordForm.password_confirmation" required
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <div v-if="passwordForm.errors.password_confirmation" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ passwordForm.errors.password_confirmation }}</div>
                            </div>
                        </div>

                        <!-- Password Submit Button -->
                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" :disabled="passwordForm.processing"
                                class="px-6 py-3 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20 disabled:opacity-50">
                                <span v-if="passwordForm.processing">Updating Password...</span>
                                <span v-else>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>