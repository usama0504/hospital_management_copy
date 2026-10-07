<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    token: props.token,
    email: props.email ?? '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const input = 'w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition';
</script>

<template>
    <div class="min-h-screen bg-gray-50/50 flex flex-col justify-between selection:bg-orange-500 selection:text-white">

        <header class="w-full py-6 px-6 sm:px-12 flex items-center justify-between border-b border-gray-100 bg-white/80 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-black text-sm shadow-md shadow-orange-500/25">HC</div>
                <h1 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Health Care</h1>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
            <div class="max-w-xl w-full bg-white border border-gray-100 shadow-xl shadow-gray-100/80 rounded-3xl p-8 sm:p-10">

                <div class="mb-8 text-center">
                    <h1 class="text-xl sm:text-3xl font-black text-gray-900 tracking-tight">Reset Password</h1>
                    <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Choose a new password for your account.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" v-model="form.email" id="email" required :class="input" />
                        <div v-if="form.errors.email" class="text-rose-600 text-xs mt-1 font-semibold">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">New password</label>
                        <input type="password" v-model="form.password" id="password" required autofocus :class="input" />
                        <div v-if="form.errors.password" class="text-rose-600 text-xs mt-1 font-semibold">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Confirm password</label>
                        <input type="password" v-model="form.password_confirmation" id="password_confirmation" required :class="input" />
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full inline-flex items-center justify-center bg-orange-500 text-white py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-orange-500/25 hover:bg-orange-600 transition disabled:opacity-50">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Reset password</span>
                    </button>
                </form>

                <p class="mt-8 text-center text-xs sm:text-sm text-gray-500 font-medium">
                    <Link :href="route('login')" class="font-bold text-orange-600 hover:text-orange-700 transition underline underline-offset-2">Back to login</Link>
                </p>
            </div>
        </main>

        <footer class="w-full py-6 px-8 text-center border-t border-gray-100 bg-white/50">
            <p class="text-xs sm:text-sm text-gray-400 font-medium">
                &copy; {{ new Date().getFullYear() }} Health Care. All rights reserved.
            </p>
        </footer>
    </div>
</template>
