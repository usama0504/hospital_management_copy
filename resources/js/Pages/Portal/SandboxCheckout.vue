<script setup>
import { Link, router } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';

const props = defineProps({
    payment: Object,
});

const money = (v) => 'Rs. ' + Number(v || 0).toLocaleString();

const finish = (outcome) => {
    router.post(route('portal.payments.sandbox.complete', props.payment.id), { outcome });
};
</script>

<template>
    <PortalLayout>
        <div class="max-w-md mx-auto bg-white border border-gray-100 shadow-sm rounded-2xl p-6 sm:p-8 space-y-5">
            <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-xs font-semibold px-4 py-3">
                Test mode &mdash; this is a simulated payment page. No real money is charged.
            </div>

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Payment</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ money(payment.amount) }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">
                    Reference {{ payment.reference }}
                    <span v-if="payment.bill?.doctor"> &middot; Dr. {{ payment.bill.doctor.name }}</span>
                </p>
            </div>

            <div class="flex flex-col gap-2.5">
                <button @click="finish('paid')"
                    class="text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition px-5 py-3 rounded-xl">
                    Pay {{ money(payment.amount) }} (simulate success)
                </button>
                <button @click="finish('failed')"
                    class="text-sm font-bold text-rose-600 bg-white hover:bg-rose-50 border border-rose-200 transition px-5 py-3 rounded-xl">
                    Simulate failure
                </button>
                <Link :href="route('portal.bills')" class="text-center text-xs font-bold text-gray-500 hover:text-gray-800 mt-1">
                    Cancel and go back
                </Link>
            </div>
        </div>
    </PortalLayout>
</template>
