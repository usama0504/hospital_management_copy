<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PortalLayout from '@/Layouts/PortalLayout.vue';

const props = defineProps({
    departments: { type: Array, default: () => [] },
    doctors: { type: Array, default: () => [] },
});

const form = useForm({
    department_id: '',
    doctor_id: '',
    appointment_date: '',
    appointment_time: '',
    notes: '',
});

const filteredDoctors = computed(() =>
    props.doctors.filter((d) => String(d.department_id) === String(form.department_id))
);
const selectedDoctor = computed(() =>
    props.doctors.find((d) => String(d.id) === String(form.doctor_id))
);

const minDate = new Date().toISOString().split('T')[0];

const availableSlots = ref([]);
const slotsLoading = ref(false);
const slotsError = ref('');
let requestId = 0;

const fetchSlots = async () => {
    if (!form.doctor_id || !form.appointment_date) {
        availableSlots.value = [];
        return;
    }
    const id = ++requestId;
    slotsLoading.value = true;
    slotsError.value = '';
    try {
        const { data } = await axios.get('/book-appointment/slots', {
            params: { doctor_id: form.doctor_id, date: form.appointment_date },
        });
        if (id === requestId) availableSlots.value = data.slots;
    } catch (e) {
        if (id === requestId) {
            availableSlots.value = [];
            slotsError.value = 'Could not load times. Please try again.';
        }
    } finally {
        if (id === requestId) slotsLoading.value = false;
    }
};

// Department badalne par doctor reset; doctor/date badalne par slots dobara.
watch(() => form.department_id, () => { form.doctor_id = ''; });
watch(() => [form.doctor_id, form.appointment_date], () => {
    form.appointment_time = '';
    fetchSlots();
});

const formatSlot = (t) => {
    const [h, m] = t.split(':');
    const hour = parseInt(h, 10);
    return `${hour % 12 === 0 ? 12 : hour % 12}:${m} ${hour >= 12 ? 'PM' : 'AM'}`;
};

const money = (v) => 'Rs. ' + Number(v || 0).toLocaleString();

const canSubmit = computed(() => form.department_id && form.doctor_id && form.appointment_date && form.appointment_time);

const submit = () => form.post(route('portal.appointments.store'));

const inputClass = 'w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 outline-none transition';
</script>

<template>
    <PortalLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Book an Appointment</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Choose a doctor and an available time.</p>
            </div>
            <Link :href="route('portal.appointments')"
                class="text-center text-xs font-bold text-gray-600 hover:text-gray-900 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                &larr; My Appointments
            </Link>
        </div>

        <form @submit.prevent="submit" class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 sm:p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Department</label>
                    <select v-model="form.department_id" :class="inputClass">
                        <option value="" disabled>Select department</option>
                        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                    <p v-if="form.errors.department_id" class="text-xs text-rose-600 font-semibold mt-1">{{ form.errors.department_id }}</p>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Doctor</label>
                    <select v-model="form.doctor_id" :disabled="!form.department_id" :class="[inputClass, !form.department_id && 'opacity-60']">
                        <option value="" disabled>{{ form.department_id ? 'Select doctor' : 'Choose a department first' }}</option>
                        <option v-for="d in filteredDoctors" :key="d.id" :value="d.id">Dr. {{ d.name }} ({{ d.specialization }})</option>
                    </select>
                    <p v-if="form.errors.doctor_id" class="text-xs text-rose-600 font-semibold mt-1">{{ form.errors.doctor_id }}</p>
                    <p v-if="selectedDoctor" class="text-xs text-gray-500 font-medium mt-1.5">
                        Consultation fee: <span class="font-bold text-gray-800">{{ money(selectedDoctor.consultation_fee) }}</span>
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Date</label>
                <input type="date" v-model="form.appointment_date" :min="minDate" :disabled="!form.doctor_id"
                    :class="[inputClass, 'sm:max-w-xs', !form.doctor_id && 'opacity-60']" />
                <p v-if="form.errors.appointment_date" class="text-xs text-rose-600 font-semibold mt-1">{{ form.errors.appointment_date }}</p>
            </div>

            <div v-if="form.doctor_id && form.appointment_date">
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Available Times</label>
                <p v-if="slotsLoading" class="text-xs text-gray-500 font-medium">Loading times...</p>
                <p v-else-if="slotsError" class="text-xs text-rose-600 font-semibold">{{ slotsError }}</p>
                <p v-else-if="!availableSlots.length" class="text-xs text-gray-500 font-medium">No times available on this day. Try another date.</p>
                <div v-else class="flex flex-wrap gap-2">
                    <button v-for="t in availableSlots" :key="t" type="button" @click="form.appointment_time = t"
                        :class="form.appointment_time === t ? 'bg-orange-500 text-white border-orange-500 shadow-md shadow-orange-500/20' : 'bg-white text-gray-700 border-gray-200 hover:border-orange-300'"
                        class="px-4 py-2 rounded-xl border text-xs font-bold transition">
                        {{ formatSlot(t) }}
                    </button>
                </div>
                <p v-if="form.errors.appointment_time" class="text-xs text-rose-600 font-semibold mt-2">{{ form.errors.appointment_time }}</p>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Notes (optional)</label>
                <textarea v-model="form.notes" rows="3" maxlength="1000" placeholder="Anything the doctor should know"
                    :class="inputClass"></textarea>
            </div>

            <button type="submit" :disabled="!canSubmit || form.processing"
                class="w-full sm:w-auto text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed transition px-8 py-3 rounded-xl shadow-md shadow-orange-500/20">
                {{ form.processing ? 'Booking...' : 'Request Appointment' }}
            </button>
        </form>
    </PortalLayout>
</template>
