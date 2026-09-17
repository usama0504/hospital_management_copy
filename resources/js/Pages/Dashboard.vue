<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Sub-components import karein
import ActivityOverviewCards from '@/Components/Dashboard/ActivityOverviewCards.vue';
import TrendChartCard from '@/Components/Dashboard/TrendChartCard.vue';
import RecentAppointmentsTable from '@/Components/Dashboard/RecentAppointmentsTable.vue';
import RecentBillingTable from '@/Components/Dashboard/RecentBillingTable.vue';

import { Line } from 'vue-chartjs';
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
            label: 'Revenue ($)',
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
                    <!-- Department Visits (Aap isay bhi alag component bana sakte hain) -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4">Patient Visit By Department</h3>
                        <div class="flex items-center justify-center py-6">
                            <div
                                class="w-36 h-36 rounded-full border-[14px] border-indigo-600 border-t-rose-400 border-r-amber-400 flex items-center justify-center text-xs font-bold text-gray-600 shadow-inner">
                                Visits
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
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4">Average Patient Visits</h3>
                        <div
                            class="h-44 bg-gray-50/50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center text-gray-400 text-xs">
                            [ Bar Chart Widget Preview ]
                        </div>
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