<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    recentAppointments: Array,
    formatDate: Function,
    formatTime: Function,
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800">Appointment Activity</h3>
            <Link :href="route('appointments.index')" class="text-xs text-orange-500 font-semibold hover:underline">View All</Link>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/60 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6 font-semibold">Name</th>
                        <th class="py-3.5 px-4 sm:px-6 font-semibold">Date</th>
                        <th class="py-3.5 px-4 sm:px-6 font-semibold">Visit Time</th>
                        <th class="py-3.5 px-4 sm:px-6 font-semibold">Doctor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    <template v-if="recentAppointments && recentAppointments.length > 0">
                        <tr v-for="appointment in recentAppointments" :key="appointment.id" class="hover:bg-gray-50/40 transition">
                            <td class="py-4 px-4 sm:px-6 font-bold text-gray-900">
                                <span class="truncate max-w-[120px] sm:max-w-none">{{ appointment.patient?.name || 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4 sm:px-6 text-xs whitespace-nowrap">
                                {{ formatDate(appointment.appointment_date) }}
                            </td>
                            <td class="py-4 px-4 sm:px-6 text-xs whitespace-nowrap font-medium text-gray-700">
                                {{ formatTime(appointment.appointment_date) }}
                            </td>
                            <td class="py-4 px-4 sm:px-6 font-bold text-gray-800 text-xs sm:text-sm whitespace-nowrap">
                                Dr. {{ appointment.doctor?.name || 'N/A' }}
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-400 text-sm">
                                No appointments found. Add an appointment to see it here!
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>