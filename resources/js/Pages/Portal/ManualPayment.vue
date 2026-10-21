<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';

const props = defineProps({
    bill: Object,
    account: Object,
});

const form = useForm({
    transaction_id: '',
    sender_number: '',
});

const copied = ref(false);
const copyNumber = async () => {
    try {
        await navigator.clipboard.writeText(props.account.number);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch (e) {
        /* copy na ho to number screen par dikh hi raha hai */
    }
};

const money = (v) => 'Rs. ' + Number(v || 0).toLocaleString();
const submit = () => form.post(route('portal.bills.jazzcash.store', props.bill.id));

const inputClass = 'w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 outline-none transition';
</script>

<template>
    <PortalLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Pay with JazzCash</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Send the money from your JazzCash app, then enter the Transaction ID below.</p>
            </div>
            <Link :href="route('portal.bills')"
                class="text-center text-xs font-bold text-gray-600 hover:text-gray-900 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                &larr; My Bills
            </Link>
        </div>

        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 sm:p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Amount to send</p>
                    <p class="text-lg font-black text-gray-900 mt-0.5">{{ money(bill.amount) }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">JazzCash number</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-lg font-black text-gray-900 break-all">{{ account.number }}</p>
                        <button type="button" @click="copyNumber"
                            class="text-[10px] font-bold text-orange-600 hover:underline shrink-0">{{ copied ? 'Copied' : 'Copy' }}</button>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">{{ account.name }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Bill reference</p>
                    <p class="text-lg font-black text-orange-600 mt-0.5">{{ bill.reference }}</p>
                    <p class="text-xs text-gray-500 font-medium">Write this in the payment note</p>
                </div>
            </div>

            <ol class="list-decimal pl-5 space-y-1.5 text-xs text-gray-600 font-medium">
                <li>Open your JazzCash app and choose Send Money.</li>
                <li>Send exactly <b class="text-gray-900">{{ money(bill.amount) }}</b> to the number above.</li>
                <li>Copy the <b class="text-gray-900">Transaction ID</b> from the JazzCash receipt or SMS.</li>
                <li>Enter it below. Your bill is marked paid once the hospital confirms the payment.</li>
            </ol>

            <form @submit.prevent="submit" class="space-y-5 border-t border-gray-100 pt-6">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Transaction ID</label>
                    <input v-model="form.transaction_id" type="text" maxlength="40" placeholder="e.g. 12345678901" :class="inputClass" />
                    <p v-if="form.errors.transaction_id" class="text-xs text-rose-600 font-semibold mt-1">{{ form.errors.transaction_id }}</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Your JazzCash number (optional)</label>
                    <input v-model="form.sender_number" type="text" maxlength="20" placeholder="03XX XXXXXXX" :class="inputClass" />
                    <p v-if="form.errors.sender_number" class="text-xs text-rose-600 font-semibold mt-1">{{ form.errors.sender_number }}</p>
                </div>

                <button type="submit" :disabled="!form.transaction_id || form.processing"
                    class="w-full sm:w-auto text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed transition px-8 py-3 rounded-xl shadow-md shadow-orange-500/20">
                    {{ form.processing ? 'Submitting...' : 'Submit Payment Details' }}
                </button>
            </form>
        </div>
    </PortalLayout>
</template>
