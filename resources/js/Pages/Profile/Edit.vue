<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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

// Profile Info Form
const profileForm = useForm({
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
    <div class="max-w-4xl mx-auto space-y-6 py-4">

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Account Profile</h1>
                <p class="text-xs text-gray-400 mt-1">Update your account settings, personal details, and profile picture.</p>
            </div>
        </div>

        <!-- Flash Success Messages -->
        <div v-if="$page.props.flash?.success" class="p-4 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100 shadow-sm">
            {{ $page.props.flash.success }}
        </div>

        <!-- Global Errors Alert -->
        <div v-if="Object.keys(profileForm.errors).length > 0 || Object.keys(passwordForm.errors).length > 0" class="p-4 rounded-xl bg-red-50 text-red-600 text-xs font-bold border border-red-100 shadow-sm">
            <span class="font-bold block mb-1">Please fix the following errors:</span>
            <ul class="list-disc list-inside space-y-0.5">
                <li v-for="(error, key) in { ...profileForm.errors, ...passwordForm.errors }" :key="key">{{ error }}</li>
            </ul>
        </div>

        <!-- Profile Card Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <!-- Avatar Header Area -->
            <div class="p-6 border-b border-gray-100 flex items-center gap-4">
                <template v-if="user?.profile_photo_path">
                    <img :src="`/storage/${user.profile_photo_path}`" :alt="user.name"
                        class="w-16 h-16 rounded-2xl object-cover shadow-sm border border-gray-200">
                </template>
                <template v-else>
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-black text-xl shadow-inner">
                        {{ userInitials }}
                    </div>
                </template>

                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ user?.name }}</h2>
                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full bg-orange-50 text-orange-600 text-[10px] font-bold uppercase tracking-wider">
                        {{ userRoleName }}
                    </span>
                </div>
            </div>

            <!-- Profile Form -->
            <form @submit.prevent="updateProfile" class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Full Name</label>
                        <input type="text" v-model="profileForm.name" required
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <div v-if="profileForm.errors.name" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.name }}</div>
                    </div>

                    <!-- Email Address (Disabled) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Email Address</label>
                        <input type="email" :value="user?.email" disabled
                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm text-gray-500 shadow-sm cursor-not-allowed">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Phone Number</label>
                        <input type="text" v-model="profileForm.phone" placeholder="+92 300 1234567"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <div v-if="profileForm.errors.phone" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.phone }}</div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Date of Birth</label>
                        <input type="date" v-model="profileForm.date_of_birth"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <div v-if="profileForm.errors.date_of_birth" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.date_of_birth }}</div>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Gender</label>
                        <select v-model="profileForm.gender"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        <div v-if="profileForm.errors.gender" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.gender }}</div>
                    </div>

                    <!-- Specialization (If Doctor) -->
                    <div v-if="doctor">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Specialization</label>
                        <input type="text" v-model="profileForm.specialization"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <div v-if="profileForm.errors.specialization" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.specialization }}</div>
                    </div>

                    <!-- Profile Picture File Input -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Profile Picture</label>
                        <input type="file" @input="profileForm.profile_photo = $event.target.files[0]"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition cursor-pointer border border-gray-200 rounded-xl bg-gray-50/50">
                        <p class="text-[11px] text-gray-400 mt-1">Recommended: PNG, JPG, or JPEG (Max 2MB)</p>
                        <div v-if="profileForm.errors.profile_photo" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.profile_photo }}</div>
                    </div>
                </div>

                <!-- Address Textarea -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Address</label>
                    <textarea v-model="profileForm.address" rows="2" placeholder="Enter full address..."
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"></textarea>
                    <div v-if="profileForm.errors.address" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.address }}</div>
                </div>

                <!-- Bio Textarea -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Bio / About</label>
                    <textarea v-model="profileForm.bio" rows="3" placeholder="Write a short bio..."
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"></textarea>
                    <div v-if="profileForm.errors.bio" class="text-red-500 text-[11px] mt-1 font-semibold">{{ profileForm.errors.bio }}</div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex justify-end">
                    <button type="submit" :disabled="profileForm.processing"
                        class="px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20 disabled:opacity-50">
                        <span v-if="profileForm.processing">Saving Changes...</span>
                        <span v-else>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password Card Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Update Password</h2>
                <p class="text-xs text-gray-400 mt-0.5">Ensure your account is using a long, random password to stay secure.</p>
            </div>

            <!-- Password Flash Success -->
            <div v-if="$page.props.flash?.password_success" class="m-6 mb-0 p-4 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100 shadow-sm">
                {{ $page.props.flash.password_success }}
            </div>

            <!-- Password Form -->
            <form @submit.prevent="updatePassword" class="p-6 space-y-4">
                <div class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Current Password</label>
                        <input type="password" v-model="passwordForm.current_password" required
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <div v-if="passwordForm.errors.current_password" class="text-red-500 text-[11px] mt-1 font-semibold">{{ passwordForm.errors.current_password }}</div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">New Password</label>
                        <input type="password" v-model="passwordForm.password" required
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <div v-if="passwordForm.errors.password" class="text-red-500 text-[11px] mt-1 font-semibold">{{ passwordForm.errors.password }}</div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Confirm Password</label>
                        <input type="password" v-model="passwordForm.password_confirmation" required
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <div v-if="passwordForm.errors.password_confirmation" class="text-red-500 text-[11px] mt-1 font-semibold">{{ passwordForm.errors.password_confirmation }}</div>
                    </div>
                </div>

                <!-- Password Submit Button -->
                <div class="pt-4 flex justify-end">
                    <button type="submit" :disabled="passwordForm.processing"
                        class="px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20 disabled:opacity-50">
                        <span v-if="passwordForm.processing">Updating Password...</span>
                        <span v-else>Update Password</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>