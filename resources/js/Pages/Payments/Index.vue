<script setup>
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    payments: Object,
    status: String,
    counts: Object,
});

const tabs = [
    { key: 'pending', label: 'Pending' },
    { key: 'paid', label: 'Approved' },
    { key: 'failed', label: 'Rejected' },
];

const formatDateTime = (v) => v
    ? new Date(v).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    : '-';
const money = (v) => 'Rs. ' + Number(v || 0).toLocaleString();

const billChanged = (p) => p.bill && Number(p.bill.amount) !== Number(p.amount);

const approve = (p) => {
    if (!confirm(`Confirm you have received ${money(p.amount)} (Transaction ${p.transaction_id}) in the JazzCash account?`)) return;
    router.post(route('payments.approve', p.id), {}, { preserveScroll: true });
};

const reject = (p) => {
    const note = prompt('Reason for rejecting this payment (the patient will see it):');
    if (!note || !note.trim()) return;
    router.post(route('payments.reject', p.id), { note: note.trim() }, { preserveScroll: true });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 px-3 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-5">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">JazzCash Payments</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                            Check each Transaction ID in the hospital's JazzCash app before approving.
                        </p>
                    </div>
                    <Link :href="route('bills.index')"
                        class="text-center text-xs font-bold text-gray-600 hover:text-gray-900 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                        &larr; Bills
                    </Link>
                </div>

                <div v-if="$page.props.flash?.success"
                    class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-xs font-semibold px-4 py-3">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error"
                    class="rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-xs font-semibold px-4 py-3">
                    {{ $page.props.flash.error }}
                </div>

                <div class="flex gap-1.5 overflow-x-auto">
                    <Link v-for="t in tabs" :key="t.key" :href="route('payments.index', { status: t.key })"
                        :class="status === t.key ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition">
                        {{ t.label }} ({{ counts[t.key] }})
                    </Link>
                </div>

                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl divide-y divide-gray-100">
                    <p v-if="!payments.data.length" class="p-6 text-xs text-gray-500 font-medium">Nothing here.</p>

                    <div v-for="p in payments.data" :key="p.id" class="p-4 sm:p-5 space-y-3">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div class="min-w-0 space-y-1">
                                <p class="text-sm font-bold text-gray-900">
                                    {{ p.patient?.name ?? 'Unknown patient' }}
                                    <span class="text-gray-400 font-medium">&middot; {{ money(p.amount) }}</span>
                                </p>
                                <p class="text-xs text-gray-600 font-medium">
                                    Transaction ID: <span class="font-black text-gray-900 tracking-wide">{{ p.transaction_id }}</span>
                                    <span v-if="p.sender_number"> &middot; From {{ p.sender_number }}</span>
                                </p>
                                <p class="text-xs text-gray-500 font-medium">
                                    Bill #{{ p.bill_id }}
                                    <span v-if="p.bill?.doctor"> &middot; Dr. {{ p.bill.doctor.name }}</span>
                                    &middot; Submitted {{ formatDateTime(p.created_at) }}
                                </p>
                                <p v-if="billChanged(p)" class="text-xs text-rose-600 font-semibold">
                                    The bill is now {{ money(p.bill.amount) }}, which differs from what the patient paid.
                                </p>
                                <p v-if="p.status !== 'pending'" class="text-xs text-gray-500 font-medium">
                                    {{ p.status === 'paid' ? 'Approved' : 'Rejected' }}
                                    <span v-if="p.reviewer"> by {{ p.reviewer.name }}</span>
                                    <span v-if="p.reviewed_at"> on {{ formatDateTime(p.reviewed_at) }}</span>
                                    <span v-if="p.review_note"> &mdash; {{ p.review_note }}</span>
                                </p>
                            </div>

                            <div v-if="p.status === 'pending'" class="flex gap-2 shrink-0">
                                <button @click="approve(p)"
                                    class="text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition px-4 py-2 rounded-lg">
                                    Approve
                                </button>
                                <button @click="reject(p)"
                                    class="text-xs font-bold text-rose-600 bg-white hover:bg-rose-50 border border-rose-200 transition px-4 py-2 rounded-lg">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="payments.prev_page_url || payments.next_page_url" class="flex justify-between text-xs font-bold">
                    <Link v-if="payments.prev_page_url" :href="payments.prev_page_url" class="text-orange-600 hover:underline">&larr; Previous</Link>
                    <span v-else></span>
                    <Link v-if="payments.next_page_url" :href="payments.next_page_url" class="text-orange-600 hover:underline">Next &rarr;</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
