<script setup>
import { Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    availabilities: Array,
    targetDoctorId: [Number, String],
    doctorName: String,
    isAdmin: Boolean
});

const form = useForm({
    doctor_id: props.isAdmin ? props.targetDoctorId : undefined,
    day_of_week: 'Monday',
    start_time: '',
    end_time: '',
});

const submit = () => {
    form.post('/doctor/availability', {
        preserveScroll: true,
        onSuccess: () => form.reset('start_time', 'end_time'),
    });
};

const deleteSlot = (id) => {
    if (confirm('Are you sure you want to delete this slot?')) {
        router.delete(`/doctor/availability/${id}`, {
            preserveScroll: true,
        });
    }
};

const toggleSlot = (id) => { router.patch(`/doctor/availability/${id}/toggle`, {}, { preserveScroll: true, }); };

const formatTime = (time) => {
    if (!time) return '';

    const [hourStr, minuteStr] = time.split(':');
    let hour = parseInt(hourStr, 10);
    const minute = minuteStr ?? '00';
    const suffix = hour >= 12 ? 'PM' : 'AM';

    hour = hour % 12;
    if (hour === 0) hour = 12;

    return `${hour}:${minute} ${suffix}`;
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-6 p-4 sm:p-6">

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900">Manage Availability</h1>
                    <p class="text-xs text-gray-400 mt-1" v-if="isAdmin">
                        Managing schedule for <span class="font-bold text-gray-600">{{ doctorName }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1" v-else>
                        Set your working days and time slots for patient appointments.
                    </p>
                </div>

                <Link :href="route('doctors.index')"
                    class="inline-flex items-center justify-center gap-1.5 bg-orange-500 text-white px-6 py-3 rounded-xl font-bold text-xs hover:bg-orange-600 transition shadow-sm shadow-orange-500/20 w-full sm:w-auto">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18">
                        </path>
                    </svg>
                    Back Doctor
                </Link>
            </div>

            <!-- Success Message -->
            <div v-if="$page.props.flash.success"
                class="p-4 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                {{ $page.props.flash.success }}
            </div>

            <!-- Add Availability Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Add New Time Slot</h2>
                </div>

                <form @submit.prevent="submit" class="p-4 sm:p-6 space-y-4">
                    <input v-if="isAdmin" type="hidden" v-model="form.doctor_id" />

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Day of Week -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Day of Week</label>
                            <select v-model="form.day_of_week"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                                required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                            <div v-if="form.errors.day_of_week" class="text-red-500 text-xs mt-1">{{
                                form.errors.day_of_week }}</div>
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Start Time</label>
                            <input type="time" v-model="form.start_time"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                                required>
                            <div v-if="form.errors.start_time" class="text-red-500 text-xs mt-1">{{
                                form.errors.start_time }}</div>
                        </div>

                        <!-- End Time -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">End Time</label>
                            <input type="time" v-model="form.end_time"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm"
                                required>
                            <div v-if="form.errors.end_time" class="text-red-500 text-xs mt-1">{{ form.errors.end_time
                            }}</div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="form.processing"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20 disabled:opacity-50">
                            Add Slot
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Slots List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Your Active Slots</h2>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-100">
                        {{ availabilities.length }} Slots Active
                    </span>
                </div>

                <div class="p-4 sm:p-6">
                    <div v-if="availabilities.length === 0"
                        class="text-center py-10 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-xs text-gray-400 font-medium">No availability slots added yet.</p>
                    </div>

                    <template v-else>
                        <!-- Mobile View: Cards layout -->
                        <div class="block sm:hidden space-y-3">
                            <div v-for="slot in availabilities" :key="slot.id"
                                class="p-4 rounded-xl bg-gray-50/75 border border-gray-100 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-gray-800 border border-gray-200">
                                        {{ slot.day_of_week }}
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="slot.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-200 text-gray-600'">
                                        {{ slot.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="text-xs font-bold text-gray-700">
                                    🕒 {{ formatTime(slot.start_time) }} - {{ formatTime(slot.end_time) }}
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-2 ">
                                    <!-- Toggle Button -->
                                    <button @click="toggleSlot(slot.id)" type="button"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg transition shadow-2xs"
                                        :class="slot.is_active ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'"
                                        :title="slot.is_active ? 'Deactivate' : 'Activate'">
                                        <svg v-if="slot.is_active" class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                        </svg>
                                        <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button @click="deleteSlot(slot.id)" type="button"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition shadow-2xs"
                                        title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
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
                                        <th class="py-3.5 px-4 rounded-l-xl">Day of Week</th>
                                        <th class="py-3.5 px-4">Timing Slot</th>
                                        <th class="py-3.5 px-4">Status</th>
                                        <th class="py-3.5 px-4 text-right rounded-r-xl">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-xs font-medium text-gray-700">
                                    <tr v-for="slot in availabilities" :key="slot.id"
                                        class="hover:bg-orange-50/30 transition">
                                        <td class="py-4 px-4">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-800">
                                                {{ slot.day_of_week }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 font-semibold text-gray-600">
                                            {{ formatTime(slot.start_time) }} - {{ formatTime(slot.end_time) }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold"
                                                :class="slot.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500'">
                                                {{ slot.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                <!-- Toggle Button -->
                                                <button @click="toggleSlot(slot.id)" type="button"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg transition shadow-2xs"
                                                    :class="slot.is_active ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'"
                                                    :title="slot.is_active ? 'Deactivate' : 'Activate'">
                                                    <svg v-if="slot.is_active" class="w-3.5 h-3.5" fill="none"
                                                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                                    </svg>
                                                    <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button @click="deleteSlot(slot.id)" type="button"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition shadow-2xs"
                                                    title="Delete">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
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
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>