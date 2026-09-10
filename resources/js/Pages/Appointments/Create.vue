<script setup>
import { Link, useForm } from '@inertiajs/vue3';

// Controller se ane walay props (patients aur doctors lists)
const props = defineProps({
    patients: Array,
    doctors: Array,
});

// Inertia useForm helper for reactive data and form submission
const form = useForm({
    patient_id: '',
    doctor_id: '',
    appointment_date: '',
    status: 'Scheduled', // Default status
});

// Form submit handler
const submit = () => {
    form.post(route('appointments.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="py-6 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-4 sm:mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Add Appointment</h2>
                    <p class="text-[11px] sm:text-xs text-gray-500 font-medium">Schedule a new hospital appointment quickly.</p>
                </div>
                <Link :href="route('appointments.index')" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-white sm:bg-gray-50 px-3.5 py-2 rounded-xl border border-gray-200 shadow-2xs shrink-0">
                    &larr; Back
                </Link>
            </div>

            <!-- Error Messages Alert (Inertia handles form.errors automatically) -->
            <div v-if="Object.keys(form.errors).length > 0" class="mb-4 sm:mb-6 flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                <span class="font-bold">Please fix the following errors:</span>
                <ul class="list-disc pl-5 space-y-0.5">
                    <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                </ul>
            </div>

            <!-- Form Card Container -->
            <form @submit.prevent="submit" class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 sm:p-6 space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Patient Selection -->
                    <div>
                        <label for="patient_id" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Patient</label>
                        <select v-model="form.patient_id" id="patient_id" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                            <option value="">Select Patient</option>
                            <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                                {{ patient.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.patient_id" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.patient_id }}</div>
                    </div>

                    <!-- Doctor Selection -->
                    <div>
                        <label for="doctor_id" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Doctor</label>
                        <select v-model="form.doctor_id" id="doctor_id" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                            <option value="">Select Doctor</option>
                            <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                Dr. {{ doctor.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.doctor_id" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.doctor_id }}</div>
                    </div>

                    <!-- Appointment Date & Time -->
                    <div>
                        <label for="appointment_date" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Appointment Date & Time</label>
                        <input type="datetime-local" v-model="form.appointment_date" id="appointment_date" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                        <div v-if="form.errors.appointment_date" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.appointment_date }}</div>
                    </div>

                    <!-- Status Selection -->
                    <div>
                        <label for="status" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status</label>
                        <select v-model="form.status" id="status" required
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <div v-if="form.errors.status" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.status }}</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                    <Link :href="route('appointments.index')" class="w-full sm:w-auto text-center text-xs font-bold text-gray-500 hover:text-gray-900 transition px-4 py-2.5 rounded-xl border border-transparent hover:border-gray-200">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing" class="w-full sm:w-auto inline-flex items-center justify-center bg-orange-500 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition disabled:opacity-50">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Appointment</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>