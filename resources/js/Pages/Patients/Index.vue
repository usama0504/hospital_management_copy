<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    patients: Object,
    filters: Object,
});

const page = usePage();
const auth = computed(() => page.props.auth?.user || {});

// Search box with debounce
const search = ref(props.filters?.search ?? '');
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('patients.index'), { search: value || undefined }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 400);
});

const clearSearch = () => {
    search.value = '';
};

// Permissions
const canManagePatients = computed(() => {
    return auth.value.permissions?.includes('manage patients') ||
        auth.value.roles?.some(r => r.name === 'admin' || r.name === 'receptionist');
});

const isAdmin = computed(() => {
    return auth.value.roles?.some(r => r.name === 'admin');
});

const deleteForm = useForm({});

const deletePatient = (id) => {
    if (confirm('Are you sure you want to delete this patient?')) {
        deleteForm.delete(route('patients.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Patients List</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Manage and monitor all registered hospital
                            patients.</p>
                    </div>

                    <!-- Add Patient Button -->
                    <div v-if="canManagePatients" class="w-full sm:w-auto">
                        <Link :href="route('patients.create')"
                            class="w-full sm:w-auto inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Add Patient
                        </Link>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input v-model="search" type="text" placeholder="Search by name, email or phone..."
                        class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-9 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition shadow-sm" />
                    <button v-if="search" @click="clearSearch" type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Success Alert -->
                <div v-if="$page.props.flash?.success"
                    class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $page.props.flash.success }}</span>
                </div>

                <!-- Patients Data Container -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">

                    <!-- Mobile View: Card Layout -->
                    <div class="block md:hidden divide-y divide-gray-100">
                        <template v-if="patients?.data && patients.data.length > 0">
                            <div v-for="patient in patients.data" :key="patient.id"
                                class="p-4 space-y-3 hover:bg-gray-50/50 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <!-- Bara naam ab wrap ho kar aayega, cut nahi hoga -->
                                    <h3 class="font-black text-gray-900 text-sm break-words leading-snug flex-1">{{
                                        patient.name }}</h3>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg shrink-0">
                                        DOB: {{ formatDate(patient.dob) }}
                                    </span>
                                </div>

                                <div class="space-y-1 text-xs text-gray-600 pt-1 border-t border-gray-50">
                                    <p class="break-all"><span class="font-bold text-gray-800">Email:</span> {{
                                        patient.email }}</p>
                                    <p><span class="font-bold text-gray-800">Phone:</span> {{ patient.phone ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-2.5 border-t border-gray-50">
                                    <Link v-if="canManagePatients" :href="route('patients.edit', patient.id)"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-blue-600 hover:bg-blue-50 transition shadow-2xs"
                                        title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                    </Link>
                                    <button v-if="isAdmin" @click="deletePatient(patient.id)" type="button"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-rose-600 hover:bg-rose-50 transition shadow-2xs"
                                        title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="py-12 text-center text-gray-400 font-medium text-xs">
                                No patients found.
                            </div>
                        </template>
                    </div>

                    <!-- Desktop View: Table Layout -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-6">Name</th>
                                    <th class="py-3.5 px-6">Email</th>
                                    <th class="py-3.5 px-6">Phone</th>
                                    <th class="py-3.5 px-6">Date of Birth</th>
                                    <th class="py-3.5 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <template v-if="patients?.data && patients.data.length > 0">
                                    <tr v-for="patient in patients.data" :key="patient.id"
                                        class="hover:bg-gray-50/50 transition">
                                        <!-- Table mein lamba naam ab wrap hoga aur table cell expand karega -->
                                        <td class="py-4 px-6 font-bold text-gray-900 max-w-xs break-words">
                                            {{ patient.name }}
                                        </td>
                                        <td class="py-4 px-6 text-gray-500 max-w-xs break-all">{{ patient.email }}</td>
                                        <td class="py-4 px-6 text-gray-500 whitespace-nowrap">{{ patient.phone ?? 'N/A'
                                            }}</td>
                                        <td class="py-4 px-6 text-gray-500 whitespace-nowrap">
                                            {{ formatDate(patient.dob) }}
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                <Link v-if="canManagePatients"
                                                    :href="route('patients.edit', patient.id)"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-blue-600 hover:bg-blue-50 transition shadow-2xs"
                                                    title="Edit">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                                    </svg>
                                                </Link>
                                                <button v-if="isAdmin" @click="deletePatient(patient.id)" type="button"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-rose-600 hover:bg-rose-50 transition shadow-2xs"
                                                    title="Delete">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
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
                                        <td colspan="5" class="py-12 text-center text-gray-400 font-medium text-xs">
                                            No patients found.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="patients?.links && patients.last_page > 1"
                        class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
                        <div class="flex gap-1 flex-wrap justify-center">
                            <component :is="link.url ? Link : 'span'" v-for="(link, index) in patients.links"
                                :key="index" :href="link.url" v-html="link.label"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition" :class="{
                                    'bg-orange-500 text-white border-orange-500': link.active,
                                    'bg-white text-gray-700 border-gray-200 hover:bg-gray-100': link.url && !link.active,
                                    'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': !link.url
                                }" />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>