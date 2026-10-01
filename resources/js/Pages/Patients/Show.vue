<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    patient: Object,
    canManagePatients: Boolean,
    canViewBills: Boolean,
});

const formatDate = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

const formatDateTime = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
};

const age = computed(() => {
    if (!props.patient?.dob) return null;
    const dob = new Date(props.patient.dob);
    const now = new Date();
    let years = now.getFullYear() - dob.getFullYear();
    const m = now.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) years--;
    return years;
});

const appointments = computed(() => props.patient?.appointments ?? []);
const prescriptions = computed(() => props.patient?.prescriptions ?? []);
const bills = computed(() => props.patient?.bills ?? []);

const totalBilled = computed(() => bills.value.reduce((sum, b) => sum + Number(b.amount || 0), 0));
const totalUnpaid = computed(() =>
    bills.value.filter((b) => (b.status || '').toLowerCase() !== 'paid')
        .reduce((sum, b) => sum + Number(b.amount || 0), 0)
);

const money = (v) => 'Rs. ' + Number(v || 0).toLocaleString();

const statusClass = (status) => {
    switch ((status || '').toLowerCase()) {
        case 'paid':
        case 'completed':
        case 'approved':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'cancelled':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-amber-50 text-amber-700 border-amber-200';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Patient Details</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Complete profile and medical history.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link v-if="canManagePatients" :href="route('patients.edit', patient.id)"
                            class="text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 transition px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/20">
                            Edit Patient
                        </Link>
                        <Link :href="route('patients.index')"
                            class="text-xs font-bold text-gray-600 hover:text-gray-900 transition bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                            &larr; Back to Patients
                        </Link>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 sm:p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl font-black">
                            {{ patient.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-black text-gray-900 break-words">{{ patient.name }}</h3>
                            <p class="text-xs text-gray-500 font-medium">Patient ID: #{{ patient.id }}</p>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5 text-xs">
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Email</dt>
                            <dd class="mt-1 font-semibold text-gray-900 break-all">{{ patient.email }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Phone</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ patient.phone ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Gender</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ patient.gender ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Date of Birth</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ formatDate(patient.dob) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Age</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ age !== null ? age + ' years' : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Registered On
                            </dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ formatDate(patient.created_at) }}</dd>
                        </div>
                        <div class="sm:col-span-2 lg:col-span-3">
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Address</dt>
                            <dd class="mt-1 font-semibold text-gray-900 whitespace-pre-line">{{ patient.address ||
                                'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4">
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Appointments</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ appointments.length }}</p>
                    </div>
                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4">
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Prescriptions</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ prescriptions.length }}</p>
                    </div>
                    <template v-if="canViewBills">
                        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4">
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Billed</p>
                            <p class="text-2xl font-black text-gray-900 mt-1">{{ money(totalBilled) }}</p>
                        </div>
                        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4">
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Unpaid Balance</p>
                            <p class="text-2xl font-black text-rose-600 mt-1">{{ money(totalUnpaid) }}</p>
                        </div>
                    </template>
                </div>

                <!-- Appointments -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900">Appointment History</h3>
                    </div>
                    <div v-if="appointments.length === 0" class="py-10 text-center text-gray-400 font-medium text-xs">
                        No appointments found.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-6">Date &amp; Time</th>
                                    <th class="py-3 px-6">Doctor</th>
                                    <th class="py-3 px-6">Department</th>
                                    <th class="py-3 px-6">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <tr v-for="a in appointments" :key="a.id" class="hover:bg-gray-50/50 transition">
                                    <td class="py-3.5 px-6 whitespace-nowrap">{{ formatDateTime(a.appointment_date) }}
                                    </td>
                                    <td class="py-3.5 px-6 font-bold text-gray-900">
                                        {{ a.doctor ? 'Dr. ' + a.doctor.name : '-' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-gray-500">{{ a.doctor?.department?.name ?? '-' }}</td>
                                    <td class="py-3.5 px-6">
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                            :class="statusClass(a.status)">{{ a.status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Prescriptions -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900">Prescriptions</h3>
                    </div>
                    <div v-if="prescriptions.length === 0" class="py-10 text-center text-gray-400 font-medium text-xs">
                        No prescriptions found.
                    </div>
                    <div v-else class="divide-y divide-gray-100">
                        <div v-for="p in prescriptions" :key="p.id" class="p-6 space-y-3">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="text-xs font-black text-gray-900">
                                        {{ formatDate(p.prescribed_date) }}
                                        <span class="text-gray-500 font-medium">
                                            &middot; {{ p.doctor ? 'Dr. ' + p.doctor.name : '-' }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1"><span
                                            class="font-bold text-gray-800">Diagnosis:</span> {{ p.diagnosis }}</p>
                                    <p v-if="p.notes" class="text-xs text-gray-600 mt-0.5"><span
                                            class="font-bold text-gray-800">Notes:</span> {{ p.notes }}</p>
                                </div>
                                <Link :href="route('prescriptions.show', p.id)"
                                    class="text-[11px] font-bold text-orange-600 hover:text-orange-700 transition">
                                    Open Prescription &rarr;
                                </Link>
                            </div>

                            <div v-if="p.items && p.items.length" class="overflow-x-auto">
                                <table class="min-w-full text-left text-xs">
                                    <thead class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        <tr>
                                            <th class="py-2 pr-4">Medicine</th>
                                            <th class="py-2 pr-4">Dosage</th>
                                            <th class="py-2 pr-4">Frequency</th>
                                            <th class="py-2 pr-4">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 font-medium text-gray-700">
                                        <tr v-for="item in p.items" :key="item.id">
                                            <td class="py-2 pr-4 font-bold text-gray-900">{{ item.medicine_name }}</td>
                                            <td class="py-2 pr-4">{{ item.dosage ?? '-' }}</td>
                                            <td class="py-2 pr-4">{{ item.frequency ?? '-' }}</td>
                                            <td class="py-2 pr-4">{{ item.duration ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bills (doctor ko nahi dikhte) -->
                <div v-if="canViewBills" class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900">Billing History</h3>
                    </div>
                    <div v-if="bills.length === 0" class="py-10 text-center text-gray-400 font-medium text-xs">
                        No bills found.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-6">Date</th>
                                    <th class="py-3 px-6">Doctor</th>
                                    <th class="py-3 px-6">Amount</th>
                                    <th class="py-3 px-6">Status</th>
                                    <th class="py-3 px-6 text-right">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <tr v-for="b in bills" :key="b.id" class="hover:bg-gray-50/50 transition">
                                    <td class="py-3.5 px-6 whitespace-nowrap">{{ formatDate(b.bill_date) }}</td>
                                    <td class="py-3.5 px-6">{{ b.doctor ? 'Dr. ' + b.doctor.name : '-' }}</td>
                                    <td class="py-3.5 px-6 font-bold text-gray-900">{{ money(b.amount) }}</td>
                                    <td class="py-3.5 px-6">
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                            :class="statusClass(b.status)">{{ b.status }}</span>
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <Link :href="route('bills.receipt', b.id)"
                                            class="text-[11px] font-bold text-orange-600 hover:text-orange-700 transition">
                                            View &rarr;
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
