<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';

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

    return Math.round(
        (Number(count) / totalAppointments.value) * 100
    );
};

const chartData = computed(() => ({
    labels: props.departmentStatistics.map(department =>
        department.name.length > 12
            ? department.name.substring(0, 12) + '...'
            : department.name
    ),
    datasets: [
        {
            label: 'Appointments',
            data: props.departmentStatistics.map(
                department => Number(department.appointments_count || 0)
            ),
            backgroundColor: '#4f46e5',
            borderRadius: 8,
            borderSkipped: false,
            maxBarThickness: 42,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,

    plugins: {
        legend: {
            display: false,
        },

        tooltip: {
            backgroundColor: '#1f2937',
            padding: 10,
            displayColors: false,

            callbacks: {
                label: (context) => {
                    return `${context.raw} Appointments`;
                },
            },
        },
    },

    scales: {
        x: {
            grid: {
                display: false,
            },

            border: {
                display: false,
            },

            ticks: {
                color: '#6b7280',
                font: {
                    size: 11,
                    weight: '500',
                },
            },
        },

        y: {
            beginAtZero: true,

            border: {
                display: false,
            },

            grid: {
                color: '#f1f3f8',
            },

            ticks: {
                color: '#9ca3af',
                precision: 0,
                font: {
                    size: 10,
                },
            },
        },
    },
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-800">
                    Department Analytics
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Appointment distribution across departments
                </p>
            </div>

            <div class="text-right">
                <p class="text-xl font-bold text-gray-800">
                    {{ totalAppointments }}
                </p>

                <p class="text-[10px] text-gray-400">
                    Total Appointments
                </p>
            </div>
        </div>

        <div v-if="departmentStatistics.length" class="grid grid-cols-1 xl:grid-cols-5">

            <!-- Left: Chart -->
            <div class="xl:col-span-3 p-6 border-b xl:border-b-0 xl:border-r border-gray-100">

                <div class="mb-5">
                    <h4 class="text-sm font-bold text-gray-800">
                        Appointments by Department
                    </h4>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Compare appointment volume across departments
                    </p>
                </div>

                <div class="h-72">
                    <Bar :data="chartData" :options="chartOptions" />
                </div>
            </div>

            <!-- Right: Overview -->
            <div class="xl:col-span-2 p-6">

                <div class="mb-5">
                    <h4 class="text-sm font-bold text-gray-800">
                        Department Overview
                    </h4>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Detailed breakdown with percentage share
                    </p>
                </div>

                <div class="space-y-5">

                    <div v-for="department in departmentStatistics" :key="department.name">
                        <!-- Department Info -->
                        <div class="flex items-center gap-3">

                            <!-- Department Icon -->
                            <div
                                class="w-9 h-9 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h6m-6 4h6m-6 4h4" />
                                </svg>
                            </div>

                            <!-- Name -->
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-800 truncate">
                                    {{ department.name }}
                                </p>

                                <p class="text-[11px] text-gray-400">
                                    {{ department.doctors_count }} Doctors
                                </p>
                            </div>

                            <!-- Count -->
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">
                                    {{ department.appointments_count }}
                                </p>

                                <p class="text-[10px] text-gray-400">
                                    {{ getPercentage(department.appointments_count) }}%
                                </p>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="flex items-center gap-3 mt-2 ml-12">

                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-500 transition-all duration-500" :style="{
                                    width: getPercentage(department.appointments_count) + '%'
                                }"></div>
                            </div>

                            <span class="text-gray-300 text-sm">
                                →
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Empty -->
        <div v-else class="py-12 text-center text-gray-400 text-sm">
            No department data available.
        </div>
    </div>
</template>