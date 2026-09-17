<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane walay props (pagination object)
const props = defineProps({
    prescriptions: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.roles?.some?.(r => r.name === 'admin') ?? false);

// Delete prescription handler
const deletePrescription = (id) => {
    if (confirm('Are you sure you want to delete this prescription?')) {
        router.delete(route('prescriptions.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

const truncate = (text, length = 70) => {
    if (!text) return '';
    return text.length > length ? text.slice(0, length) + '…' : text;
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            <!-- Header & Action Section -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 bg-white sm:bg-transparent p-4 sm:p-0 rounded-2xl border sm:border-0 border-gray-100 shadow-sm sm:shadow-none">
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Prescriptions</h2>
                    <p class="text-xs text-gray-500 mt-0.5 font-medium">View and manage patient prescriptions and
                        medical records.</p>
                </div>

                <Link :href="route('prescriptions.create')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>New Prescription</span>
                </Link>
            </div>

            <!-- Success Alert (Inertia Flash Messages) -->
            <div v-if="$page.props.flash?.success"
                class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold shadow-2xs">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <!-- Main Card Wrapper -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">

                <!-- MOBILE & TABLET VIEW: Clean Streamlined Layout -->
                <div class="block md:hidden divide-y divide-gray-100">
                    <template v-if="prescriptions.data && prescriptions.data.length > 0">
                        <div v-for="prescription in prescriptions.data" :key="prescription.id"
                            class="p-4 space-y-3 hover:bg-gray-50/50 transition">

                            <!-- Top Row: Patient Name & Date -->
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-bold text-gray-900 text-sm truncate">
                                    {{ prescription.patient?.name ?? 'N/A' }}
                                </h4>
                                <span class="text-[11px] text-gray-400 font-semibold shrink-0">
                                    {{ formatDate(prescription.prescribed_date) }}
                                </span>
                            </div>

                            <!-- Doctor & Diagnosis Info -->
                            <div class="space-y-1">
                                <p class="text-[11px] font-bold text-orange-600">
                                    Dr. {{ prescription.doctor?.name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-600 font-medium leading-relaxed">
                                    <span class="text-gray-400 font-bold text-[10px] uppercase">Diagnosis:</span>
                                    {{ truncate(prescription.diagnosis, 75) }}
                                </p>
                            </div>

                            <!-- Action Buttons: 3 Equal Columns Grid Fix -->
                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-50">
                                <Link :href="route('prescriptions.show', prescription.id)"
                                    class="w-full text-center py-2 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs hover:bg-emerald-100 transition">
                                    View
                                </Link>
                                <Link :href="route('prescriptions.edit', prescription.id)"
                                    class="w-full text-center py-2 rounded-xl bg-blue-50 text-blue-700 font-bold text-xs hover:bg-blue-100 transition">
                                    Edit
                                </Link>
                                <button v-if="isAdmin" @click="deletePrescription(prescription.id)" type="button"
                                    class="w-full text-center py-2 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 transition">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="text-center text-gray-400 py-12 px-4">
                            <p class="text-xs font-semibold text-gray-500">No prescriptions found.</p>
                        </div>
                    </template>
                </div>
                <!-- DESKTOP VIEW: Table Layout (md and up) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs font-medium text-gray-600">
                        <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                            <tr>
                                <th class="py-3.5 px-4 lg:px-6 whitespace-nowrap">Patient</th>
                                <th class="py-3.5 px-4 lg:px-6 whitespace-nowrap">Doctor</th>
                                <th class="py-3.5 px-4 lg:px-6">Diagnosis</th>
                                <th class="py-3.5 px-4 lg:px-6 whitespace-nowrap">Date</th>
                                <th class="py-3.5 px-4 lg:px-6 text-right whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template v-if="prescriptions.data && prescriptions.data.length > 0">
                                <tr v-for="prescription in prescriptions.data" :key="prescription.id"
                                    class="hover:bg-gray-50/60 transition">
                                    <td class="py-4 px-4 lg:px-6 font-bold text-gray-900">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <!-- Avatar removed from table too just in case, keeping text clean -->
                                            <span class="truncate max-w-[140px] lg:max-w-none">{{
                                                prescription.patient?.name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 lg:px-6 text-gray-700 font-semibold whitespace-nowrap">
                                        Dr. {{ prescription.doctor?.name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4 lg:px-6 text-gray-500 font-medium max-w-[180px] lg:max-w-xs">
                                        <span class="line-clamp-2">{{ truncate(prescription.diagnosis, 60) }}</span>
                                    </td>
                                    <td class="py-4 px-4 lg:px-6 text-gray-500 font-medium whitespace-nowrap">
                                        {{ formatDate(prescription.prescribed_date) }}
                                    </td>
                                    <td class="py-4 px-4 lg:px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <Link :href="route('prescriptions.show', prescription.id)"
                                                class="p-2 rounded-xl bg-gray-50 text-emerald-600 hover:bg-emerald-50 transition"
                                                title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </Link>
                                            <Link :href="route('prescriptions.edit', prescription.id)"
                                                class="p-2 rounded-xl bg-gray-50 text-blue-600 hover:bg-blue-50 transition"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                            </Link>
                                            <button v-if="isAdmin" @click="deletePrescription(prescription.id)"
                                                type="button"
                                                class="p-2 rounded-xl bg-gray-50 text-rose-600 hover:bg-rose-50 transition"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="5" class="text-center text-gray-400 py-12">
                                        <p class="text-xs font-semibold text-gray-500">No prescriptions found.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Pagination Links -->
            <div class="mt-4 sm:mt-6 flex items-center justify-center gap-1.5 flex-wrap px-2"
                v-if="prescriptions.total > 10">
                <template v-for="(link, key) in prescriptions.links" :key="key">
                    <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                        class="min-w-[36px] h-9 px-3.5 inline-flex items-center justify-center text-xs rounded-xl border transition font-bold shadow-2xs"
                        :class="{
                            'bg-orange-500 text-white border-orange-500 shadow-orange-500/20': link.active,
                            'bg-white text-gray-700 border-gray-200 hover:bg-gray-50': link.url && !link.active,
                            'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed select-none': !link.url
                        }" />
                </template>
            </div>

        </div>
    </AuthenticatedLayout>
</template>