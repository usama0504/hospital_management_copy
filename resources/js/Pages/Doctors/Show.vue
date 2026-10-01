<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    doctor: Object,
    stats: Object,
    recentAppointments: Array,
    showAppointments: Boolean,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});

const isAdmin = computed(() => {
    const roles = authUser.value.roles || [];
    return roles.some((r) => (typeof r === 'string' ? r : r?.name) === 'admin');
});

const formatDateTime = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
};

const formatTime = (t) => (t ? String(t).slice(0, 5) : '-');

const availabilities = computed(() => props.doctor?.availabilities ?? []);

const statusClass = (status) => {
    switch ((status || '').toLowerCase()) {
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
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Doctor Details</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Complete profile and schedule.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link v-if="isAdmin" :href="route('doctors.edit', doctor.id)"
                            class="text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 transition px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/20">
                            Edit Doctor
                        </Link>
                        <Link :href="route('doctors.index')"
                            class="text-xs font-bold text-gray-600 hover:text-gray-900 transition bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                            &larr; Back to Doctors
                        </Link>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 sm:p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <img v-if="doctor.photo_url" :src="doctor.photo_url" :alt="doctor.name"
                            class="w-14 h-14 rounded-2xl object-cover border border-gray-100" />
                        <div v-else
                            class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl font-black">
                            {{ doctor.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-black text-gray-900 break-words">Dr. {{ doctor.name }}</h3>
                            <p class="text-xs text-gray-500 font-medium">{{ doctor.specialization }}</p>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5 text-xs">
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Email</dt>
                            <dd class="mt-1 font-semibold text-gray-900 break-all">{{ doctor.email }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Phone</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ doctor.phone ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Gender</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ doctor.gender ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Specialization
                            </dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ doctor.specialization }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Department</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ doctor.department?.name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Consultation Fee
                            </dt>
                            <dd class="mt-1 font-semibold text-gray-900">
                                {{ doctor.consultation_fee ? 'Rs. ' + Number(doctor.consultation_fee).toLocaleString()
                                    : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4">
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Appointments</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ stats.total_appointments }}</p>
                    </div>
                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4">
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Unique Patients</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ stats.total_patients }}</p>
                    </div>
                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4">
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Upcoming</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ stats.upcoming_appointments }}</p>
                    </div>
                </div>

                <!-- Weekly Availability -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900">Weekly Availability</h3>
                    </div>
                    <div v-if="availabilities.length === 0" class="py-10 text-center text-gray-400 font-medium text-xs">
                        No availability set.
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-6">
                        <div v-for="slot in availabilities" :key="slot.id"
                            class="flex items-center justify-between border border-gray-100 rounded-xl px-4 py-3 text-xs">
                            <span class="font-bold text-gray-900">{{ slot.day_of_week }}</span>
                            <span class="font-medium text-gray-600">
                                {{ formatTime(slot.start_time) }} - {{ formatTime(slot.end_time) }}
                            </span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                :class="slot.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200'">
                                {{ slot.is_active ? 'Active' : 'Off' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div v-if="showAppointments"
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900">Recent Appointments</h3>
                    </div>
                    <div v-if="!recentAppointments || recentAppointments.length === 0"
                        class="py-10 text-center text-gray-400 font-medium text-xs">
                        No appointments found.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-6">Date &amp; Time</th>
                                    <th class="py-3 px-6">Patient</th>
                                    <th class="py-3 px-6">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <tr v-for="a in recentAppointments" :key="a.id" class="hover:bg-gray-50/50 transition">
                                    <td class="py-3.5 px-6 whitespace-nowrap">{{ formatDateTime(a.appointment_date) }}
                                    </td>
                                    <td class="py-3.5 px-6 font-bold text-gray-900">{{ a.patient?.name ?? '-' }}</td>
                                    <td class="py-3.5 px-6">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                            :class="statusClass(a.status)">{{ a.status }}</span>
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