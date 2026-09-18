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

// Search box: URL query string ke sath sync, debounced taake har keystroke par request na jaye
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

// Admin aur Receptionist dono patients ko manage (Add/Edit) kar sakte hain
const canManagePatients = computed(() => {
    return auth.value.permissions?.includes('manage patients') ||
        auth.value.roles?.some(r => r.name === 'admin' || r.name === 'receptionist');
});

// Sirf Admin hi delete kar sakta hai
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
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-GB', options);
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Patients List</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Manage and monitor all registered hospital
                            patients.</p>
                    </div>

                    <!-- Add Patient Button -->
                    <div v-if="canManagePatients">
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

                    <!-- Mobile View: Card Layout with Clear Labels -->
                    <div class="block md:hidden divide-y divide-gray-100">
                        <template v-if="patients?.data && patients.data.length > 0">
                            <div v-for="patient in patients.data" :key="patient.id"
                                class="p-4 space-y-3 hover:bg-gray-50/50 transition">
                                <div class="flex items-start justify-between">
                                    <h3 class="font-black text-gray-900 text-sm">{{ patient.name }}</h3>
                                    <span
                                        class="text-[11px] font-semibold bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full">
                                        DOB: {{ formatDate(patient.dob) }}
                                    </span>
                                </div>

                                <div class="space-y-1 text-xs text-gray-600 pt-1 border-t border-gray-50">
                                    <p><span class="font-bold text-gray-800">Email:</span> {{ patient.email }}</p>
                                    <p><span class="font-bold text-gray-800">Phone:</span> {{ patient.phone ?? 'N/A' }}
                                    </p>
                                </div>

                                <!-- Actions for Mobile (Centered & Equal Size) -->
                                <div class="flex items-center justify-center gap-2.5 pt-2.5 border-t border-gray-50">
                                    <Link v-if="canManagePatients" :href="route('patients.edit', patient.id)"
                                        class="w-28 inline-flex items-center justify-center text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 py-2 rounded-xl transition shadow-sm">
                                        Edit
                                    </Link>
                                    <button v-if="isAdmin" @click="deletePatient(patient.id)" type="button"
                                        class="w-28 inline-flex items-center justify-center text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 py-2 rounded-xl transition shadow-sm">
                                        Delete
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

                    <!-- Desktop View: Table Layout (Centered & Equal Size Actions) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-6">Name</th>
                                    <th class="py-3.5 px-6">Email</th>
                                    <th class="py-3.5 px-6">Phone</th>
                                    <th class="py-3.5 px-6">Date of Birth</th>
                                    <th class="py-3.5 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <template v-if="patients?.data && patients.data.length > 0">
                                    <tr v-for="patient in patients.data" :key="patient.id"
                                        class="hover:bg-gray-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-gray-900">
                                            {{ patient.name }}
                                        </td>
                                        <td class="py-4 px-6 text-gray-500">{{ patient.email }}</td>
                                        <td class="py-4 px-6 text-gray-500">{{ patient.phone ?? 'N/A' }}</td>
                                        <td class="py-4 px-6 text-gray-500">
                                            {{ formatDate(patient.dob) }}
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-2">
                                                <Link v-if="canManagePatients"
                                                    :href="route('patients.edit', patient.id)"
                                                    class="w-24 inline-flex items-center justify-center text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 py-2 rounded-xl transition shadow-sm">
                                                    Edit
                                                </Link>
                                                <button v-if="isAdmin" @click="deletePatient(patient.id)" type="button"
                                                    class="w-24 inline-flex items-center justify-center text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 py-2 rounded-xl transition shadow-sm">
                                                    Delete
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