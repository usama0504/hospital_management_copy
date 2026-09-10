<script setup>
import { router, Link } from '@inertiajs/vue3';
import { reactive } from 'vue';

// Controller se ane walay props
const props = defineProps({
    from: String,
    to: String,
    patientsCount: Number,
    doctorsCount: Number,
    appointmentsCount: Number,
    pendingBillsCount: Number,
    paidBillsCount: Number,
});

// Form state for date filtering using Inertia router.get
const form = reactive({
    from: props.from || '',
    to: props.to || '',
});

const handleFilter = () => {
    router.get(route('reports.index'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-6">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Hospital Reports</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Filter and view overall hospital activity statistics and reports.</p>
            </div>

            <!-- Date Filter Form Card -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 mb-6">
                <form @submit.prevent="handleFilter" class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="w-full sm:w-auto flex-1">
                        <label for="from" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">From Date</label>
                        <input type="date" id="from" v-model="form.from"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                    </div>
                    <div class="w-full sm:w-auto flex-1">
                        <label for="to" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">To Date</label>
                        <input type="date" id="to" v-model="form.to"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center bg-orange-500 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                            Filter
                        </button>
                        <Link v-if="form.from || form.to" :href="route('reports.index')" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-center">
                            Reset
                        </Link>
                    </div>
                </form>
            </div>

            <!-- Summary Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                
                <!-- Total Patients -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Patients</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-gray-900">{{ patientsCount }}</div>
                </div>

                <!-- Total Doctors -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Doctors</span>
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-gray-900">{{ doctorsCount }}</div>
                </div>

                <!-- Total Appointments -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Appointments</span>
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-gray-900">{{ appointmentsCount }}</div>
                </div>

                <!-- Pending Bills -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pending Bills</span>
                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-rose-600">{{ pendingBillsCount }}</div>
                </div>

                <!-- Paid Bills -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Paid Bills</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-emerald-600">{{ paidBillsCount }}</div>
                </div>

            </div>

        </div>
    </div>
</template>