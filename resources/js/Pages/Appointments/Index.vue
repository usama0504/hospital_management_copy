<script setup>
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane walay props (pagination object)
const props = defineProps({
    appointments: Object, // Laravel pagination object (data, links, etc.)
});

// Delete appointment handler
const deleteAppointment = (id) => {
    if (confirm('Are you sure you want to delete this appointment?')) {
        router.delete(route('appointments.destroy', id), {
            preserveScroll: true,
        });
    }
};

// Helper functions
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
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' - ' + 
           date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const strtoupperName = (name) => {
    if (!name) return 'P';
    return name.charAt(0).toUpperCase();
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            
            <!-- Header & Action Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Appointments List</h2>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Manage and monitor all hospital appointments efficiently.</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Today's Available Doctors Button -->
                    <Link :href="route('receptionist.today')" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50 transition shadow-2xs">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Today's Doctors</span>
                    </Link>

                    <!-- Add Appointment Button -->
                    <Link :href="route('appointments.create')" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-xl font-semibold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Add Appointment</span>
                    </Link>
                </div>
            </div>

            <!-- Success Alert (Inertia Flash Messages) -->
            <div v-if="$page.props.flash?.success" class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <!-- Main Card Wrapper -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                
                <!-- MOBILE VIEW: Card Layout -->
                <div class="block md:hidden divide-y divide-gray-100">
                    <template v-if="appointments.data && appointments.data.length > 0">
                        <div v-for="appointment in appointments.data" :key="appointment.id" class="p-4 space-y-3 hover:bg-gray-50/50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupperName(appointment.patient?.name) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-xs">{{ appointment.patient?.name ?? 'N/A' }}</h4>
                                        <p class="text-[11px] text-gray-500">Dr. {{ appointment.doctor?.name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <span :class="getStatusClass(appointment.status)" class="inline-flex items-center px-2 py-0.5 rounded-lg border text-[10px] font-bold capitalize shadow-2xs">
                                    {{ appointment.status }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-gray-500 pt-1 border-t border-gray-50">
                                <div class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ formatDate(appointment.appointment_date) }}</span>
                                </div>

                                <!-- Mobile Actions -->
                                <div class="flex items-center gap-1.5">
                                    <Link :href="route('appointments.edit', appointment.id)" class="p-1.5 rounded-lg bg-gray-50 text-blue-600 hover:bg-blue-50 transition" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125"/>
                                        </svg>
                                    </Link>
                                    
                                    <button @click="deleteAppointment(appointment.id)" type="button" class="p-1.5 rounded-lg bg-gray-50 text-rose-600 hover:bg-rose-50 transition" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="text-center text-gray-400 py-12">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-500">No appointments found.</p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- DESKTOP VIEW: Table Layout -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs font-medium text-gray-600">
                        <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                            <tr>
                                <th class="py-3.5 px-6">Patient</th>
                                <th class="py-3.5 px-6">Doctor</th>
                                <th class="py-3.5 px-6">Date & Time</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template v-if="appointments.data && appointments.data.length > 0">
                                <tr v-for="appointment in appointments.data" :key="appointment.id" class="hover:bg-gray-50/60 transition">
                                    <td class="py-4 px-6 font-bold text-gray-900">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs">
                                                {{ strtoupperName(appointment.patient?.name) }}
                                            </div>
                                            <span>{{ appointment.patient?.name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-700 font-semibold">
                                        Dr. {{ appointment.doctor?.name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 font-medium">
                                        {{ formatDate(appointment.appointment_date) }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span :class="getStatusClass(appointment.status)" class="inline-flex items-center px-2.5 py-1 rounded-lg border text-[11px] font-bold capitalize shadow-2xs">
                                            {{ appointment.status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <Link :href="route('appointments.edit', appointment.id)" class="p-2 rounded-xl bg-gray-50 text-blue-600 hover:bg-blue-50 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125"/>
                                                </svg>
                                            </Link>
                                            
                                            <button @click="deleteAppointment(appointment.id)" type="button" class="p-2 rounded-xl bg-gray-50 text-rose-600 hover:bg-rose-50 transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="5" class="text-center text-gray-400 py-12">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                            </svg>
                                            <p class="text-xs font-semibold text-gray-500">No appointments found.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Pagination Links -->
            <div class="mt-6 flex justify-center gap-1 flex-wrap" v-if="appointments.links">
                <template v-for="(link, key) in appointments.links" :key="key">
                    <component
                        :is="link.url ? Link : 'span'"
                        :href="link.url"
                        v-html="link.label"
                        class="px-3 py-1.5 text-xs rounded-lg border transition"
                        :class="{
                            'bg-orange-500 text-white border-orange-500 shadow-sm': link.active,
                            'bg-white text-gray-700 border-gray-200 hover:bg-gray-50': link.url && !link.active,
                            'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': !link.url
                        }"
                    />
                </template>
            </div>

        </div>
    </AuthenticatedLayout>
</template>