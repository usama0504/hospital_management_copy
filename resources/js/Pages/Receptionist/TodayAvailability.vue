<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane walay props
defineProps({
    currentDay: String,
    availabilities: Array,
});

// Helper function to format time string (e.g., "14:30:00" -> "02:30 PM")
const formatTime = (timeString) => {
    if (!timeString) return '';
    const [hours, minutes] = timeString.split(':');
    const h = parseInt(hours, 10);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const formattedHours = h % 12 || 12;
    return `${String(formattedHours).padStart(2, '0')}:${minutes} ${ampm}`;
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Today's Doctor Availability</h1>
                        <p class="text-xs text-gray-500 mt-0.5 font-medium">
                            Showing available doctors for today: <span class="font-bold text-orange-600">{{ currentDay }}</span>
                        </p>
                    </div>
                    <Link :href="route('appointments.index')" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-200 transition">
                        &larr; Back to Appointments
                    </Link>
                </div>

                <!-- Content Card -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden p-6 sm:p-8">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-6">Available Schedule List</h2>

                    <!-- Empty State -->
                    <div v-if="!availabilities || availabilities.length === 0" class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                        <p class="text-xs font-semibold text-gray-500">No doctors are available today.</p>
                        <p class="text-[11px] text-gray-400 mt-1">Please check back later or view the full schedule.</p>
                    </div>

                    <!-- Doctors Grid -->
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="slot in availabilities" :key="slot.id" class="border border-gray-100 rounded-2xl p-5 bg-gray-50/40 flex flex-col justify-between gap-4 shadow-2xs hover:border-orange-200 hover:bg-white transition group">
                            
                            <!-- Top Info -->
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-black text-gray-900 text-sm tracking-tight">Dr. {{ slot.doctor?.name || 'N/A' }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                </div>

                                <!-- Specialization / Specialty -->
                                <p class="text-xs font-semibold text-orange-600">
                                    {{ slot.doctor?.specialization || 'General Practitioner' }}
                                </p>

                                <!-- Timing Slot Box -->
                                <div class="mt-4 flex items-center gap-2 bg-orange-50/60 border border-orange-100 px-3.5 py-2.5 rounded-xl text-orange-900">
                                    <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs font-bold">
                                        {{ formatTime(slot.start_time) }} - {{ formatTime(slot.end_time) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 font-medium">Ready for bookings</span>
                                <Link :href="route('appointments.create', { doctor_id: slot.doctor_id })" class="inline-flex items-center gap-1.5 bg-orange-500 text-white px-3.5 py-2 rounded-xl font-bold text-[11px] hover:bg-orange-600 transition shadow-sm shadow-orange-500/20">
                                    Book Now
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                                    </svg>
                                </Link>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>