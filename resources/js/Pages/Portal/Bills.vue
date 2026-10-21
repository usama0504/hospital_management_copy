<script setup>
import { Link, router } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';

defineProps({
    bills: Object,
    unpaidTotal: Number,
    payments: { type: Object, default: () => ({ enabled: false, sandbox: false, manual: false }) },
});

const canPay = (b) => ['Unpaid', 'Pending'].includes(b.status) && Number(b.amount) > 0;

const pay = (b) => router.post(route('portal.bills.pay', b.id));

const formatDate = (v) => v
    ? new Date(v).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
    : '-';
const money = (v) => 'Rs. ' + Number(v || 0).toLocaleString();

const statusClass = (status) =>
    (status || '').toLowerCase() === 'paid'
        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
        : 'bg-amber-50 text-amber-700 border-amber-200';
</script>

<template>
    <PortalLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">My Bills</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">
                    {{ (payments.enabled || payments.manual) ? 'Pay online, or at the hospital reception.' : 'Payments are made at the hospital reception.' }}
                </p>
            </div>
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl px-5 py-3">
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Unpaid</p>
                <p class="text-lg font-black" :class="unpaidTotal > 0 ? 'text-rose-600' : 'text-emerald-600'">{{ money(unpaidTotal) }}</p>
            </div>
        </div>

        <div v-if="payments.enabled && payments.sandbox"
            class="rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-xs font-semibold px-4 py-3">
            Online payment is in test mode. No real money is charged.
        </div>

        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl divide-y divide-gray-100">
            <p v-if="!bills.data.length" class="p-6 text-xs text-gray-500 font-medium">No bills yet.</p>

            <div v-for="b in bills.data" :key="b.id"
                class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-900">{{ money(b.amount) }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        {{ formatDate(b.bill_date) }}<span v-if="b.doctor"> &middot; Dr. {{ b.doctor.name }}</span>
                    </p>
                    <p v-if="b.rejection_note && canPay(b) && !b.has_pending_payment" class="text-xs text-rose-600 font-semibold mt-1">
                        Your last payment was not accepted: {{ b.rejection_note }}
                    </p>
                </div>
                <div class="flex items-center gap-3 self-start sm:self-auto">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border" :class="statusClass(b.status)">{{ b.status }}</span>
                    <span v-if="b.has_pending_payment"
                        class="text-[10px] font-bold px-2.5 py-1 rounded-full border bg-sky-50 text-sky-700 border-sky-200">Payment under review</span>
                    <Link v-if="payments.manual && canPay(b) && !b.has_pending_payment" :href="route('portal.bills.jazzcash', b.id)"
                        class="text-[11px] font-bold text-orange-600 hover:bg-orange-50 border border-orange-200 transition px-4 py-2 rounded-lg">
                        Pay via JazzCash
                    </Link>
                    <button v-if="payments.enabled && canPay(b) && !b.has_pending_payment" @click="pay(b)"
                        class="text-[11px] font-bold text-white bg-orange-500 hover:bg-orange-600 transition px-4 py-2 rounded-lg shadow-sm shadow-orange-500/20">
                        Pay Now
                    </button>
                </div>
            </div>
        </div>

        <div v-if="bills.prev_page_url || bills.next_page_url" class="flex justify-between text-xs font-bold">
            <Link v-if="bills.prev_page_url" :href="bills.prev_page_url" class="text-orange-600 hover:underline">&larr; Newer</Link>
            <span v-else></span>
            <Link v-if="bills.next_page_url" :href="bills.next_page_url" class="text-orange-600 hover:underline">Older &rarr;</Link>
        </div>
    </PortalLayout>
</template>
