<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    appointments: Object,
    filters: Object,
});

// Search + Status filter: URL query string ke sath sync, debounced
const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');
let searchTimeout = null;

const applyFilters = () => {
    router.get(route('appointments.index'), {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch(status, applyFilters);

const clearSearch = () => {
    search.value = '';
};

const deleteAppointment = (id) => {
    if (confirm('Are you sure you want to delete this appointment?')) {
        router.delete(route('appointments.destroy', id), {
            preserveScroll: true,
        });
    }
};

const getStatusClass = (status) => {
    switch ((status || '').toLowerCase()) {
        case 'completed':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'cancelled':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-amber-50 text-amber-700 border-amber-200';
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return (
        date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) +
        ' - ' +
        date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    );
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-4 px-2 sm:space-y-6 sm:px-0">

            <!-- Header & Action Section -->
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-gray-900 sm:text-xl md:text-2xl">
                        Appointments List
                    </h2>
                    <p class="mt-1 text-xs font-medium text-gray-500">
                        Manage and monitor all hospital appointments efficiently.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:justify-end">
                    <!-- Calendar View -->
                    <Link :href="route('appointments.calendar')"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 shadow-sm transition hover:bg-gray-50 sm:px-4 sm:py-2.5">
                        <svg class="h-4 w-4 shrink-0 text-orange-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span>Calendar View</span>
                    </Link>

                    <!-- Today's Doctors -->
                    <Link :href="route('receptionist.today')"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 shadow-sm transition hover:bg-gray-50 sm:px-4 sm:py-2.5">
                        <svg class="h-4 w-4 shrink-0 text-orange-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Today's Doctors</span>
                    </Link>

                    <!-- Add Appointment -->
                    <Link :href="route('appointments.create')"
                        class="col-span-2 inline-flex items-center justify-center gap-1.5 rounded-xl bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/20 transition hover:bg-orange-600 sm:col-span-1 sm:px-5 sm:py-2.5">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Add Appointment</span>
                    </Link>
                </div>
            </div>

            <!-- Search + Status Filter -->
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input v-model="search" type="text" placeholder="Search by patient name..."
                        class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-9 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition shadow-sm" />
                    <button v-if="search" @click="clearSearch" type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <select v-model="status"
                    class="bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition shadow-sm sm:w-48">
                    <option value="">All Statuses</option>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Success Alert -->
            <div v-if="$page.props.flash?.success"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
                <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <!-- Main Card Wrapper -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

                <!-- MOBILE VIEW: Cards -->
                <div class="divide-y divide-gray-100 md:hidden">
                    <template v-if="appointments?.data?.length">
                        <div v-for="appointment in appointments.data" :key="appointment.id"
                            class="space-y-3 p-4 transition hover:bg-gray-50/50">

                            <!-- Patient + Status -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Patient</p>
                                    <h4 class="truncate text-sm font-bold text-gray-900">
                                        {{ appointment.patient?.name ?? 'N/A' }}
                                    </h4>
                                </div>
                                <span :class="getStatusClass(appointment.status)"
                                    class="shrink-0 rounded-lg border px-2 py-0.5 text-[10px] font-bold capitalize">
                                    {{ appointment.status }}
                                </span>
                            </div>

                            <!-- Doctor -->
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Doctor</p>
                                <p class="truncate text-xs font-semibold text-gray-700">
                                    Dr. {{ appointment.doctor?.name ?? 'N/A' }}
                                </p>
                            </div>

                            <!-- Date + Actions -->
                            <div class="flex items-center justify-between gap-2 border-t border-gray-100 pt-3">
                                <div class="flex min-w-0 items-center gap-1.5 text-[11px] font-medium text-gray-500">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="truncate">{{ formatDate(appointment.appointment_date) }}</span>
                                </div>

                                <div class="flex shrink-0 items-center gap-1.5">
                                    <Link :href="route('prescriptions.create', { appointment_id: appointment.id })"
                                        class="rounded-lg bg-gray-50 p-1.5 text-emerald-600 transition hover:bg-emerald-50"
                                        title="Write Prescription">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </Link>
                                    <Link :href="route('appointments.edit', appointment.id)"
                                        class="rounded-lg bg-gray-50 p-1.5 text-blue-600 transition hover:bg-blue-50"
                                        title="Edit">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                    </Link>
                                    <button @click="deleteAppointment(appointment.id)" type="button"
                                        class="rounded-lg bg-gray-50 p-1.5 text-rose-600 transition hover:bg-rose-50"
                                        title="Delete">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div v-else class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <p class="text-xs font-semibold text-gray-500">No appointments found.</p>
                        </div>
                    </div>
                </div>

                <!-- DESKTOP VIEW: Table -->
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs font-medium text-gray-600">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                            <tr>
                                <th class="px-4 py-3.5 lg:px-6">Patient</th>
                                <th class="px-4 py-3.5 lg:px-6">Doctor</th>
                                <th class="px-4 py-3.5 lg:px-6">Date & Time</th>
                                <th class="px-4 py-3.5 lg:px-6">Status</th>
                                <th class="px-4 py-3.5 text-right lg:px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template v-if="appointments?.data?.length">
                                <tr v-for="appointment in appointments.data" :key="appointment.id"
                                    class="transition hover:bg-gray-50/60">
                                    <td class="px-4 py-4 font-bold text-gray-900 lg:px-6">
                                        <span class="block max-w-[160px] truncate lg:max-w-none">
                                            {{ appointment.patient?.name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 font-semibold text-gray-700 lg:px-6">
                                        Dr. {{ appointment.doctor?.name ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 font-medium text-gray-500 lg:px-6">
                                        {{ formatDate(appointment.appointment_date) }}
                                    </td>
                                    <td class="px-4 py-4 lg:px-6">
                                        <span :class="getStatusClass(appointment.status)"
                                            class="inline-flex whitespace-nowrap rounded-lg border px-2.5 py-1 text-[11px] font-bold capitalize">
                                            {{ appointment.status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right lg:px-6">
                                        <div class="inline-flex items-center gap-1.5 lg:gap-2">
                                            <Link
                                                :href="route('prescriptions.create', { appointment_id: appointment.id })"
                                                class="rounded-xl bg-gray-50 p-1.5 text-emerald-600 transition hover:bg-emerald-50 lg:p-2"
                                                title="Write Prescription">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </Link>
                                            <Link :href="route('appointments.edit', appointment.id)"
                                                class="rounded-xl bg-gray-50 p-1.5 text-blue-600 transition hover:bg-blue-50 lg:p-2"
                                                title="Edit">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                            </Link>
                                            <button @click="deleteAppointment(appointment.id)" type="button"
                                                class="rounded-xl bg-gray-50 p-1.5 text-rose-600 transition hover:bg-rose-50 lg:p-2"
                                                title="Delete">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="5" class="py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        <p class="text-xs font-semibold text-gray-500">No appointments found.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Pagination Links -->
            <div class="mt-4 flex flex-wrap justify-center gap-1 sm:mt-6"
                v-if="appointments?.links && appointments.links.length > 3">
                <template v-for="(link, key) in appointments.links" :key="key">
                    <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                        class="min-w-[32px] rounded-lg border px-2.5 py-1.5 text-center text-xs transition sm:px-3"
                        :class="{
                            'border-orange-500 bg-orange-500 text-white shadow-sm': link.active,
                            'border-gray-200 bg-white text-gray-700 hover:bg-gray-50': link.url && !link.active,
                            'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400': !link.url,
                        }" />
                </template>
            </div>

        </div>
    </AuthenticatedLayout>
</template>