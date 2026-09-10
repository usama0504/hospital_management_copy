<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Controller se ane walay props (availabilities list aur optional selected doctor info agar admin view kar raha ho)
const props = defineProps({
    availabilities: Array,
    doctor: Object, // Optional: agar admin kisi specific doctor ki availability manage kar raha hai
});

const page = usePage();

// Form helper for adding a new availability slot
const form = useForm({
    doctor_id: props.doctor ? props.doctor.id : null,
    day_of_week: '',
    start_time: '',
    end_time: '',
});

const submit = () => {
    form.post(route('doctor.availability.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('day_of_week', 'start_time', 'end_time'),
    });
};

// Form helper for deleting a slot
const deleteForm = useForm({});

const deleteSlot = (id) => {
    if (confirm('Are you sure you want to delete this slot?')) {
        deleteForm.delete(route('doctor.availability.destroy', id), {
            preserveScroll: true,
        });
    }
};

// Helper to format time (e.g. 14:00:00 to 02:00 PM)
const formatTime = (timeString) => {
    if (!timeString) return '';
    const [hours, minutes] = timeString.split(':');
    const h = parseInt(hours, 10);
    const period = h >= 12 ? 'PM' : 'AM';
    const formattedHours = h % 12 || 12;
    return `${formattedHours}:${minutes} ${period}`;
};
</script>

<template>
    <div class="max-w-4xl mx-auto space-y-6 py-4">

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Manage Availability</h1>
                <p class="text-xs text-gray-500 font-medium mt-0.5">
                    <span v-if="doctor">Setting schedule for Dr. {{ doctor.name }}</span>
                    <span v-else>Set your working days and time slots for patient appointments.</span>
                </p>
            </div>
            <!-- Back Button to Doctors Index Page -->
            <div>
                <Link :href="route('doctors.index')"
                    class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-semibold text-xs hover:bg-gray-200 transition">
                    &larr; Back to Doctors List
                </Link>
            </div>
        </div>

        <!-- Success / Error Messages via Flash -->
        <div v-if="$page.props.flash?.success" class="p-4 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ $page.props.flash.success }}</span>
        </div>

        <div v-if="$page.props.flash?.error" class="p-4 rounded-xl bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>{{ $page.props.flash.error }}</span>
        </div>

        <!-- Validation Errors Alert -->
        <div v-if="Object.keys(form.errors).length > 0" class="p-4 rounded-xl bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-100">
            <span class="font-bold">Please fix the following errors:</span>
            <ul class="list-disc list-inside space-y-1 mt-1">
                <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
            </ul>
        </div>

        <!-- Add Availability Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Add New Time Slot</h2>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Day of Week -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Day of Week</label>
                        <select v-model="form.day_of_week"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                            required>
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Start Time</label>
                        <input type="time" v-model="form.start_time"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                            required>
                    </div>

                    <!-- End Time -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">End Time</label>
                        <input type="time" v-model="form.end_time"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"
                            required>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-xs uppercase tracking-wider hover:bg-orange-600 transition shadow-md shadow-orange-500/20 disabled:opacity-50">
                        <span v-if="form.processing">Adding...</span>
                        <span v-else>Add Slot</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Availabilities List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Active Slots</h2>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">List of all currently configured working schedules.</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-100">
                    {{ availabilities?.length || 0 }} Slots Active
                </span>
            </div>

            <div class="p-6">
                <div v-if="!availabilities || availabilities.length === 0" class="text-center py-10 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
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
                            <tr v-for="slot in availabilities" :key="slot.id" class="hover:bg-orange-50/30 transition">
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-800">
                                        {{ slot.day_of_week }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-semibold text-gray-600 flex items-center gap-1.5 mt-1">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ formatTime(slot.start_time) }}
                                    <span class="text-gray-400 font-normal">to</span>
                                    {{ formatTime(slot.end_time) }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <button @click="deleteSlot(slot.id)" type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 font-bold hover:bg-rose-100 transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
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
</template>