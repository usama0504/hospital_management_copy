<script setup>
import { Line } from 'vue-chartjs';

defineProps({
    trendChartData: Object,
    trendChartOptions: Object,
});
</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <h3 class="text-base font-bold text-gray-800">Appointments &amp; Revenue Trend</h3>
            <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-gray-500">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-400"></span> Appointments</span>
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-indigo-600"></span> Revenue</span>
            </div>
        </div>
        <div class="h-60 w-full">
            <Line :data="trendChartData" :options="trendChartOptions" />
        </div>
    </div>
</template>