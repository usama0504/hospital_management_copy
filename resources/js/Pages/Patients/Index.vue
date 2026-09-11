<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Controller se ane walay props
const props = defineProps({
    patients: Object, // Paginator object from Laravel
});

const page = usePage();

// Helper to check user permissions/roles safely from page props (auth object)
const auth = computed(() => page.props.auth?.user || {});
const canManagePatients = computed(() => {
    // Agar aapke paas permissions array ya roles array hain auth.user mein
    return auth.value.permissions?.includes('manage patients') || auth.value.roles?.some(r => r.name === 'admin');
});
const isAdmin = computed(() => {
    return auth.value.roles?.some(r => r.name === 'admin');
});

// Form helper for deleting a patient
const deleteForm = useForm({});

const deletePatient = (id) => {
    if (confirm('Are you sure you want to delete this patient?')) {
        deleteForm.delete(route('patients.destroy', id), {
            preserveScroll: true,
        });
    }
};

// Helper for first letter avatar
const getInitial = (name) => {
    return name ? name.charAt(0).toUpperCase() : 'P';
};

// Date formatter helper
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
                
                <!-- Header Section with Action Button -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Patients List</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Manage and monitor all registered hospital patients.</p>
                    </div>
                    
                    <!-- Permission check equivalent to @can('manage patients') -->
                    <div v-if="canManagePatients">
                        <Link :href="route('patients.create')" 
                            class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Add Patient
                        </Link>
                    </div>
                </div>

                <!-- Success Message Alert -->
                <div v-if="$page.props.flash?.success" class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $page.props.flash.success }}</span>
                </div>

                <!-- Table Card Container -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
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
                                    <tr v-for="patient in patients.data" :key="patient.id" class="hover:bg-gray-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-gray-900">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                                    {{ getInitial(patient.name) }}
                                                </div>
                                                <span>{{ patient.name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-gray-500">{{ patient.email }}</td>
                                        <td class="py-4 px-6 text-gray-500">{{ patient.phone ?? 'N/A' }}</td>
                                        <td class="py-4 px-6 text-gray-500">
                                            {{ formatDate(patient.dob) }}
                                        </td>
                                        <td class="py-4 px-6 text-right space-x-3 whitespace-nowrap">
                                            <Link v-if="canManagePatients" :href="route('patients.edit', patient.id)" class="font-bold text-gray-600 hover:text-orange-600 transition">
                                                Edit
                                            </Link>
                                            
                                            <button v-if="isAdmin" @click="deletePatient(patient.id)" type="button" class="font-bold text-rose-500 hover:text-rose-700 transition">
                                                Delete
                                            </button>
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

                    <!-- Pagination Container -->
                    <div v-if="patients?.links && patients.last_page > 1" class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
                        <div class="flex gap-1 flex-wrap justify-center">
                            <component :is="link.url ? Link : 'span'"
                                v-for="(link, index) in patients.links" 
                                :key="index"
                                :href="link.url"
                                v-html="link.label"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition"
                                :class="{
                                    'bg-orange-500 text-white border-orange-500': link.active,
                                    'bg-white text-gray-700 border-gray-200 hover:bg-gray-100': link.url && !link.active,
                                    'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': !link.url
                                }"
                            />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>