<script setup>
import { Link, useForm } from '@inertiajs/vue3';

// Inertia useForm helper initialized with registration fields
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'receptionist', // Default role
});

// Form submit handler (POST request to register route)
const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'), // Reset passwords on completion
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50/50 flex flex-col justify-between selection:bg-orange-500 selection:text-white">

        <header class="w-full py-6 px-6 sm:px-12 flex items-center justify-between border-b border-gray-100 bg-white/80 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-black text-sm shadow-md shadow-orange-500/25">
                    HC
                </div>
                <h1 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Health Care</h1>
            </div>
    
        </header>

        <!-- Main Register Content -->
        <main class="flex-1 w-full flex items-center justify-center px-4 py-10">
            <div
                class="max-w-xl w-full bg-white shadow-xl shadow-gray-100/80 rounded-3xl border border-gray-100 p-8 sm:p-10 mx-auto">

                <!-- Heading -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Create an Account</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Enter your details to register for Health Care</p>
                </div>

                <!-- Validation Errors Alert -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold shadow-sm">
                    <ul class="list-disc pl-4 space-y-1">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Two Column Fields Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <label for="name"
                                class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Full
                                Name</label>
                            <input type="text" v-model="form.name" id="name" required autofocus
                                class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 outline-none transition"
                                placeholder="John Doe" />
                            <div v-if="form.errors.name" class="text-rose-600 text-xs mt-1 font-semibold">{{
                                form.errors.name }}</div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email"
                                class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email
                                Address</label>
                            <input type="email" v-model="form.email" id="email" required
                                class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 outline-none transition"
                                placeholder="name@example.com" />
                            <div v-if="form.errors.email" class="text-rose-600 text-xs mt-1 font-semibold">{{
                                form.errors.email }}</div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password</label>
                            <input type="password" v-model="form.password" id="password" required
                                class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 outline-none transition"
                                placeholder="••••••••" />
                            <div v-if="form.errors.password" class="text-rose-600 text-xs mt-1 font-semibold">{{
                                form.errors.password }}</div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Confirm
                                Password</label>
                            <input type="password" v-model="form.password_confirmation" id="password_confirmation"
                                required
                                class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 outline-none transition"
                                placeholder="••••••••" />
                        </div>
                    </div>

                    <!-- Role (Full Width) -->
                    <div>
                        <label for="role"
                            class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Register
                            as</label>
                        <select v-model="form.role" id="role" required
                            class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 outline-none transition">
                            <option value="receptionist">Receptionist</option>
                            <option value="doctor">Doctor</option>
                        </select>
                        <div v-if="form.errors.role" class="text-rose-600 text-xs mt-1 font-semibold">{{
                            form.errors.role }}</div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing"
                            class="w-full py-3.5 px-4 bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/20 transition duration-200 disabled:opacity-50">
                            <span v-if="form.processing">Creating Account...</span>
                            <span v-else>Create Account</span>
                        </button>
                    </div>
                </form>

                <!-- Footer Link -->
                <div class="mt-8 text-center text-sm text-gray-600 font-medium">
                    Already have an account?
                    <Link :href="route('login')"
                        class="text-orange-600 hover:text-orange-700 underline font-semibold ml-1">Login</Link>
                </div>

            </div>
        </main>

        <!-- Bottom Simple Footer -->
        <footer class="w-full py-6 px-8 text-center border-t border-gray-100 bg-white/50">
            <p class="text-xs sm:text-sm text-gray-400 font-medium">
                &copy; {{ new Date().getFullYear() }} Health Care. All rights reserved.
            </p>
        </footer>

    </div>
</template>