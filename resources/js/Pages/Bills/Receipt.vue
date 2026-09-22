<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    bill: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

const formatCurrency = (amount) => {
    return Number(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatInvoiceId = (id) => {
    return String(id).padStart(5, '0');
};

const statusColors = {
    Paid: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    Pending: 'bg-amber-50 text-amber-700 border-amber-200',
    Unpaid: 'bg-rose-50 text-rose-700 border-rose-200',
};

const printPage = () => {
    window.print();
};
</script>

<template>

    <component :is="'style'">
        @media print {
        body * {
        visibility: hidden;
        }

        #printable-receipt,
        #printable-receipt * {
        visibility: visible;
        }

        #printable-receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
        padding: 10px !important;
        margin: 0 !important;
        }
        }
    </component>

    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-6 flex items-center justify-between print:hidden gap-4">
                    <Link :href="route('bills.index')"
                        class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                        &larr; Back to Bills List
                    </Link>
                    <button @click="printPage()"
                        class="inline-flex items-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-orange-500/20 hover:bg-orange-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Receipt
                    </button>
                </div>

                <div id="printable-receipt"
                    class="bg-white border border-gray-100 shadow-2xl shadow-gray-100/90 rounded-3xl p-6 sm:p-12 space-y-8 relative overflow-hidden">

                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-orange-400 to-orange-600"></div>

                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-100 pb-6 gap-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-2xl bg-orange-500 flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-orange-500/30">
                                HC
                            </div>
                            <div>
                                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Health Care</h1>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Official Medical Treatment & Billing
                                    Invoice</p>
                            </div>
                        </div>
                        <div
                            class="text-left sm:text-right bg-gray-50 px-5 py-3 rounded-2xl border border-gray-100 w-full sm:w-auto">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Invoice
                                Reference</span>
                            <span class="text-base font-black text-orange-600">#INV-{{ formatInvoiceId(bill.id)
                            }}</span>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm bg-gray-50/60 p-6 rounded-2xl border border-gray-100">

                        <div>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Billed
                                To Patient</span>
                            <h3 class="font-bold text-gray-900 text-base">{{ bill.patient?.name ?? 'N/A' }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5 font-medium">Phone: {{ bill.patient?.phone ?? 'N/A'
                            }}</p>
                        </div>

                        <div>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Consulting
                                Doctor</span>
                            <h3 class="font-bold text-gray-900 text-base">Dr. {{ bill.doctor?.name ?? 'N/A' }}</h3>
                            <p class="text-xs text-orange-600 font-semibold mt-0.5">{{ bill.doctor?.specialization ??
                                'Medical Specialist' }}</p>
                        </div>

                        <div class="sm:text-right flex flex-col sm:items-end justify-between">
                            <div>
                                <span
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Issued
                                    Date</span>
                                <span class="font-bold text-gray-800 text-xs sm:text-sm">{{ formatDate(bill.bill_date)
                                }}</span>
                            </div>
                            <div class="mt-3 sm:mt-0">
                                <span
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Payment
                                    Status</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border"
                                    :class="statusColors[bill.status] || 'bg-gray-50 text-gray-700 border-gray-200'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                    {{ bill.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="py-4 px-5">Description of Charges</th>
                                    <th class="py-4 px-5 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                                <tr>
                                    <td class="py-4 px-5 font-semibold text-gray-800">
                                        Doctor Consultation & Treatment Services
                                        <span class="block text-[11px] text-gray-400 font-normal mt-0.5">Standard
                                            medical care & consultation billing entry</span>
                                    </td>
                                    <td class="py-4 px-5 text-right font-black text-gray-900 text-base">
                                        Rs.{{ formatCurrency(bill.amount) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end pt-2">
                        <div
                            class="w-full sm:w-80 space-y-3 bg-gray-50/80 p-5 rounded-2xl border border-gray-100 shadow-sm">
                            <div class="flex justify-between text-xs text-gray-500 font-semibold">
                                <span>Subtotal</span>
                                <span class="text-gray-800">Rs.{{ formatCurrency(bill.amount) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 font-semibold">
                                <span>Government Tax (0%)</span>
                                <span class="text-gray-800">Rs.0.00</span>
                            </div>
                            <div
                                class="flex justify-between text-base font-black text-gray-900 border-t border-gray-200/80 pt-3">
                                <span>Total Payable</span>
                                <span class="text-orange-600 text-lg">Rs.{{ formatCurrency(bill.amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="border-t border-gray-100 pt-6 mt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400 gap-6">
                        <div class="text-center sm:text-left">
                            <p class="font-bold text-gray-700">Thank you for trusting Health Care services.</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">This is a computer-generated official receipt
                                and requires no physical seal.</p>
                        </div>
                        <div class="text-center sm:text-right min-w-[180px]">
                            <div class="h-8 mb-1"></div>
                            <p class="font-bold text-gray-800 border-t border-gray-300 pt-2">Authorized Signature</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>