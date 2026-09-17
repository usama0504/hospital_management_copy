<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

const props = defineProps({
    events: Array,
});

// Selected event details shown in the side panel when an event is clicked
const selectedEvent = ref(null);

// Screen size track karte hain taake chhoti screen pe calendar automatically
// simplified list-view + chhota toolbar use kare (FullCalendar khud responsive nahi hota)
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024);
const isMobile = computed(() => windowWidth.value < 640);
const isTablet = computed(() => windowWidth.value >= 640 && windowWidth.value < 1024);

const handleResize = () => {
    windowWidth.value = window.innerWidth;
};

onMounted(() => window.addEventListener('resize', handleResize));
onUnmounted(() => window.removeEventListener('resize', handleResize));

// Header toolbar screen size ke hisaab se badalta hai:
// - Mobile: sirf prev/next/today + title, view-switch neeche chhote buttons se
// - Tablet/Desktop: full toolbar with view switch buttons
const headerToolbar = computed(() => {
    if (isMobile.value) {
        return { left: 'prev,next', center: 'title', right: 'today' };
    }
    if (isTablet.value) {
        return { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listWeek' };
    }
    return { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' };
});

const initialView = computed(() => (isMobile.value ? 'listWeek' : 'dayGridMonth'));

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: initialView.value,
    headerToolbar: headerToolbar.value,
    height: 'auto',
    aspectRatio: isMobile.value ? 0.9 : 1.6,
    contentHeight: 'auto',
    dayMaxEvents: isMobile.value ? 2 : 3,
    events: props.events,
    eventClick(info) {
        selectedEvent.value = {
            id: info.event.id,
            title: info.event.title,
            start: info.event.start,
            status: info.event.extendedProps.status,
            patient: info.event.extendedProps.patient,
            doctor: info.event.extendedProps.doctor,
        };
    },
}));

// Mobile ke liye chhote view-switch buttons neeche dikhaye jaate hain
// (kyun ke default toolbar mein sab buttons ek sath fit nahi hote)
const calendarRef = ref(null);
const mobileViews = [
    { key: 'listWeek', label: 'List' },
    { key: 'dayGridMonth', label: 'Month' },
    { key: 'timeGridWeek', label: 'Week' },
    { key: 'timeGridDay', label: 'Day' },
];
const activeMobileView = ref('listWeek');
const switchView = (viewName) => {
    activeMobileView.value = viewName;
    const api = calendarRef.value?.getApi?.();
    if (api) api.changeView(viewName);
};

const formatTime = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
};

const statusBadge = (status) => ({
    Scheduled: 'bg-amber-50 text-amber-700 border-amber-200',
    Completed: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    Cancelled: 'bg-rose-50 text-rose-700 border-rose-200',
}[status] ?? 'bg-gray-50 text-gray-700 border-gray-200');

const goToEdit = (id) => {
    router.visit(route('appointments.edit', id));
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div>
                    <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight">Appointment Calendar</h2>
                    <p class="text-[11px] sm:text-xs text-gray-500 mt-1 font-medium">Visual view of all scheduled
                        appointments.</p>
                </div>
                <Link :href="route('appointments.index')"
                    class="inline-flex items-center justify-center gap-2 bg-white text-gray-700 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl font-semibold text-xs shadow-sm border border-gray-200 hover:bg-gray-50 transition w-full sm:w-auto">
                    List View
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

                <!-- Calendar -->
                <div class="lg:col-span-2 bg-white border border-gray-100 shadow-sm rounded-2xl p-2.5 sm:p-5 calendar-wrapper"
                    :class="{ 'is-mobile': isMobile }">

                    <!-- Mobile-only view switch (2x2 grid of small buttons, replaces cramped toolbar buttons) -->
                    <div v-if="isMobile" class="grid grid-cols-4 gap-1.5 mb-3">
                        <button v-for="v in mobileViews" :key="v.key" type="button" @click="switchView(v.key)"
                            class="text-[10px] font-bold py-1.5 rounded-lg border transition" :class="activeMobileView === v.key
                                ? 'bg-orange-500 text-white border-orange-500'
                                : 'bg-gray-50 text-gray-600 border-gray-200'">
                            {{ v.label }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <FullCalendar ref="calendarRef" :options="calendarOptions" :key="isMobile" />
                    </div>
                </div>

                <!-- Side Panel: legend + selected event details -->
                <div class="space-y-4">
                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 sm:p-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Status Legend</h3>
                        <div class="flex sm:block gap-4 sm:gap-0 sm:space-y-2 text-xs font-semibold text-gray-600">
                            <div class="flex items-center gap-2"><span
                                    class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span> Scheduled</div>
                            <div class="flex items-center gap-2"><span
                                    class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span> Completed</div>
                            <div class="flex items-center gap-2"><span
                                    class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span> Cancelled</div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 sm:p-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Appointment Details
                        </h3>
                        <template v-if="selectedEvent">
                            <div class="space-y-2.5">
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Patient</span>
                                    <p class="text-sm font-bold text-gray-900">{{ selectedEvent.patient ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Doctor</span>
                                    <p class="text-sm font-semibold text-gray-700">Dr. {{ selectedEvent.doctor ?? 'N/A'
                                        }}</p>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Date
                                        &amp; Time</span>
                                    <p class="text-xs font-semibold text-gray-600 break-words">{{
                                        formatTime(selectedEvent.start) }}</p>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Status</span>
                                    <span class="inline-block text-[11px] font-bold px-2.5 py-1 rounded-full border"
                                        :class="statusBadge(selectedEvent.status)">
                                        {{ selectedEvent.status }}
                                    </span>
                                </div>
                                <button @click="goToEdit(selectedEvent.id)" type="button"
                                    class="w-full mt-3 inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                                    Edit Appointment
                                </button>
                            </div>
                        </template>
                        <template v-else>
                            <p class="text-xs text-gray-400 font-medium">Click on any appointment in the calendar to
                                view its details here.</p>
                        </template>
                    </div>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style>
/* FullCalendar ko app ke Tailwind theme (orange accents, rounded corners) ke sath match karne ke liye */
.calendar-wrapper .fc {
    font-family: inherit;
    font-size: 0.75rem;
}

.calendar-wrapper .fc-toolbar {
    flex-wrap: wrap;
    gap: 0.5rem;
    row-gap: 0.5rem;
}

.calendar-wrapper .fc-toolbar-chunk {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.35rem;
}

.calendar-wrapper .fc-toolbar-title {
    font-size: 1rem;
    font-weight: 800;
    color: #111827;
}

.calendar-wrapper .fc-button {
    background-color: #f9fafb !important;
    border-color: #e5e7eb !important;
    color: #374151 !important;
    font-weight: 600 !important;
    text-transform: capitalize !important;
    box-shadow: none !important;
    padding: 0.35rem 0.7rem !important;
}

.calendar-wrapper .fc-button:hover {
    background-color: #f3f4f6 !important;
}

.calendar-wrapper .fc-button-active,
.calendar-wrapper .fc-button-primary:not(:disabled).fc-button-active {
    background-color: #f97316 !important;
    border-color: #f97316 !important;
    color: #ffffff !important;
}

.calendar-wrapper .fc-daygrid-day.fc-day-today,
.calendar-wrapper .fc-timegrid-col.fc-day-today {
    background-color: #fff7ed !important;
}

.calendar-wrapper .fc-event {
    border: none;
    border-radius: 6px;
    padding: 1px 4px;
    cursor: pointer;
    font-weight: 600;
}

/* Mobile-specific tightening */
.calendar-wrapper.is-mobile .fc {
    font-size: 0.68rem;
}

.calendar-wrapper.is-mobile .fc-toolbar-title {
    font-size: 0.85rem;
}

.calendar-wrapper.is-mobile .fc-button {
    padding: 0.3rem 0.5rem !important;
    font-size: 0.65rem !important;
}

.calendar-wrapper.is-mobile .fc-daygrid-day-number,
.calendar-wrapper.is-mobile .fc-col-header-cell-cushion {
    font-size: 0.65rem;
    padding: 2px;
}

.calendar-wrapper.is-mobile .fc-list-event-title,
.calendar-wrapper.is-mobile .fc-list-event-time {
    font-size: 0.7rem;
}

.calendar-wrapper.is-mobile .fc-daygrid-event {
    font-size: 0.6rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>