<script setup>
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';

defineProps({
    prescriptions: Object,
});

const formatDate = (v) => v
    ? new Date(v).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
    : '-';
const rx = (id) => 'RX-' + String(id).padStart(5, '0');
</script>

<template>
    <PortalLayout>
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">My Prescriptions</h2>
            <p class="text-xs text-gray-500 font-medium mt-0.5">View online or download as PDF.</p>
        </div>

        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl divide-y divide-gray-100">
            <p v-if="!prescriptions.data.length" class="p-6 text-xs text-gray-500 font-medium">No prescriptions yet.</p>

            <div v-for="p in prescriptions.data" :key="p.id"
                class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-orange-600">#{{ rx(p.id) }}</p>
                    <p class="text-sm font-bold text-gray-900 break-words">{{ p.diagnosis }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        {{ formatDate(p.prescribed_date) }} &middot; Dr. {{ p.doctor?.name ?? 'N/A' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('portal.prescriptions.show', p.id)"
                        class="text-[11px] font-bold text-gray-700 hover:text-gray-900 bg-white border border-gray-200 px-3.5 py-2 rounded-lg transition">
                        View
                    </Link>
                    <a :href="route('portal.prescriptions.pdf', p.id)"
                        class="text-[11px] font-bold text-white bg-orange-500 hover:bg-orange-600 px-3.5 py-2 rounded-lg transition">
                        Download PDF
                    </a>
                </div>
            </div>
        </div>

        <div v-if="prescriptions.prev_page_url || prescriptions.next_page_url" class="flex justify-between text-xs font-bold">
            <Link v-if="prescriptions.prev_page_url" :href="prescriptions.prev_page_url" class="text-orange-600 hover:underline">&larr; Newer</Link>
            <span v-else></span>
            <Link v-if="prescriptions.next_page_url" :href="prescriptions.next_page_url" class="text-orange-600 hover:underline">Older &rarr;</Link>
        </div>
    </PortalLayout>
</template>
