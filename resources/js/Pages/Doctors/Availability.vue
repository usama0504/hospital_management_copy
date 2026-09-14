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

// 👇 Naya function — "14:00:00" ya "14:00" ko "2:00 PM" mein convert karta hai
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
        <div class="max-w-4xl mx-auto space-y-6 p-6">

            <!-- Page Header -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-gray-900">Manage Availability</h1>
                    <p class="text-xs text-gray-400 mt-1" v-if="isAdmin">
                        Managing schedule for <span class="font-bold text-gray-600">{{ doctorName }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1" v-else>
                        Set your working days and time slots for patient appointments.
                    </p>
                </div>

                <Link href="/doctors"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold text-xs shadow-sm hover:bg-gray-50 hover:text-orange-600 hover:border-orange-200 transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Back to Doctors
                </Link>
            </div>
            <!-- Success Message -->
            <div v-if="$page.props.flash.success"
                class="p-4 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                {{ $page.props.flash.success }}
            </div>

            <!-- Add Availability Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Add New Time Slot</h2>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <!-- Admin ke case mein doctor_id fixed hidden field ke sath jayega -->
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
                            class="px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20 disabled:opacity-50">
                            Add Slot
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Slots List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Your Active Slots</h2>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-100">
                        {{ availabilities.length }} Slots Active
                    </span>
                </div>

                <div class="p-6">
                    <div v-if="availabilities.length === 0"
                        class="text-center py-10 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-xs text-gray-400 font-medium">No availability slots added yet.</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-4 rounded-l-xl">Day of Week</th>
                                    <th class="py-3.5 px-4">Timing Slot</th>
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
                                        {{ formatTime(slot.start_time) }} to {{ formatTime(slot.end_time) }}
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button @click="deleteSlot(slot.id)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 font-bold hover:bg-rose-100 transition shadow-sm">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>