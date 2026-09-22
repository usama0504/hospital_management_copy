<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Sub-components import karein
import ActivityOverviewCards from '@/Components/Dashboard/ActivityOverviewCards.vue';
import TrendChartCard from '@/Components/Dashboard/TrendChartCard.vue';
import RecentAppointmentsTable from '@/Components/Dashboard/RecentAppointmentsTable.vue';
import RecentBillingTable from '@/Components/Dashboard/RecentBillingTable.vue';

import { Line, Doughnut, Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Tooltip, Legend, Filler);

const props = defineProps({
    auth: Object,
    appointmentsCount: Number,
    operationsCount: Number,
    patientsCount: Number,
    totalEarnings: [Number, String],
    popularDoctors: Array,
    recentAppointments: Array,
    recentBills: Array,
    trendLabels: Array,
    appointmentsTrend: Array,
    revenueTrend: Array,
    statusBreakdown: Object,
    doctorLoad: Array,
});

// Role check computed
const isDoctor = computed(() => {
    const user = props.auth?.user;
    if (!user) return false;
    if (typeof user.role === 'string') return user.role.toLowerCase() === 'doctor';
    if (Array.isArray(user.roles)) {
        return user.roles.some(r => (typeof r === 'string' && r.toLowerCase() === 'doctor') || (r?.name?.toLowerCase() === 'doctor'));
    }
    return user.role?.name?.toLowerCase() === 'doctor';
});

// Line Chart Data
const trendChartData = computed(() => ({
    labels: props.trendLabels ?? [],
    datasets: [
        {
            label: 'Appointments',
            data: props.appointmentsTrend ?? [],
            borderColor: '#f97316',
            backgroundColor: 'rgba(249,115,22,0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y',
        },
        {
            label: 'Revenue (Rs.)',
            data: props.revenueTrend ?? [],
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,0.08)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1',
        },
    ],
}));

const trendChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11, weight: 'bold' } } } },
    scales: {
        y: { type: 'linear', position: 'left', beginAtZero: true, ticks: { precision: 0 } },
        y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } },
    },
};

// NEW: Doughnut Chart — Appointment Status Breakdown (pehle ye "Patient Visit By
// Department" ka fake hardcoded circle tha, ab real data se bana hai)
const statusColorMap = { Scheduled: '#f59e0b', Completed: '#10b981', Cancelled: '#f43f5e' };
const statusChartData = computed(() => {
    const breakdown = props.statusBreakdown ?? {};
    const labels = Object.keys(breakdown);
    return {
        labels,
        datasets: [{
            data: Object.values(breakdown),
            backgroundColor: labels.map(l => statusColorMap[l] ?? '#6b7280'),
            borderWidth: 0,
        }],
    };
});
const statusChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '68%',
    plugins: { legend: { display: false } },
};

// NEW: Bar Chart — Doctor-wise appointment load, top 5 (pehle ye
// "[ Bar Chart Widget Preview ]" placeholder tha, ab real data se bana hai)
const doctorLoadChartData = computed(() => ({
    labels: (props.doctorLoad ?? []).map(d => 'Dr. ' + d.doctor),
    datasets: [{
        label: 'Appointments',
        data: (props.doctorLoad ?? []).map(d => d.total),
        backgroundColor: '#f97316',
        borderRadius: 6,
        maxBarThickness: 28,
    }],
}));
const doctorLoadChartOptions = {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
};

// Formatters
const formatCurrency = (value) => Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const formatDate = (dateString) => dateString ? new Date(dateString).toLocaleDateString('en-GB') : 'N/A';
const formatTime = (dateString) => dateString ? new Date(dateString).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : 'N/A';
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">

            <!-- Top Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <!-- Activity Overview Component -->
                    <ActivityOverviewCards :appointmentsCount="appointmentsCount" :operationsCount="operationsCount"
                        :patientsCount="patientsCount" :totalEarnings="totalEarnings" :isDoctor="isDoctor"
                        :formatCurrency="formatCurrency" />

                    <!-- Trend Chart Component -->
                    <TrendChartCard :trendChartData="trendChartData" :trendChartOptions="trendChartOptions" />
                </div>

                <!-- Right Sidebar Column -->
                <div class="space-y-6">
                    <!-- Appointment Status Breakdown (real Doughnut chart) -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4">Appointment Status Breakdown</h3>
                        <div class="flex items-center justify-center py-4">
                            <div class="w-36 h-36">
                                <Doughnut :data="statusChartData" :options="statusChartOptions" />
                            </div>
                        </div>
                        <div class="space-y-2.5 pt-2 text-xs font-semibold">
                            <div v-for="(count, status) in statusBreakdown" :key="status"
                                class="flex items-center justify-between text-gray-600">
                                <span class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full"
                                        :style="{ backgroundColor: statusColorMap[status] ?? '#6b7280' }"></span>
                                    {{ status }}
                                </span>
                                <span class="text-gray-900 font-bold">{{ count }}</span>
                            </div>
                            <div v-if="!statusBreakdown || Object.keys(statusBreakdown).length === 0"
                                class="text-center text-gray-400 py-2">
                                No appointment data yet.
                            </div>
                        </div>
                    </div>

                    <!-- Popular Doctors -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-800">Popular Doctor List</h3>
                            <Link :href="route('doctors.index')"
                                class="text-xs text-orange-500 font-semibold hover:underline">View All</Link>
                        </div>
                        <div class="divide-y divide-gray-100 text-sm">
                            <template v-if="popularDoctors && popularDoctors.length > 0">
                                <div v-for="doctor in popularDoctors" :key="doctor.id"
                                    class="px-6 py-3.5 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                            {{ doctor.name.substring(0, 2).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-xs">Dr. {{ doctor.name }}</p>
                                            <p class="text-[11px] text-gray-400">{{ doctor.specialization ||
                                                'Specialist' }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Available</span>
                                </div>
                            </template>
                            <div v-else class="p-6 text-center text-gray-400 text-xs">No doctors found in database.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Section: Recent Appointments -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2">
                    <RecentAppointmentsTable :recentAppointments="recentAppointments" :formatDate="formatDate"
                        :formatTime="formatTime" />
                </div>
                <div class="space-y-6">
                    <!-- Doctor-wise Appointment Load (real Bar chart) -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4">Doctor-wise Appointment Load</h3>
                        <div class="h-44">
                            <Bar :data="doctorLoadChartData" :options="doctorLoadChartOptions" />
                        </div>
                        <p v-if="!doctorLoad || doctorLoad.length === 0" class="text-center text-gray-400 text-xs mt-2">
                            No
                            appointment data yet.</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Billing -->
            <div v-if="!isDoctor" class="grid grid-cols-1 gap-6">
                <RecentBillingTable :recentBills="recentBills" :totalEarnings="totalEarnings"
                    :formatCurrency="formatCurrency" />
            </div>

        </div>
    </AuthenticatedLayout>
</template>