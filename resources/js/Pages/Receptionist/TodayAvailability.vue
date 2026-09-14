<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
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

// Availabilities ko doctor_id ke hisab se group kar do
const groupedByDoctor = computed(() => {
    if (!props.availabilities || props.availabilities.length === 0) return [];

    const groups = {};

    props.availabilities.forEach((slot) => {
        const doctorId = slot.doctor_id;

        if (!groups[doctorId]) {
            groups[doctorId] = {
                doctor_id: doctorId,
                doctor: slot.doctor,
                slots: [],
            };
        }

        groups[doctorId].slots.push({
            start_time: slot.start_time,
            end_time: slot.end_time,
        });
    });

    return Object.values(groups);
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-4 sm:py-6 lg:py-8">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

                <!-- Header Section -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight truncate">
                            Today's Doctor Availability
                        </h1>
                        <p class="text-[11px] sm:text-xs text-gray-500 mt-0.5 font-medium">
                            Showing available doctors for today:
                            <span class="font-bold text-orange-600">{{ currentDay }}</span>
                        </p>
                    </div>
                    <Link :href="route('appointments.index')"
                        class="inline-flex items-center justify-center gap-1.5 bg-orange-500 text-white px-6 py-3 rounded-xl font-bold text-xs hover:bg-orange-600 transition shadow-sm shadow-orange-500/20 w-full sm:w-auto">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                        </svg>
                        Back Appointments

                    </Link>
                </div>

                <!-- Content Card -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden p-4 sm:p-6 lg:p-8">
                    <h2 class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 sm:mb-6">
                        Available Schedule List
                    </h2>

                    <!-- Empty State -->
                    <div v-if="!groupedByDoctor || groupedByDoctor.length === 0" class="text-center py-10 sm:py-12">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-300 mx-auto mb-3" fill="none"
                            stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <p class="text-xs font-semibold text-gray-500">No doctors are available today.</p>
                        <p class="text-[11px] text-gray-400 mt-1 px-4">Please check back later or view the full
                            schedule.</p>
                    </div>

                    <!-- Doctors Grid -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                        <div v-for="group in groupedByDoctor" :key="group.doctor_id"
                            class="border border-gray-100 rounded-2xl p-4 sm:p-5 bg-gray-50/40 flex flex-col justify-between gap-4 shadow-2xs hover:border-orange-200 hover:bg-white transition">

                            <!-- Top Info -->
                            <div class="min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-black text-gray-900 text-sm tracking-tight truncate">
                                        Dr. {{ group.doctor?.name || 'N/A' }}
                                    </h3>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex-shrink-0">
                                        Active
                                    </span>
                                </div>

                                <!-- Specialization / Specialty -->
                                <p class="text-xs font-semibold text-orange-600 truncate">
                                    {{ group.doctor?.specialization || 'General Practitioner' }}
                                </p>

                                <!-- Timing Slots (multiple ho sakte hain) -->
                                <div class="mt-4 space-y-2">
                                    <div v-for="(slot, index) in group.slots" :key="index"
                                        class="flex items-center gap-2 bg-orange-50/60 border border-orange-100 px-3 sm:px-3.5 py-2.5 rounded-xl text-orange-900">
                                        <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs font-bold whitespace-nowrap">
                                            {{ formatTime(slot.start_time) }} - {{ formatTime(slot.end_time) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Common Book Now Button (sirf ek hi, poori list ke liye) -->
                    <div v-if="groupedByDoctor.length"
                        class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t border-gray-100 flex justify-center sm:justify-end">
                        <Link :href="route('appointments.create')"
                            class="inline-flex items-center justify-center gap-1.5 bg-orange-500 text-white px-6 py-3 rounded-xl font-bold text-xs hover:bg-orange-600 transition shadow-sm shadow-orange-500/20 w-full sm:w-auto">
                            Appointments Now
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                            </svg>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>