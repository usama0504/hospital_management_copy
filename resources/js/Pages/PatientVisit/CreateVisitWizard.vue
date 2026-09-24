<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    departments: Array,
    doctors: Array,
});

const currentStep = ref(1);

const stepLabels = { 1: 'Patient Info', 2: 'Appointment', 3: 'Billing' };
const currentStepLabel = computed(() => stepLabels[currentStep.value]);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    dob: '',
    department_id: '',
    doctor_id: '',
    appointment_date: '',
    status: 'Scheduled',
    amount: '',
    bill_status: 'Unpaid',
    bill_date: new Date().toISOString().split('T')[0], // Default today
});

// --- Appointment page jaisa hi logic: department -> date -> doctor -> slots ---
const appointmentDate = ref('');
const availableSlots = ref([]);
const loadingSlots = ref(false);

const getSelectedDay = () => {
    if (!appointmentDate.value) return '';
    return new Date(appointmentDate.value + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long' });
};

const filteredDoctors = computed(() => {
    if (!form.department_id || !appointmentDate.value) return [];
    const selectedDay = getSelectedDay();

    return props.doctors.filter(doctor => {
        const sameDepartment = String(doctor.department_id) === String(form.department_id);
        const availableThatDay = doctor.availabilities?.some(
            a => a.day_of_week === selectedDay && a.is_active
        );
        return sameDepartment && availableThatDay;
    });
});

const resetSelection = () => {
    form.doctor_id = '';
    form.appointment_date = '';
    availableSlots.value = [];
};

const loadSlots = async () => {
    if (!form.doctor_id || !appointmentDate.value) {
        availableSlots.value = [];
        return;
    }

    loadingSlots.value = true;
    try {
        const response = await axios.get(route('appointments.available-slots'), {
            params: { doctor_id: form.doctor_id, date: appointmentDate.value },
        });
        availableSlots.value = response.data.slots;
    } catch (error) {
        console.error(error);
        availableSlots.value = [];
    } finally {
        loadingSlots.value = false;
    }
};

const selectSlot = (slot) => {
    form.appointment_date = `${appointmentDate.value}T${slot}`;
};

const formatSlot = (slot) => {
    if (!slot) return '';
    const [hour, minute] = slot.split(':');
    const date = new Date();
    date.setHours(hour, minute, 0);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

const selectedDoctor = computed(() =>
    props.doctors.find(d => String(d.id) === String(form.doctor_id)) || null
);

// Doctor select hote hi uski consultation fee bill amount mein auto-fill ho jaye
// (amount phir bhi Step 3 mein edit ki ja sakti hai)
watch(() => form.doctor_id, () => {
    form.amount = selectedDoctor.value ? selectedDoctor.value.consultation_fee ?? '' : '';
    if (form.doctor_id && appointmentDate.value) loadSlots();
});
// ---------------------------------------------------------------------------

// Step-wise Validation logic
const nextStep = () => {
    form.clearErrors();

    if (currentStep.value === 1) {
        if (!form.name) form.setError('name', 'The full name field is required.');
        if (!form.phone) form.setError('phone', 'The phone number field is required.');
    }
    else if (currentStep.value === 2) {
        if (!form.department_id) form.setError('department_id', 'Please select a department.');
        if (!appointmentDate.value) form.setError('appointment_date', 'Please select an appointment date.');
        else if (!form.doctor_id) form.setError('doctor_id', 'Please select a consulting doctor.');
        else if (!form.appointment_date) form.setError('appointment_date', 'Please select an available time slot.');
    }
    else if (currentStep.value === 3) {
        if (!form.amount) form.setError('amount', 'The billing amount is required.');
    }

    if (Object.keys(form.errors).length === 0) {
        if (currentStep.value < 3) {
            currentStep.value++;
        }
    }
};

const prevStep = () => {
    form.clearErrors();
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submit = () => {
    form.clearErrors();
    if (!form.amount) {
        form.setError('amount', 'The billing amount is required.');
        return;
    }

    form.post(route('patient.visit.store'), {
        preserveScroll: true,
        onError: (errors) => {
            if (errors.department_id || errors.doctor_id || errors.appointment_date) currentStep.value = 2;
            else if (errors.name || errors.email || errors.phone || errors.dob || errors.address) currentStep.value = 1;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-4 sm:py-8">
            <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                    <div class="min-w-0">
                        <h3 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight">New Patient Appointment
                            Register</h3>
                        <p class="text-[11px] sm:text-xs text-gray-500 font-medium">Register patient, schedule
                            appointment, and generate bill in one smooth flow.</p>
                    </div>
                    <Link :href="route('appointments.index')"
                        class="w-full sm:w-auto text-center text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-white sm:bg-gray-50 px-3.5 py-2.5 sm:py-2 rounded-xl border border-gray-200 shadow-2xs shrink-0">
                        &larr; Back
                    </Link>
                </div>

                <!-- Stepper Indicator Bar -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-3.5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div :class="currentStep >= 1 ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-500'"
                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition shrink-0">
                                1
                            </div>
                            <span class="hidden sm:inline text-xs font-bold"
                                :class="currentStep >= 1 ? 'text-gray-900' : 'text-gray-400'">Patient Info</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-100 mx-2 sm:mx-4"
                            :class="{ 'bg-orange-200': currentStep > 1 }"></div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div :class="currentStep >= 2 ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-500'"
                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition shrink-0">
                                2
                            </div>
                            <span class="hidden sm:inline text-xs font-bold"
                                :class="currentStep >= 2 ? 'text-gray-900' : 'text-gray-400'">Appointment</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-100 mx-2 sm:mx-4"
                            :class="{ 'bg-orange-200': currentStep > 2 }"></div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div :class="currentStep >= 3 ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-500'"
                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition shrink-0">
                                3
                            </div>
                            <span class="hidden sm:inline text-xs font-bold"
                                :class="currentStep >= 3 ? 'text-gray-900' : 'text-gray-400'">Billing</span>
                        </div>
                    </div>
                    <!-- Mobile-only caption: labels hidden above to keep the bar compact, shown here instead -->
                    <p class="sm:hidden text-center text-[11px] font-bold text-gray-700 mt-2.5">
                        Step {{ currentStep }} of 3 — {{ currentStepLabel }}
                    </p>
                </div>

                <!-- Error Messages Alert Box -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                    <span class="font-bold">Please fix the following errors:</span>
                    <ul class="list-disc pl-5 space-y-0.5">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <!-- Form Container -->
                <form @submit.prevent="submit"
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl p-3.5 sm:p-6 space-y-4">

                    <!-- STEP 1 -->
                    <div v-show="currentStep === 1" class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Step 1: Patient Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Full
                                    Name</label>
                                <input type="text" v-model="form.name"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                                    placeholder="John Doe" />
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email
                                    Address</label>
                                <input type="email" v-model="form.email"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                                    placeholder="john@example.com" />
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Phone
                                    Number</label>
                                <input type="text" v-model="form.phone"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                                    placeholder="+92..." />
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Date
                                    of Birth</label>
                                <input type="date" v-model="form.dob"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Address</label>
                                <textarea v-model="form.address" rows="2"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                                    placeholder="Street address..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div v-show="currentStep === 2" class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Step 2: Appointment Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Department</label>
                                <select v-model="form.department_id" @change="resetSelection"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition disabled:opacity-60">
                                    <option value="">Select Department</option>
                                    <option v-for="department in departments" :key="department.id"
                                        :value="department.id">
                                        {{ department.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.department_id"
                                    class="text-rose-500 text-[10px] mt-1 font-semibold">{{ form.errors.department_id }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Appointment
                                    Date</label>
                                <input type="date" v-model="appointmentDate" @change="resetSelection"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition disabled:opacity-60" />
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Consulting
                                    Doctor</label>
                                <select v-model="form.doctor_id" :disabled="!form.department_id || !appointmentDate"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition disabled:opacity-60">
                                    <option value="">Select Doctor</option>
                                    <option v-for="doctor in filteredDoctors" :key="doctor.id" :value="doctor.id">
                                        Dr. {{ doctor.name }}
                                    </option>
                                </select>
                                <div v-if="form.department_id && appointmentDate && filteredDoctors.length === 0"
                                    class="text-rose-500 text-[10px] mt-1 font-semibold">
                                    No doctor available in this department on this day.
                                </div>
                                <div v-if="form.errors.doctor_id" class="text-rose-500 text-[10px] mt-1 font-semibold">
                                    {{ form.errors.doctor_id }}</div>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Available
                                    Time Slots</label>
                                <div v-if="loadingSlots"
                                    class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-xl p-3">
                                    Loading available slots...
                                </div>
                                <div v-else-if="availableSlots.length > 0" class="flex flex-wrap gap-2">
                                    <button v-for="slot in availableSlots" :key="slot" type="button"
                                        @click="selectSlot(slot)" :class="[
                                            form.appointment_date === `${appointmentDate}T${slot}`
                                                ? 'bg-orange-500 text-white border-orange-500 shadow-xs'
                                                : 'bg-orange-50 text-orange-600 border-orange-200 hover:bg-orange-500 hover:text-white',
                                            'px-3.5 py-1.5 rounded-lg border text-xs font-bold transition'
                                        ]">
                                        {{ formatSlot(slot) }}
                                    </button>
                                </div>
                                <div v-else-if="form.doctor_id && appointmentDate && !loadingSlots"
                                    class="text-xs text-rose-600 bg-rose-50 border border-rose-200 rounded-xl p-3 font-semibold">
                                    No available time slots for this doctor.
                                </div>
                                <div v-else
                                    class="text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-xl p-3">
                                    Select department, date and doctor to see available time slots.
                                </div>
                                <div v-if="form.appointment_date" class="mt-2.5 text-xs text-gray-600">
                                    Selected Appointment: <span class="font-bold text-orange-600">{{ appointmentDate }}
                                        {{ formatSlot(form.appointment_date.split('T')[1]) }}</span>
                                </div>
                                <div v-if="form.errors.appointment_date"
                                    class="text-rose-500 text-[10px] mt-1 font-semibold">{{ form.errors.appointment_date
                                    }}</div>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status</label>
                                <select v-model="form.status"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                    <option value="Scheduled">Scheduled</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div v-show="currentStep === 3" class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Step 3: Billing Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Amount</label>
                                <p v-if="selectedDoctor" class="text-[10px] text-gray-500 font-medium mb-1.5">
                                    Dr. {{ selectedDoctor.name }}'s consultation fee is filled in automatically.
                                </p>
                                <input type="number" step="0.01" v-model="form.amount" inputmode="decimal"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                                    placeholder="0.00" />
                                <div v-if="form.errors.amount" class="text-rose-500 text-[10px] mt-1 font-semibold">{{
                                    form.errors.amount }}</div>
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bill
                                    Status</label>
                                <select v-model="form.bill_status"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                    <option value="Unpaid">Unpaid</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bill
                                    Date</label>
                                <input type="date" v-model="form.bill_date"
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="flex flex-col-reverse sm:flex-row items-center sm:justify-between gap-2.5 pt-4 border-t border-gray-100">
                        <button type="button" @click="prevStep" v-if="currentStep > 1"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                            &larr; Previous
                        </button>
                        <div v-else class="hidden sm:block"></div>

                        <div class="w-full sm:w-auto">
                            <button type="button" @click="nextStep" v-if="currentStep < 3"
                                class="w-full sm:w-auto bg-orange-500 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                                Next &rarr;
                            </button>
                            <button type="submit" :disabled="form.processing" v-else
                                class="w-full sm:w-auto bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition disabled:opacity-50">
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Complete & Show Receipt</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>