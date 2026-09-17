<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    events: Array
});

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    height: 'auto',
    handleWindowResize: true,
    events: props.events,
    eventClick: (info) => {
        alert(`Patient: ${info.event.extendedProps.patient || info.event.title}\nStatus: ${info.event.extendedProps.status}`);
    }
});
</script>

<template>

    <Head title="Appointments Calendar" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Appointments Calendar</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Visual schedule of all patient appointments.</p>
                </div>
                <a href="/appointments/create"
                    class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors gap-2 self-start sm:self-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Appointment
                </a>
            </div>
        </template>

        <div class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Card Container -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <FullCalendar :options="calendarOptions" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Clean & Minimal FullCalendar Adjustments */
.fc {
    font-family: inherit;
}

.fc .fc-toolbar-title {
    font-size: 1.125rem !important;
    font-weight: 600;
    color: #1f2937;
}

.fc .fc-button {
    background-color: #f97316 !important;
    border-color: #f97316 !important;
    font-size: 0.813rem !important;
    font-weight: 500;
    padding: 6px 12px !important;
    border-radius: 6px !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.fc .fc-button:hover {
    background-color: #ea580c !important;
    border-color: #ea580c !important;
}

.fc .fc-button-active {
    background-color: #c2410c !important;
    border-color: #c2410c !important;
}

.fc-event {
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
}

@media (max-width: 640px) {
    .fc .fc-toolbar {
        flex-direction: column;
        gap: 10px;
    }
}
</style>