<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';

const props = defineProps({
    departments: { type: Array, default: () => [] },
    doctors: { type: Array, default: () => [] },
    preselect: { type: Object, default: () => ({}) },
});

const steps = ['Department', 'Doctor', 'Date & Time', 'Patient Info', 'Confirm'];
const step = ref(1);

const form = useForm({
    department_id: props.preselect?.department_id || '',
    doctor_id: props.preselect?.doctor_id || '',
    appointment_date: '',
    appointment_time: '',
    patient_name: '',
    patient_phone: '',
    notes: '',
});

onMounted(() => {
    if (form.doctor_id) {
        const doc = props.doctors.find((d) => String(d.id) === String(form.doctor_id));
        if (doc) form.department_id = doc.department_id;
    }
});

const filteredDoctors = computed(() => {
    if (!form.department_id) return props.doctors;
    return props.doctors.filter((d) => String(d.department_id) === String(form.department_id));
});

const selectedDoctor = computed(() => props.doctors.find((d) => String(d.id) === String(form.doctor_id)));
const selectedDepartment = computed(() => props.departments.find((d) => String(d.id) === String(form.department_id)));

const minDate = new Date().toISOString().split('T')[0];

const next = () => { if (step.value < 5) step.value++; };
const back = () => { if (step.value > 1) step.value--; };

const chooseDepartment = (id) => { form.department_id = id; form.doctor_id = ''; next(); };
const chooseDoctor = (id) => { form.doctor_id = id; next(); };

const canContinueDate = computed(() => form.appointment_date && form.appointment_time);
const canContinuePatient = computed(() => form.patient_name && form.patient_phone);

const submit = () => {
    form.post('/book-appointment', {
        onSuccess: () => {
            step.value = 1;
            form.reset();
        },
    });
};
</script>

<template>
    <PublicLayout>
        <PageHero title="Book Appointment" subtitle="Schedule Your Visit in Easy Steps" />

        <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div v-if="$page.props.flash?.success" class="mb-8 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-5 py-4">
                {{ $page.props.flash.success }}
            </div>

            <!-- Stepper -->
            <div class="flex items-center justify-between mb-10">
                <template v-for="(label, i) in steps" :key="label">
                    <div class="flex flex-col items-center flex-1">
                        <span :class="['w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold', step > i ? 'bg-brand-600 text-white' : step === i + 1 ? 'bg-brand-600 text-white ring-4 ring-brand-100' : 'bg-slate-100 text-slate-400']">
                            <Icon v-if="step > i + 1" name="check" class="w-4 h-4" />
                            <template v-else>{{ i + 1 }}</template>
                        </span>
                        <span class="hidden sm:block text-[11px] mt-1.5 font-semibold text-slate-400 text-center">{{ label }}</span>
                    </div>
                    <div v-if="i < steps.length - 1" :class="['h-0.5 flex-1 -mt-4 sm:-mt-5', step > i + 1 ? 'bg-brand-600' : 'bg-slate-100']"></div>
                </template>
            </div>

            <!-- Step 1: Department -->
            <div v-if="step === 1">
                <h2 class="font-heading font-bold text-lg text-slate-900 mb-5">Select Department</h2>
                <div class="grid sm:grid-cols-3 gap-3">
                    <button v-for="d in departments" :key="d.id" type="button" @click="chooseDepartment(d.id)"
                        :class="['p-4 rounded-xl border text-sm font-semibold text-left transition', String(form.department_id) === String(d.id) ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:border-brand-300']">
                        {{ d.name }}
                    </button>
                </div>
            </div>

            <!-- Step 2: Doctor -->
            <div v-else-if="step === 2">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-heading font-bold text-lg text-slate-900">Select Doctor</h2>
                    <button type="button" @click="back" class="text-xs font-bold text-brand-600">&larr; Back</button>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <button v-for="d in filteredDoctors" :key="d.id" type="button" @click="chooseDoctor(d.id)"
                        :class="['p-4 rounded-xl border flex items-center gap-3 text-left transition', String(form.doctor_id) === String(d.id) ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:border-brand-300']">
                        <Avatar :name="d.name" :photo-url="d.photo_url" size="sm" />
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">Dr. {{ d.name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ d.specialization }}</p>
                        </div>
                    </button>
                </div>
                <p v-if="!filteredDoctors.length" class="text-sm text-slate-400">No doctors available in this department yet.</p>
            </div>

            <!-- Step 3: Date & Time -->
            <div v-else-if="step === 3">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-heading font-bold text-lg text-slate-900">Choose Date &amp; Time</h2>
                    <button type="button" @click="back" class="text-xs font-bold text-brand-600">&larr; Back</button>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Date</label>
                        <input v-model="form.appointment_date" type="date" :min="minDate"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Time</label>
                        <input v-model="form.appointment_time" type="time"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                    </div>
                </div>
                <button type="button" @click="next" :disabled="!canContinueDate"
                    class="mt-6 w-full bg-brand-600 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold py-3 rounded-xl hover:bg-brand-700 transition">
                    Continue
                </button>
            </div>

            <!-- Step 4: Patient Info -->
            <div v-else-if="step === 4">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-heading font-bold text-lg text-slate-900">Patient Information</h2>
                    <button type="button" @click="back" class="text-xs font-bold text-brand-600">&larr; Back</button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Full Name</label>
                        <input v-model="form.patient_name" type="text" placeholder="Your full name"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Phone Number</label>
                        <input v-model="form.patient_phone" type="text" placeholder="03xx-xxxxxxx"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Notes (optional)</label>
                        <textarea v-model="form.notes" rows="3" placeholder="Briefly describe your concern"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400"></textarea>
                    </div>
                </div>
                <button type="button" @click="next" :disabled="!canContinuePatient"
                    class="mt-6 w-full bg-brand-600 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold py-3 rounded-xl hover:bg-brand-700 transition">
                    Continue
                </button>
            </div>

            <!-- Step 5: Confirm -->
            <div v-else>
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-heading font-bold text-lg text-slate-900">Confirm Appointment</h2>
                    <button type="button" @click="back" class="text-xs font-bold text-brand-600">&larr; Back</button>
                </div>
                <div class="rounded-2xl border border-slate-100 divide-y divide-slate-100 mb-6">
                    <div class="flex justify-between px-5 py-3.5 text-sm">
                        <span class="text-slate-400">Department</span>
                        <span class="font-semibold text-slate-800">{{ selectedDepartment?.name || '-' }}</span>
                    </div>
                    <div class="flex justify-between px-5 py-3.5 text-sm">
                        <span class="text-slate-400">Doctor</span>
                        <span class="font-semibold text-slate-800">Dr. {{ selectedDoctor?.name || '-' }}</span>
                    </div>
                    <div class="flex justify-between px-5 py-3.5 text-sm">
                        <span class="text-slate-400">Date &amp; Time</span>
                        <span class="font-semibold text-slate-800">{{ form.appointment_date }} at {{ form.appointment_time }}</span>
                    </div>
                    <div class="flex justify-between px-5 py-3.5 text-sm">
                        <span class="text-slate-400">Patient</span>
                        <span class="font-semibold text-slate-800">{{ form.patient_name }} &middot; {{ form.patient_phone }}</span>
                    </div>
                </div>
                <p v-if="form.errors.doctor_id || form.errors.appointment_date" class="text-sm text-rose-500 mb-4">
                    Please check the details above and try again.
                </p>
                <button type="button" @click="submit" :disabled="form.processing"
                    class="w-full bg-brand-600 text-white font-bold py-3.5 rounded-xl hover:bg-brand-700 transition disabled:opacity-60">
                    {{ form.processing ? 'Booking...' : 'Confirm Appointment' }}
                </button>
            </div>
        </section>
    </PublicLayout>
</template>