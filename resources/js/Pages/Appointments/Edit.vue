<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { ref, watch } from 'vue';

const props = defineProps({
    appointment: Object,
    patients: Array,
    departments: Array,
    doctors: Array,
});

const formatLocalDateTime = dateString => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const pad = n => String(n).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const existingDateTime = formatLocalDateTime(
    props.appointment?.appointment_date
);

const appointmentDate = ref(
    existingDateTime
        ? existingDateTime.split('T')[0]
        : ''
);

const currentTime = ref(
    existingDateTime
        ? existingDateTime.split('T')[1]
        : ''
);

const availableSlots = ref([]);
const loadingSlots = ref(false);

const form = useForm({
    patient_id: props.appointment?.patient_id || '',
    department_id: props.appointment?.doctor?.department_id || '',
    doctor_id: props.appointment?.doctor_id || '',
    appointment_date: existingDateTime,
    status: props.appointment?.status || 'Scheduled',
});

const getSelectedDay = () => {
    if (!appointmentDate.value) return '';

    const date = new Date(
        appointmentDate.value + 'T00:00:00'
    );

    return date.toLocaleDateString('en-US', {
        weekday: 'long',
    });
};

const filteredDoctors = () => {
    if (!form.department_id || !appointmentDate.value) {
        return [];
    }

    const selectedDay = getSelectedDay();

    return props.doctors.filter(doctor => {
        const sameDepartment =
            String(doctor.department_id) ===
            String(form.department_id);

        const availableThatDay =
            doctor.availabilities?.some(
                availability =>
                    availability.day_of_week === selectedDay &&
                    availability.is_active
            );

        return sameDepartment && availableThatDay;
    });
};

const formatSlot = slot => {
    if (!slot) return '';

    const [hour, minute] = slot.split(':');
    const date = new Date();

    date.setHours(hour, minute, 0);

    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};

const loadSlots = async () => {
    if (!form.doctor_id || !appointmentDate.value) {
        availableSlots.value = [];
        return;
    }

    loadingSlots.value = true;

    try {
        const response = await axios.get(
            route('appointments.available-slots'),
            {
                params: {
                    doctor_id: form.doctor_id,
                    date: appointmentDate.value,
                    appointment_id: props.appointment.id,
                },
            }
        );

        availableSlots.value = response.data.slots;

        if (
            currentTime.value &&
            !availableSlots.value.includes(currentTime.value)
        ) {
            availableSlots.value.unshift(currentTime.value);
        }
    } catch (error) {
        console.error(error);
        availableSlots.value = [];
    } finally {
        loadingSlots.value = false;
    }
};

const selectDepartment = () => {
    form.doctor_id = '';
    currentTime.value = '';
    form.appointment_date = '';
    availableSlots.value = [];
};

const selectDate = () => {
    form.doctor_id = '';
    currentTime.value = '';
    form.appointment_date = '';
    availableSlots.value = [];
};

const selectDoctor = () => {
    currentTime.value = '';
    form.appointment_date = '';
    availableSlots.value = [];

    loadSlots();
};

const selectSlot = slot => {
    currentTime.value = slot;

    form.appointment_date =
        `${appointmentDate.value}T${slot}`;
};

const submit = () => {
    form.put(
        route('appointments.update', props.appointment.id),
        {
            preserveScroll: true,
        }
    );
};

watch(
    () => form.doctor_id,
    () => {
        if (form.doctor_id && appointmentDate.value) {
            loadSlots();
        }
    }
);

loadSlots();
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-4 sm:mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                            Edit Appointment
                        </h2>

                        <p class="text-[11px] sm:text-xs text-gray-500 font-medium">
                            Change appointment details or select a new time slot.
                        </p>
                    </div>

                    <Link :href="route('appointments.index')"
                        class="text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-white sm:bg-gray-50 px-3.5 py-2 rounded-xl border border-gray-200 shadow-2xs shrink-0">
                        &larr; Back
                    </Link>
                </div>

                <!-- Errors -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="mb-4 sm:mb-6 flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                    <span class="font-bold">
                        Please fix the following errors:
                    </span>

                    <ul class="list-disc pl-5 space-y-0.5">
                        <li v-for="(error, key) in form.errors" :key="key">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit"
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 sm:p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <!-- Patient -->
                        <div>
                            <label for="patient_id"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Patient
                            </label>

                            <select v-model="form.patient_id" id="patient_id" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700">
                                <option value="">
                                    Select Patient
                                </option>

                                <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                                    {{ patient.name }}
                                </option>
                            </select>

                            <div v-if="form.errors.patient_id" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                {{ form.errors.patient_id }}
                            </div>
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department_id"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Department
                            </label>

                            <select v-model="form.department_id" @change="selectDepartment" id="department_id" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700">
                                <option value="">
                                    Select Department
                                </option>

                                <option v-for="department in departments" :key="department.id" :value="department.id">
                                    {{ department.name }}
                                </option>
                            </select>

                            <div v-if="form.errors.department_id" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                {{ form.errors.department_id }}
                            </div>
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="appointment_date"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Appointment Date
                            </label>

                            <input type="date" v-model="appointmentDate" @change="selectDate" id="appointment_date"
                                required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700" />

                            <div v-if="form.errors.appointment_date"
                                class="text-rose-600 text-[11px] mt-1 font-semibold">
                                {{ form.errors.appointment_date }}
                            </div>
                        </div>

                        <!-- Doctor -->
                        <div>
                            <label for="doctor_id"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Doctor
                            </label>

                            <select v-model="form.doctor_id" @change="selectDoctor" id="doctor_id" required
                                :disabled="!form.department_id || !appointmentDate"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 disabled:opacity-60">
                                <option value="">
                                    Select Doctor
                                </option>

                                <option v-for="doctor in filteredDoctors()" :key="doctor.id" :value="doctor.id">
                                    Dr. {{ doctor.name }}
                                </option>
                            </select>

                            <div v-if="form.department_id && appointmentDate && filteredDoctors().length === 0"
                                class="text-rose-600 text-[11px] mt-1 font-semibold">
                                No doctor is available in this department on this day.
                            </div>

                            <div v-if="form.errors.doctor_id" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                {{ form.errors.doctor_id }}
                            </div>
                        </div>

                        <!-- Time Slots -->
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Available Time Slots
                            </label>

                            <div v-if="loadingSlots"
                                class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-xl p-3">
                                Loading available slots...
                            </div>

                            <div v-else-if="availableSlots.length > 0" class="flex flex-wrap gap-2">
                                <button v-for="slot in availableSlots" :key="slot" type="button"
                                    @click="selectSlot(slot)" :class="[
                                        currentTime === slot
                                            ? 'bg-orange-500 text-white border-orange-500'
                                            : 'bg-orange-50 text-orange-600 border-orange-200 hover:bg-orange-500 hover:text-white',
                                        'px-4 py-2 rounded-lg border text-xs font-bold transition'
                                    ]">
                                    {{ formatSlot(slot) }}
                                </button>
                            </div>

                            <div v-else-if="form.doctor_id && appointmentDate && !loadingSlots"
                                class="text-xs text-rose-600 bg-rose-50 border border-rose-200 rounded-xl p-3 font-semibold">
                                No available time slots for this doctor.
                            </div>

                            <div v-else class="text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-xl p-3">
                                Select department, date and doctor to see available time slots.
                            </div>

                            <div v-if="form.appointment_date" class="mt-3 text-xs text-gray-600">
                                Selected Appointment:

                                <span class="font-bold text-orange-600">
                                    {{ appointmentDate }}
                                    {{ formatSlot(currentTime) }}
                                </span>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status"
                                class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Status
                            </label>

                            <select v-model="form.status" id="status" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700">
                                <option value="Scheduled">Scheduled</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div
                        class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <Link :href="route('appointments.index')"
                            class="w-full sm:w-auto text-center text-xs font-bold text-gray-500 px-4 py-2.5 rounded-xl">
                            Cancel
                        </Link>

                        <button type="submit" :disabled="form.processing || !form.appointment_date"
                            class="w-full sm:w-auto bg-orange-500 text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-600 transition disabled:opacity-50">
                            <span v-if="form.processing">
                                Updating...
                            </span>

                            <span v-else>
                                Update Appointment
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>