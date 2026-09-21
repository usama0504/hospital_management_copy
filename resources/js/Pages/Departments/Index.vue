<script setup>
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    departments: Array,
});

const deleteDepartment = (id) => {
    if (confirm('Are you sure you want to delete this department? Doctors in it will not be deleted, but will lose their department assignment.')) {
        router.delete(route('departments.destroy', id), {
            preserveScroll: true,
        });
    }
};

const initials = (name) => {
    if (!name) return 'D';
    return name.charAt(0).toUpperCase();
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">

            <!-- Header & Action Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight">Departments</h2>
                    <p class="text-[11px] sm:text-xs text-gray-500 mt-1 font-medium">Departments and the doctors
                        assigned to each one.</p>
                </div>

                <Link :href="route('departments.create')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-xl font-semibold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Add Department</span>
                </Link>
            </div>

            <!-- Success Alert -->
            <div v-if="$page.props.flash?.success"
                class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <!-- Empty State -->
            <div v-if="!departments || departments.length === 0"
                class="bg-white border border-gray-100 shadow-sm rounded-2xl py-16 text-center">
                <p class="text-xs font-semibold text-gray-500">No departments found.</p>
            </div>

            <!-- Department Cards (each with its own doctors) -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div v-for="department in departments" :key="department.id"
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden flex flex-col">

                    <!-- Department Header -->
                    <div class="p-4 sm:p-5 border-b border-gray-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-black text-gray-900 truncate">{{ department.name }}</h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0"
                                        :class="department.status ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'">
                                        {{ department.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-1 line-clamp-2">{{ department.description || 'No description' }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <Link :href="route('departments.edit', department.id)"
                                    class="p-2 rounded-lg bg-gray-50 text-blue-600 hover:bg-blue-50 transition"
                                    title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </Link>
                                <button @click="deleteDepartment(department.id)" type="button"
                                    class="p-2 rounded-lg bg-gray-50 text-rose-600 hover:bg-rose-50 transition"
                                    title="Delete">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Doctors in this Department -->
                    <div class="p-4 sm:p-5 flex-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">
                            Doctors ({{ department.doctors?.length ?? 0 }})
                        </p>

                        <div v-if="department.doctors && department.doctors.length > 0" class="space-y-2.5">
                            <div v-for="doctor in department.doctors" :key="doctor.id"
                                class="flex items-center gap-3 bg-gray-50/60 rounded-xl px-3 py-2.5">
                                <div
                                    class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-[11px] shrink-0">
                                    {{ initials(doctor.name) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-gray-900 truncate">Dr. {{ doctor.name }}</p>
                                    <p class="text-[11px] text-gray-500 truncate">{{ doctor.specialization }}</p>
                                </div>
                                <span v-if="doctor.consultation_fee"
                                    class="text-[11px] font-bold text-gray-700 shrink-0">
                                    Rs. {{ Number(doctor.consultation_fee).toLocaleString() }}
                                </span>
                            </div>
                        </div>

                        <div v-else class="text-center py-6">
                            <p class="text-[11px] text-gray-400 font-medium">No doctors assigned to this department yet.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>