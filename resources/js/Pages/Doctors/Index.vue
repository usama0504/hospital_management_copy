<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    doctors: Object,
});
// Script section mein yeh add karein agar user ke paas doctor object hai
const currentDoctorId = computed(() => page.props.auth?.doctor?.id || authUser.value.id);

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
                                class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/25 hover:bg-orange-600 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5"
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-6">Name</th>
                                    <th class="py-3.5 px-6">Email</th>
                                    <th class="py-3.5 px-6">Phone</th>
                                    <th class="py-3.5 px-6">Specialization</th>
                                    <th class="py-3.5 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <template v-for="doctor in doctors.data" :key="doctor.id">
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-gray-900">Dr. {{ doctor.name }}</td>
                                        <td class="py-4 px-6 text-gray-500">{{ doctor.email }}</td>
                                        <td class="py-4 px-6 text-gray-500">{{ doctor.phone ?? 'N/A' }}</td>
                                        <td class="py-4 px-6">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-100">
                                                {{ doctor.specialization }}
                                            </span>
                                        </td>

                                        <td class="py-4 px-6 text-right space-x-2">
                                            <!-- Admin ke liye Availability, Edit aur Delete buttons -->
                                            <template v-if="hasRole('admin')">
                                                <Link :href="`/doctor/availability?doctor_id=${doctor.id}`"
                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg bg-orange-50 text-orange-600 font-bold hover:bg-orange-100 transition"
                                                    title="Manage Doctor Availability">
                                                    Availability
                                                </Link>

                                                <Link :href="`/doctors/${doctor.id}/edit`"
                                                    class="font-bold text-gray-600 hover:text-orange-600 transition">
                                                    Edit</Link>

                                                <button @click="deleteDoctor(doctor.id)" type="button"
                                                    class="font-bold text-rose-500 hover:text-rose-700 transition">Delete</button>
                                            </template>
                                        </td>
                                    </tr>
                                </template>

                                <template v-if="!doctors.data || doctors.data.length === 0">
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-gray-400 font-medium text-xs">
                                            No doctors available.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

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