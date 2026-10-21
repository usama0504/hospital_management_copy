<script setup>
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';

defineProps({
    patient: Object,
    upcoming: Array,
    prescriptions: Array,
    stats: Object,
});

const formatDateTime = (v) => v
    ? new Date(v).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    : '-';
const formatDate = (v) => v
    ? new Date(v).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
    : '-';
const money = (v) => 'Rs. ' + Number(v || 0).toLocaleString();
</script>

<template>
    <PortalLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Welcome, {{ patient.name }}</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Your appointments, prescriptions and bills in one place.</p>
            </div>
            <Link :href="route('portal.appointments.create')"
                class="text-center text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 transition px-5 py-3 rounded-xl shadow-md shadow-orange-500/20">
                Book New Appointment
            </Link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Appointments</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ stats.appointments }}</p>
            </div>
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Prescriptions</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ stats.prescriptions }}</p>
            </div>
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Unpaid Bills</p>
                <p class="text-2xl font-black mt-1" :class="stats.unpaid > 0 ? 'text-rose-600' : 'text-emerald-600'">{{
                    money(stats.unpaid) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <section class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-gray-900">Upcoming Appointments</h3>
                    <Link :href="route('portal.appointments')" class="text-[11px] font-bold text-orange-600 hover:underline">View all</Link>
                </div>
                <p v-if="!upcoming.length" class="text-xs text-gray-500 font-medium">No upcoming appointments.</p>
                <ul v-else class="space-y-3">
                    <li v-for="a in upcoming" :key="a.id" class="border border-gray-100 rounded-xl px-4 py-3">
                        <p class="text-xs font-bold text-gray-900">{{ formatDateTime(a.appointment_date) }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                            Dr. {{ a.doctor?.name ?? 'N/A' }}
                            <span v-if="a.doctor?.department"> &middot; {{ a.doctor.department.name }}</span>
                        </p>
                        <span class="inline-block mt-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">{{ a.status }}</span>
                    </li>
                </ul>
            </section>

            <section class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-gray-900">Recent Prescriptions</h3>
                    <Link :href="route('portal.prescriptions')" class="text-[11px] font-bold text-orange-600 hover:underline">View all</Link>
                </div>
                <p v-if="!prescriptions.length" class="text-xs text-gray-500 font-medium">No prescriptions yet.</p>
                <ul v-else class="space-y-3">
                    <li v-for="p in prescriptions" :key="p.id" class="border border-gray-100 rounded-xl px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 break-words">{{ p.diagnosis }}</p>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">
                                    {{ formatDate(p.prescribed_date) }} &middot; Dr. {{ p.doctor?.name ?? 'N/A' }}
                                </p>
                            </div>
                            <Link :href="route('portal.prescriptions.show', p.id)"
                                class="shrink-0 text-[11px] font-bold text-orange-600 hover:underline">Open</Link>
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </PortalLayout>
</template>
