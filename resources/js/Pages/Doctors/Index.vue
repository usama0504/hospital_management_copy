<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    doctors: Object,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});

// Flexible role check jo objects aur strings dono ko handle karega
const hasRole = (roleName) => {
    const roles = authUser.value.roles || [];
    return roles.some(role => {
        if (typeof role === 'string') return role === roleName;
        if (typeof role === 'object' && role !== null) return role.name === roleName;
        return false;
    });
};

const form = useForm({});

const deleteDoctor = (id) => {
    if (confirm('Are you sure you want to delete this doctor?')) {
        form.delete(`/doctors/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header Section with Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Doctors List</h2>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Manage all hospital doctors and their
                            specializations.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <template v-if="hasRole('doctor')">
                            <Link :href="`/doctor/availability/${authUser.doctor_id || authUser.id}`"
                                class="inline-flex items-center justify-center gap-1.5 bg-orange-500 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-sm shadow-orange-500/20 w-full sm:w-auto">
                                Manage Availability
                            </Link>
                        </template>

                        <!-- Agar Admin hai -->
                        <template v-if="hasRole('admin')">
                            <Link href="/doctors/create"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition shrink-0">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Doctor
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Success Message Alert -->
                <div v-if="$page.props.flash?.success"
                    class="mb-6 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $page.props.flash.success }}</span>
                </div>

                <!-- Table Card Container -->
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">

                    <div v-if="!doctors.data || doctors.data.length === 0"
                        class="py-12 text-center text-gray-400 font-medium text-xs">
                        No doctors available.
                    </div>

                    <template v-else>
                        <!-- Mobile View: Cards layout -->
                        <div class="block sm:hidden divide-y divide-gray-100">
                            <div v-for="doctor in doctors.data" :key="doctor.id" class="p-4 space-y-3 bg-white">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-bold text-sm text-gray-900">Dr. {{ doctor.name }}</h3>
                                        <p class="text-xs text-gray-500">{{ doctor.email }}</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-100">
                                        {{ doctor.department?.name ?? '—' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-600 pt-1">
                                    <span>📞 {{ doctor.phone ?? 'N/A' }}</span>
                                    <span class="font-bold text-gray-900">
                                        {{ doctor.consultation_fee ? 'Rs. ' +
                                            Number(doctor.consultation_fee).toLocaleString() : '—' }}
                                    </span>
                                </div>

                                <!-- Mobile Actions -->
                                <div v-if="hasRole('admin')"
                                    class="flex items-center justify-end gap-1.5 pt-2 border-t border-gray-100">
                                    <!-- Availability Icon Button -->
                                    <Link :href="`/doctor/availability?doctor_id=${doctor.id}`"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-orange-600 hover:bg-orange-50 transition shadow-2xs"
                                        title="Manage Doctor Availability">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </Link>

                                    <!-- Edit Icon Button -->
                                    <Link :href="`/doctors/${doctor.id}/edit`"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-blue-600 hover:bg-blue-50 transition shadow-2xs"
                                        title="Edit Doctor">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                    </Link>

                                    <!-- Delete Icon Button -->
                                    <button @click="deleteDoctor(doctor.id)" type="button"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-rose-600 hover:bg-rose-50 transition shadow-2xs"
                                        title="Delete Doctor">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop View: Table layout -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-left">
                                <thead
                                    class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3.5 px-6">Name</th>
                                        <th class="py-3.5 px-6">Email</th>
                                        <th class="py-3.5 px-6">Phone</th>
                                        <th class="py-3.5 px-6">Department</th>
                                        <th class="py-3.5 px-6">Fee</th>
                                        <th class="py-3.5 px-6 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                    <tr v-for="doctor in doctors.data" :key="doctor.id"
                                        class="hover:bg-gray-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-gray-900">Dr. {{ doctor.name }}</td>
                                        <td class="py-4 px-6 text-gray-500">{{ doctor.email }}</td>
                                        <td class="py-4 px-6 text-gray-500">{{ doctor.phone ?? 'N/A' }}</td>
                                        <td class="py-4 px-6 text-gray-500">
                                            {{ doctor.department?.name ?? '—' }}
                                        </td>
                                        <td class="py-4 px-6 text-gray-700 font-semibold">
                                            {{ doctor.consultation_fee ? 'Rs. ' +
                                                Number(doctor.consultation_fee).toLocaleString() : '—' }}
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div v-if="hasRole('admin')"
                                                class="inline-flex items-center justify-end gap-1.5">
                                                <!-- Availability Icon Button -->
                                                <Link :href="`/doctor/availability?doctor_id=${doctor.id}`"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-orange-600 hover:bg-orange-50 transition shadow-2xs"
                                                    title="Manage Doctor Availability">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </Link>

                                                <!-- Edit Icon Button -->
                                                <Link :href="`/doctors/${doctor.id}/edit`"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-blue-600 hover:bg-blue-50 transition shadow-2xs"
                                                    title="Edit Doctor">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                                    </svg>
                                                </Link>

                                                <!-- Delete Icon Button -->
                                                <button @click="deleteDoctor(doctor.id)" type="button"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-rose-600 hover:bg-rose-50 transition shadow-2xs"
                                                    title="Delete Doctor">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <!-- Pagination Container -->
                    <div v-if="doctors.links && doctors.links.length > 3"
                        class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in doctors.links" :key="index">
                                <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition" :class="{
                                        'bg-orange-500 text-white border-orange-500 shadow-sm shadow-orange-500/20': link.active,
                                        'bg-white text-gray-700 border-gray-200 hover:bg-gray-50': link.url && !link.active,
                                        'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': !link.url
                                    }" />
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>