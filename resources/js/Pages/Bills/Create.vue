<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane walay props (patients, doctors aur appointments lists)
defineProps({
    patients: Array,
    doctors: Array,
    appointments: Array,
});

// Inertia useForm helper initialized with default bill fields
const form = useForm({
    patient_id: '',
    doctor_id: '',
    appointment_id: '', // <-- Yeh field add ki gayi hai
    amount: '',
    status: 'Unpaid',
    bill_date: '',
});

// Form submit handler (POST request to store route)
const submit = () => {
    form.post(route('bills.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Header Section -->
                <div class="mb-4 sm:mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Add New Bill</h2>
                        <p class="text-[11px] sm:text-xs text-gray-500 font-medium">Create and record a new billing entry quickly.</p>
                    </div>
                    <Link :href="route('bills.index')" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-white sm:bg-gray-50 px-3.5 py-2 rounded-xl border border-gray-200 shadow-2xs shrink-0">
                        &larr; Back
                    </Link>
                </div>

                <!-- Error Messages Alert -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-4 sm:mb-6 flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                    <span class="font-bold">Please fix the following errors:</span>
                    <ul class="list-disc pl-5 space-y-0.5">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <!-- Form Card Container (2-Column Grid) -->
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
                            <label for="doctor_id" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Consulting Doctor</label>
                            <select v-model="form.doctor_id" id="doctor_id" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <option value="">Select Doctor</option>
                                <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                    Dr. {{ doctor.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.doctor_id" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.doctor_id }}</div>
                        </div>

                        <!-- Appointment Selection (New Added) -->
                        <div class="sm:col-span-2">
                            <label for="appointment_id" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Linked Appointment (Optional)</label>
                            <select v-model="form.appointment_id" id="appointment_id"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <option value="">Select Appointment (if applicable)</option>
                                <option v-for="appointment in appointments" :key="appointment.id" :value="appointment.id">
                                    ID: {{ appointment.id }} — {{ appointment.appointment_date }}
                                </option>
                            </select>
                            <div v-if="form.errors.appointment_id" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.appointment_id }}</div>
                        </div>

                        <!-- Amount Field -->
                        <div>
                            <label for="amount" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Amount</label>
                            <input type="number" step="0.01" v-model="form.amount" id="amount" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" placeholder="0.00" />
                            <div v-if="form.errors.amount" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.amount }}</div>
                        </div>

                        <!-- Status Selection -->
                        <div>
                            <label for="status" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status</label>
                            <select v-model="form.status" id="status" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <option value="Unpaid">Unpaid</option>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                            </select>
                            <div v-if="form.errors.status" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.status }}</div>
                        </div>

                        <!-- Bill Date Field -->
                        <div class="sm:col-span-2">
                            <label for="bill_date" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bill Date</label>
                            <input type="date" v-model="form.bill_date" id="bill_date" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.bill_date" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.bill_date }}</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <Link :href="route('bills.index')" class="w-full sm:w-auto text-center text-xs font-bold text-gray-500 hover:text-gray-900 transition px-4 py-2.5 rounded-xl border border-transparent hover:border-gray-200">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="w-full sm:w-auto inline-flex items-center justify-center bg-orange-500 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition disabled:opacity-50">
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Bill</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>