<script setup>
import { Link, router } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';

defineProps({
    appointments: Object,
});

const formatDateTime = (v) => v
    ? new Date(v).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    : '-';

const statusClass = (status) => {
    switch ((status || '').toLowerCase()) {
        case 'completed':
        case 'approved':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'cancelled':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-amber-50 text-amber-700 border-amber-200';
    }
};

const canCancel = (a) =>
    ['Pending', 'Scheduled'].includes(a.status) && new Date(a.appointment_date) > new Date();

const cancel = (a) => {
    if (!confirm('Cancel this appointment?')) return;
    router.patch(route('portal.appointments.cancel', a.id), {}, { preserveScroll: true });
};
</script>

<template>
    <PortalLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">My Appointments</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Upcoming and past visits.</p>
            </div>
            <Link :href="route('portal.appointments.create')"
                class="text-center text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 transition px-5 py-3 rounded-xl shadow-md shadow-orange-500/20">
                Book New Appointment
            </Link>
        </div>

        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl divide-y divide-gray-100">
            <p v-if="!appointments.data.length" class="p-6 text-xs text-gray-500 font-medium">You have no appointments yet.</p>

            <div v-for="a in appointments.data" :key="a.id"
                class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-900">{{ formatDateTime(a.appointment_date) }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Dr. {{ a.doctor?.name ?? 'N/A' }}
                        <span v-if="a.doctor?.department"> &middot; {{ a.doctor.department.name }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border" :class="statusClass(a.status)">{{ a.status }}</span>
                    <button v-if="canCancel(a)" @click="cancel(a)"
                        class="text-[11px] font-bold text-rose-600 hover:bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <div v-if="appointments.prev_page_url || appointments.next_page_url" class="flex justify-between text-xs font-bold">
            <Link v-if="appointments.prev_page_url" :href="appointments.prev_page_url" class="text-orange-600 hover:underline">&larr; Newer</Link>
            <span v-else></span>
            <Link v-if="appointments.next_page_url" :href="appointments.next_page_url" class="text-orange-600 hover:underline">Older &rarr;</Link>
        </div>
    </PortalLayout>
</template>
