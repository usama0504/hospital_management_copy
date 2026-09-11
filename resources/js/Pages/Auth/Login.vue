<script setup>
import { Link, useForm } from '@inertiajs/vue3';

// Props se agar koi flash message (jaise success) arha ho
defineProps({
    status: String,
});

// Inertia useForm helper for login credentials
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Form submit handler (POST request to login route)
const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'), // Password field reset on completion
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50/50 flex flex-col justify-between selection:bg-orange-500 selection:text-white">
        
        <!-- Top Minimal Header -->
        <header class="w-full py-6 px-6 sm:px-12 flex items-center justify-between border-b border-gray-100 bg-white/80 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-black text-sm shadow-md shadow-orange-500/25">
                    HC
                </div>
                <h1 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Health Care</h1>
            </div>
    
        </header>

        <!-- Main Login Content -->
        <main class="flex-1 flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
            <div class="max-w-xl w-full bg-white border border-gray-100 shadow-xl shadow-gray-100/80 rounded-3xl p-8 sm:p-10">
                
                <!-- Header Section -->
                <div class="mb-8 text-center">
                    <h1 class="text-xl sm:text-3xl font-black text-gray-900 tracking-tight">Welcome Back</h1>
                    <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Please sign in to your hospital account</p>
                </div>

                <!-- Success Message Alert (from status prop or session) -->
                <div v-if="status" class="mb-6 flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold shadow-sm">
                    {{ status }}
                </div>

                <!-- Error Message Alert (Inertia handles form.errors automatically) -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-6 flex items-center bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold shadow-sm">
                    {{ Object.values(form.errors)[0] }}
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" v-model="form.email" id="email" required autofocus
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" placeholder="name@example.com" />
                        <div v-if="form.errors.email" class="text-rose-600 text-xs mt-1 font-semibold">{{ form.errors.email }}</div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" v-model="form.password" id="password" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" placeholder="••••••••" />
                        <div v-if="form.errors.password" class="text-rose-600 text-xs mt-1 font-semibold">{{ form.errors.password }}</div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing" class="w-full inline-flex items-center justify-center bg-orange-500 text-white py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-orange-500/25 hover:bg-orange-600 transition disabled:opacity-50">
                            <span v-if="form.processing">Signing in...</span>
                            <span v-else>Login</span>
                        </button>
                    </div>
                </form>

                <!-- Register Link Footer -->
                <p class="mt-8 text-center text-xs sm:text-sm text-gray-500 font-medium">
                    Don't have an account?
                    <Link :href="route('register')" class="font-bold text-orange-600 hover:text-orange-700 transition ml-1 underline underline-offset-2">Register here</Link>
                </p>

            </div>
        </main>

        <!-- Bottom Simple Footer -->
        <<footer class="w-full py-6 px-8 text-center border-t border-gray-100 bg-white/50">
            <p class="text-xs sm:text-sm text-gray-400 font-medium">
                &copy; {{ new Date().getFullYear() }} Health Care. All rights reserved.
            </p>
        </footer>

    </div>
</template>