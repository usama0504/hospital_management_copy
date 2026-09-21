<script setup>
defineProps({
    appointmentsCount: Number,
    operationsCount: Number,
    patientsCount: Number,
    totalEarnings: [Number, String],
    isDoctor: Boolean,
    formatCurrency: Function,
});
</script>

<template>
    <div>
        <h3 class="text-base font-bold text-gray-800 mb-4">Activity Overview</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Appointments -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ appointmentsCount || 0 }}</p>
                    <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">Appointments</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Operations -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ operationsCount || 0 }}</p>
                    <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">Operations</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" />
                    </svg>
                </div>
            </div>

            <!-- New Patients -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ patientsCount || 0 }}</p>
                    <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">Total Patients</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <!-- Earnings (Hidden for Doctors) -->
            <div v-if="!isDoctor"
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-2xl font-black text-gray-900">${{ formatCurrency(totalEarnings || 0) }}</p>
                    <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">Earning</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>