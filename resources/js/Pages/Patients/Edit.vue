<script setup>
import { Link, useForm } from '@inertiajs/vue3';

// Controller se ane wala 'patient' object prop
const props = defineProps({
    patient: Object,
});

// Inertia useForm initialize kar rahe hain existing data ke saath
const form = useForm({
    name: props.patient?.name || '',
    email: props.patient?.email || '',
    phone: props.patient?.phone || '',
    dob: props.patient?.dob ? props.patient.dob.split('T')[0] : '', // YYYY-MM-DD format for date input
    address: props.patient?.address || '',
});

// Update form submission handler (PUT request)
const submit = () => {
    form.put(route('patients.update', props.patient.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Edit Patient</h2>
                    <p class="text-[11px] text-gray-500 font-medium">Update the patient's information below.</p>
                </div>
                <Link :href="route('patients.index')" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    &larr; Back
                </Link>
            </div>

            <!-- Error Messages Alert -->
            <div v-if="Object.keys(form.errors).length > 0" class="flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-xl text-xs font-semibold">
                <span class="font-bold">Please fix the following errors:</span>
                <ul class="list-disc pl-5 space-y-0.5">
                    <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                </ul>
            </div>

            <!-- Form Card Container (2-Column Grid) -->
            <form @submit.prevent="submit" class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 sm:p-6 space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Name</label>
                        <input type="text" id="name" v-model="form.name" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                        <div v-if="form.errors.name" class="text-rose-500 text-[11px] mt-1 font-semibold">{{ form.errors.name }}</div>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                        <input type="email" id="email" v-model="form.email" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                        <div v-if="form.errors.email" class="text-rose-500 text-[11px] mt-1 font-semibold">{{ form.errors.email }}</div>
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Phone</label>
                        <input type="text" id="phone" v-model="form.phone" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                        <div v-if="form.errors.phone" class="text-rose-500 text-[11px] mt-1 font-semibold">{{ form.errors.phone }}</div>
                    </div>

                    <!-- Date of Birth Field -->
                    <div>
                        <label for="dob" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Date of Birth</label>
                        <input type="date" id="dob" v-model="form.dob"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                        <div v-if="form.errors.dob" class="text-rose-500 text-[11px] mt-1 font-semibold">{{ form.errors.dob }}</div>
                    </div>

                    <!-- Address Field (Full Width) -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Address</label>
                        <textarea id="address" v-model="form.address" rows="2"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"></textarea>
                        <div v-if="form.errors.address" class="text-rose-500 text-[11px] mt-1 font-semibold">{{ form.errors.address }}</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center justify-center bg-orange-500 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition disabled:opacity-50">
                        <span v-if="form.processing">Updating...</span>
                        <span v-else>Update Patient</span>
                    </button>
                    <Link :href="route('patients.index')" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition px-3 py-2">
                        Cancel
                    </Link>
                </div>
            </form>

        </div>
    </div>
</template>