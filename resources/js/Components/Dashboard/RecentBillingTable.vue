<script setup>
defineProps({
    recentBills: Array,
    totalEarnings: [Number, String],
    formatCurrency: Function,
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800">Recent Billing & Payments</h3>
            <span class="text-xs text-rose-500 font-bold">Total Earnings: ${{ formatCurrency(totalEarnings || 0) }}</span>
        </div>
        <div class="overflow-x-auto">
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
                                ${{ formatCurrency(bill.amount) }}
                            </td>
                            <td class="py-4 px-6 text-xs">
                                <span v-if="bill.status && bill.status.toLowerCase() === 'paid'" class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full font-bold">Paid</span>
                                <span v-else class="px-2.5 py-1 bg-amber-50 text-amber-600 rounded-full font-bold capitalize">{{ bill.status }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-500">
                                {{ bill.created_at ? new Date(bill.created_at).toLocaleDateString() : 'N/A' }}
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