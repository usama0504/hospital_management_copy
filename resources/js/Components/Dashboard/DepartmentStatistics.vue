<script setup>
import { computed } from 'vue';

const props = defineProps({
    departmentStatistics: {
        type: Array,
        default: () => [],
    },
});

const totalAppointments = computed(() => {
    return props.departmentStatistics.reduce(
        (total, department) => total + Number(department.appointments_count || 0),
        0
    );
});

const getPercentage = (count) => {
    if (!totalAppointments.value) return 0;
    return Math.round((Number(count) / totalAppointments.value) * 100);
};

// Har department ke liye ek gradient color-pair (orange theme ke andar variation)
const palette = [
    { from: '#f97316', to: '#fb923c', soft: '#fff7ed', text: '#c2410c', bar: '#fdba74' },
    { from: '#f59e0b', to: '#fbbf24', soft: '#fffbeb', text: '#b45309', bar: '#fcd34d' },
    { from: '#ef4444', to: '#f87171', soft: '#fef2f2', text: '#b91c1c', bar: '#fca5a5' },
    { from: '#10b981', to: '#34d399', soft: '#ecfdf5', text: '#047857', bar: '#6ee7b7' },
    { from: '#6366f1', to: '#818cf8', soft: '#eef2ff', text: '#4338ca', bar: '#a5b4fc' },
    { from: '#14b8a6', to: '#2dd4bf', soft: '#f0fdfa', text: '#0f766e', bar: '#5eead4' },
];

// Backend already appointments_count ke hisaab se descending sorted bhejta hai
const departmentsWithStyle = computed(() => {
    return props.departmentStatistics.map((department, index) => {
        const pct = getPercentage(department.appointments_count);
        const color = palette[index % palette.length];

        const radius = 26;
        const circumference = 2 * Math.PI * radius;
        const dash = (pct / 100) * circumference;

        // Doctor-wise mini bar chart ke liye: har doctor ka bar us department
        // ke sabse busy doctor ke against relative hoga (0-100%)
        const doctors = department.doctors ?? [];
        const maxDoctorCount = Math.max(1, ...doctors.map(d => Number(d.appointments_count || 0)));
        const doctorsWithBars = doctors.map(doctor => ({
            ...doctor,
            barPct: Math.round((Number(doctor.appointments_count || 0) / maxDoctorCount) * 100),
        }));

        return {
            ...department,
            pct,
            color,
            radius,
            circumference,
            dash,
            doctorsWithBars,
        };
    });
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header -->
        <div class="relative px-5 sm:px-6 py-5 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-orange-50 via-white to-white"></div>
            <div class="relative flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white flex items-center justify-center shrink-0 shadow-md shadow-orange-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h6m-6 4h6m-6 4h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Department Performance</h3>
                        <p class="text-[11px] text-gray-500 font-medium">Doctor-wise breakdown within every department
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-white border border-gray-100 shadow-sm rounded-xl px-4 py-2">
                    <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Total Visits</span>
                    <span class="text-lg font-black text-gray-900 leading-none">{{ totalAppointments }}</span>

                </div>
            </div>
        </div>

        <!-- Department Cards (each with its own doctor performance chart) -->
        <div v-if="departmentsWithStyle.length" class="p-5 sm:p-6 pt-0 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div v-for="department in departmentsWithStyle" :key="department.name"
                class="rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">

                <!-- Department Header Row -->
                <div class="flex items-center gap-3 p-4" :style="{ backgroundColor: department.color.soft }">
                    <!-- Department share ring -->
                    <div class="relative w-14 h-14 shrink-0">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 64 64">
                            <circle cx="32" cy="32" :r="department.radius" fill="none" stroke="#ffffff"
                                stroke-width="6" />
                            <circle cx="32" cy="32" :r="department.radius" fill="none" :stroke="department.color.from"
                                stroke-width="6" stroke-linecap="round"
                                :stroke-dasharray="`${department.dash} ${department.circumference}`" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-[11px] font-black" :style="{ color: department.color.text }">{{
                                department.pct }}%</span>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-black text-gray-900 truncate">{{ department.name }}</p>
                        <p class="text-[11px] text-gray-500 font-medium">{{ department.doctors_count }} doctors · {{
                            department.appointments_count }} appointments</p>
                    </div>
                </div>

                <!-- Doctor-wise Performance Chart -->
                <div class="p-4 space-y-3">
                    <template v-if="department.doctorsWithBars.length > 0">
                        <div v-for="doctor in department.doctorsWithBars" :key="doctor.id"
                            class="flex items-center gap-3">
                            <span class="text-[11px] font-semibold text-gray-600 w-24 sm:w-28 truncate shrink-0"
                                :title="doctor.name">
                                Dr. {{ doctor.name }}
                            </span>
                            <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 ease-out"
                                    :style="{ width: doctor.barPct + '%', backgroundColor: department.color.from }">
                                </div>
                            </div>
                            <span class="text-[11px] font-bold text-gray-800 w-6 text-right shrink-0">{{
                                doctor.appointments_count }}</span>
                        </div>
                    </template>
                    <p v-else class="text-[11px] text-gray-400 text-center py-2">No doctors assigned to this department
                        yet.</p>
                </div>
            </div>
        </div>

        <!-- Empty -->
        <div v-else class="py-14 text-center text-gray-400 text-sm">
            No department data available yet.
        </div>
    </div>
</template>