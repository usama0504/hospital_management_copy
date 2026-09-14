<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    dob: '',
    address: '',
});

const submit = () => {
    form.post(route('patients.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Add New Patient</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Register a new patient into the hospital system.</p>
                    </div>
                    <Link :href="route('patients.index')" class="text-xs font-bold text-gray-600 hover:text-gray-900 transition bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                        &larr; Back to Patients
                    </Link>
                </div>

                <div v-if="Object.keys(form.errors).length > 0" class="flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold shadow-sm">
                    <span class="font-bold">Please fix the following errors:</span>
                    <ul class="list-disc pl-5 space-y-0.5">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <form @submit.prevent="submit" class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 sm:p-8 space-y-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        <div>
                            <label for="name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Name</label>
                            <input type="text" id="name" v-model="form.name" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.name" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" id="email" v-model="form.email" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.email" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label for="phone" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Phone</label>
                            <input type="text" id="phone" v-model="form.phone" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.phone" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.phone }}</div>
                        </div>

                        <div>
                            <label for="dob" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Date of Birth</label>
                            <input type="date" id="dob" v-model="form.dob"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.dob" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.dob }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Address</label>
                            <textarea id="address" v-model="form.address" rows="3"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"></textarea>
                            <div v-if="form.errors.address" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.address }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center bg-orange-500 text-white px-6 py-3 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition disabled:opacity-50">
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Patient</span>
                        </button>
                        <Link :href="route('patients.index')" class="text-xs font-bold text-gray-600 hover:text-gray-900 transition px-4 py-2.5">
                            Cancel
                        </Link>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>