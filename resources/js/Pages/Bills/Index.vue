<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    bills: Object,
    auth: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');
let searchTimeout = null;

const applyFilters = () => {
    router.get(route('bills.index'), {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['bills'],
    });
};

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch(status, applyFilters);

const clearSearch = () => {
    search.value = '';
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

const deleteBill = (id) => {
    if (confirm('Are you sure you want to delete this bill?')) {
        router.delete(route('bills.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 px-3 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">

                <!-- Header Section & Add Button -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Bills List</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Manage and track all hospital billing
                            records</p>
                    </div>

                    <div>
                        <Link :href="route('bills.create')"
                            class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-orange-500/20 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Bill
                        </Link>
                    </div>
                </div>

                <!-- Search + Status Filter -->
                <div class="flex flex-col sm:flex-row gap-2.5 mb-5">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input v-model="search" type="text" placeholder="Search by patient name..."
                            class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-9 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition shadow-sm" />
                        <button v-if="search" @click="clearSearch" type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <select v-model="status"
                        class="bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition shadow-sm sm:w-44">
                        <option value="">All Statuses</option>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>

                <!-- Success Message Alert -->
                <div v-if="$page.props.flash?.success"
                    class="mb-5 flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                    {{ $page.props.flash.success }}
                </div>

                <!-- ================= MOBILE CARDS VIEW ================= -->
                <div class="block sm:hidden space-y-3">
                    <div v-for="bill in bills.data" :key="bill.id"
                        class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-900">{{ bill.patient?.name ?? 'N/A' }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                :class="bill.status.toLowerCase() === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                {{ bill.status.charAt(0).toUpperCase() + bill.status.slice(1) }}
                            </span>
                        </div>
                        <div class="text-[11px] text-gray-500 mb-3 flex items-center gap-2">
                            <span>Dr. {{ bill.doctor?.name ?? 'N/A' }}</span>
                            <span class="text-gray-300">•</span>
                            <span>{{ formatDate(bill.bill_date) }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2.5 border-t border-gray-50">
                            <span class="text-xs font-extrabold text-gray-900">
                                ${{ Number(bill.amount).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                maximumFractionDigits: 2 }) }}
                            </span>
                            <div class="flex items-center space-x-1.5">
                                <!-- Receipt -->
                                <Link :href="route('bills.receipt', bill.id)" title="View Receipt"
                                    class="inline-flex items-center justify-center p-1.5 text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </Link>
                                <!-- Edit -->
                                <Link :href="route('bills.edit', bill.id)" title="Edit Bill"
                                    class="inline-flex items-center justify-center p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </Link>
                                <!-- Delete -->
                                <button @click="deleteBill(bill.id)" type="button" title="Delete Bill"
                                    class="inline-flex items-center justify-center p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-if="!bills.data || bills.data.length === 0"
                        class="bg-white border border-gray-100 rounded-2xl p-8 text-center text-gray-400 text-xs font-medium">
                        No bills found.
                    </div>
                </div>

                <!-- ================= DESKTOP TABLE VIEW ================= -->
                <div
                    class="hidden sm:block bg-white border border-gray-100 shadow-xl shadow-gray-100/80 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/70">
                                <tr>
                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        Patient</th>
                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        Doctor</th>
                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        Bill Date</th>
                                    <th
                                        class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <tr v-for="bill in bills.data" :key="bill.id" class="hover:bg-gray-50/50 transition">
                                    <td class="py-3 px-4 font-semibold text-gray-900">
                                        {{ bill.patient?.name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 font-medium">
                                        Dr. {{ bill.doctor?.name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-gray-900">
                                        ${{ Number(bill.amount).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                        maximumFractionDigits: 2 }) }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                            :class="bill.status.toLowerCase() === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                            {{ bill.status.charAt(0).toUpperCase() + bill.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-500 text-[11px]">
                                        {{ formatDate(bill.bill_date) }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-right space-x-1.5">
                                        <!-- Receipt Button -->
                                        <Link :href="route('bills.receipt', bill.id)" title="View Receipt"
                                            class="inline-flex items-center justify-center p-1.5 text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </Link>
                                        <!-- Edit Button -->
                                        <Link :href="route('bills.edit', bill.id)" title="Edit Bill"
                                            class="inline-flex items-center justify-center p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </Link>
                                        <!-- Delete Button -->
                                        <button @click="deleteBill(bill.id)" type="button" title="Delete Bill"
                                            class="inline-flex items-center justify-center p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!bills.data || bills.data.length === 0">
                                    <td colspan="6" class="py-10 px-4 text-center text-gray-400 font-medium text-xs">
                                        No bills found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div v-if="bills.links && bills.links.length > 3"
                    class="mt-4 py-3 px-4 bg-white sm:bg-gray-50/50 border border-gray-100 rounded-2xl flex justify-center shadow-sm">
                    <div class="flex flex-wrap gap-1">
                        <template v-for="(link, index) in bills.links" :key="index">
                            <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                                class="px-2.5 py-1 text-[11px] font-bold rounded-lg border transition" :class="[
                                    link.active ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]" />
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>