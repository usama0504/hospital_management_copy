<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane wala doctor prop
const props = defineProps({
    doctor: Object,
    departments: Array,
});

// Inertia useForm helper initialized with existing doctor values
const form = useForm({
    name: props.doctor.name ?? '',
    email: props.doctor.email ?? '',
    phone: props.doctor.phone ?? '',
    specialization: props.doctor.specialization ?? '',
    department_id: props.doctor.department_id ?? '',
    consultation_fee: props.doctor.consultation_fee ?? '',
});

// Form update handler (PUT request to update route)
const submit = () => {
    form.put(route('doctors.update', props.doctor.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header Section -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Edit Doctor</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Update the doctor's profile details below.
                        </p>
                    </div>
                    <Link :href="route('doctors.index')"
                        class="text-xs font-bold text-gray-600 hover:text-gray-900 transition bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                        &larr; Back to Doctors
                    </Link>
                </div>

                <!-- Error Messages Alert -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="mb-6 flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold shadow-sm">
                    <span class="font-bold">Please fix the following errors:</span>
                    <ul class="list-disc pl-5 space-y-0.5">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <!-- Form Card Container (2-Column Grid) -->
                <form @submit.prevent="submit"
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 sm:p-8 space-y-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Name Field -->
                        <div>
                            <label for="name"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Name</label>
                            <input type="text" v-model="form.name" id="name" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.name" class="text-rose-600 text-[11px] mt-1 font-semibold">{{
                                form.errors.name }}</div>
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" v-model="form.email" id="email" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.email" class="text-rose-600 text-[11px] mt-1 font-semibold">{{
                                form.errors.email }}</div>
                        </div>

                        <!-- Phone Field -->
                        <div>
                            <label for="phone"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Phone</label>
                            <input type="text" v-model="form.phone" id="phone" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.phone" class="text-rose-600 text-[11px] mt-1 font-semibold">{{
                                form.errors.phone }}</div>
                        </div>

                        <!-- Specialization Field -->
                        <div>
                            <label for="specialization"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Specialization</label>
                            <input type="text" v-model="form.specialization" id="specialization" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.specialization" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                {{ form.errors.specialization }}</div>
                        </div>

                        <!-- Department Field -->
                        <div>
                            <label for="department_id"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Department</label>
                            <select v-model="form.department_id" id="department_id"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <option value="">Select Department</option>
                                <option v-for="department in departments" :key="department.id" :value="department.id">
                                    {{ department.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.department_id" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                {{ form.errors.department_id }}</div>
                        </div>

                        <!-- Consultation Fee Field -->
                        <div>
                            <label for="consultation_fee"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Consultation
                                Fee</label>
                            <input type="number" step="0.01" min="0" v-model="form.consultation_fee"
                                id="consultation_fee" placeholder="0.00"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.consultation_fee"
                                class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.consultation_fee }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center bg-orange-500 text-white px-6 py-3 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition disabled:opacity-50">
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update Doctor</span>
                        </button>
                        <Link :href="route('doctors.index')"
                            class="text-xs font-bold text-gray-600 hover:text-gray-900 transition px-4 py-2.5">
                            Cancel
                        </Link>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>