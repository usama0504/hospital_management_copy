<script setup>
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane walay props (bills data, auth info agar required ho)
const props = defineProps({
    bills: Object,
    auth: Object, // Laravel ka auth object permissions/roles check karne ke liye agar pass ho raha ho
});

// Helper function to format date
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

// Delete handler function
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
        <div class="py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">

                <!-- Header Section & Add Button -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Bills List</h2>
                        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Manage and track all hospital billing records</p>
                    </div>

                    <!-- Add Bill Button (Agar user ke pas permission ho) -->
                    <div>
                        <Link :href="route('bills.create')"
                            class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-xl shadow-lg shadow-orange-500/20 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Bill
                        </Link>
                    </div>
                </div>

                <!-- Success Message Alert (Flash messages via page props agar Inertia shared mein hain) -->
                <div v-if="$page.props.flash?.success" class="mb-6 flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold shadow-sm">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Table Card Container -->
                <div class="bg-white border border-gray-100 shadow-xl shadow-gray-100/80 rounded-3xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/70">
                                <tr>
                                    <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Doctor</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Bill Date</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm font-medium text-gray-700">
                                <tr v-for="bill in bills.data" :key="bill.id" class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-semibold text-gray-900">
                                        {{ bill.patient?.name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 font-medium">
                                        Dr. {{ bill.doctor?.name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-900">
                                        ${{ Number(bill.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border"
                                            :class="bill.status.toLowerCase() === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                            {{ bill.status.charAt(0).toUpperCase() + bill.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 text-xs">
                                        {{ formatDate(bill.bill_date) }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-right space-x-2">
                                        
                                        <!-- View Receipt Button -->
                                        <Link :href="route('bills.receipt', bill.id)"
                                            class="inline-flex items-center text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-100 px-3 py-1.5 rounded-lg transition">
                                            Receipt
                                        </Link>

                                        <!-- Edit Button -->
                                        <Link :href="route('bills.edit', bill.id)"
                                            class="inline-flex items-center text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition">
                                            Edit
                                        </Link>

                                        <!-- Delete Button -->
                                        <button @click="deleteBill(bill.id)" type="button"
                                            class="inline-flex items-center text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!bills.data || bills.data.length === 0">
                                    <td colspan="6" class="py-12 px-6 text-center text-gray-400 font-medium text-sm">
                                        No bills found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div v-if="bills.links && bills.links.length > 3" class="py-4 px-6 bg-gray-50/50 border-t border-gray-100 flex justify-center">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in bills.links" :key="index">
                                <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition"
                                    :class="[
                                        link.active ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]" />
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>