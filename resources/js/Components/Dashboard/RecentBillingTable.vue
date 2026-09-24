<script setup>
defineProps({
    recentBills: Array,
    totalEarnings: [Number, String],
    formatCurrency: Function,
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
            <h3 class="text-sm sm:text-base font-bold text-gray-800">Recent Billing & Payments</h3>
            <span class="text-xs text-rose-500 font-bold">Total Earnings:Rs.{{ formatCurrency(totalEarnings || 0)
            }}</span>
        </div>
        <!-- Mobile: card list -->
        <div class="sm:hidden divide-y divide-gray-100">
            <template v-if="recentBills && recentBills.length > 0">
                <div v-for="bill in recentBills" :key="bill.id"
                    class="px-4 py-3.5 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ bill.patient?.name || 'Walk-in Patient' }}</p>
                        <p class="text-[11px] text-gray-500 font-medium">
                            {{ bill.bill_date ? new Date(bill.bill_date).toLocaleDateString('en-GB', {
                                day: '2-digit', month: 'short', year: 'numeric'
                            }) : 'N/A' }}
                        </p>
                    </div>
                    <div class="text-right shrink-0 space-y-1">
                        <p class="text-sm font-extrabold text-gray-800">Rs.{{ formatCurrency(bill.amount) }}</p>
                        <span v-if="bill.status && bill.status.toLowerCase() === 'paid'"
                            class="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-bold">Paid</span>
                        <span v-else
                            class="inline-block px-2 py-0.5 bg-amber-50 text-amber-600 rounded-full text-[10px] font-bold capitalize">{{ bill.status }}</span>
                    </div>
                </div>
            </template>
            <div v-else class="px-4 py-6 text-center text-gray-400 text-xs">
                No billing records found in database.
            </div>
        </div>

        <!-- Tablet / Desktop: table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/60 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="py-3.5 px-6 font-semibold">Patient Name</th>
                        <th class="py-3.5 px-6 font-semibold">Amount</th>
                        <th class="py-3.5 px-6 font-semibold">Status</th>
                        <th class="py-3.5 px-6 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    <template v-if="recentBills && recentBills.length > 0">
                        <tr v-for="bill in recentBills" :key="bill.id" class="hover:bg-gray-50/40 transition">
                            <td class="py-4 px-6 font-bold text-gray-900">
                                {{ bill.patient?.name || 'Walk-in Patient' }}
                            </td>
                            <td class="py-4 px-6 font-extrabold text-gray-800">
                                Rs.{{ formatCurrency(bill.amount) }}
                            </td>
                            <td class="py-4 px-6 text-xs">
                                <span v-if="bill.status && bill.status.toLowerCase() === 'paid'"
                                    class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full font-bold">Paid</span>
                                <span v-else
                                    class="px-2.5 py-1 bg-amber-50 text-amber-600 rounded-full font-bold capitalize">{{
                                        bill.status }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-500">
                                {{ bill.bill_date ? new Date(bill.bill_date).toLocaleDateString('en-GB', {
                                    day:
                                        '2-digit', month: 'short', year: 'numeric'
                                }) : 'N/A' }}
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-400 text-xs">
                                No billing records found in database.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>