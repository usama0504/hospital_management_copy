<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    prescription: Object,
    patients: Array,
    doctors: Array,
});

const form = useForm({
    patient_id: props.prescription.patient_id,
    prescribed_date: props.prescription.prescribed_date?.slice(0, 10) ?? '',
    diagnosis: props.prescription.diagnosis,
    notes: props.prescription.notes ?? '',
    items: props.prescription.items.length > 0
        ? props.prescription.items.map(i => ({
            id: i.id,
            medicine_name: i.medicine_name,
            dosage: i.dosage ?? '',
            frequency: i.frequency ?? '',
            duration: i.duration ?? '',
            instructions: i.instructions ?? '',
        }))
        : [{ medicine_name: '', dosage: '', frequency: '', duration: '', instructions: '' }],
});

const addItem = () => {
    form.items.push({ medicine_name: '', dosage: '', frequency: '', duration: '', instructions: '' });
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submit = () => {
    form.put(route('prescriptions.update', props.prescription.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header Section -->
                <div class="mb-4 sm:mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Edit Prescription</h2>
                        <p class="text-[11px] sm:text-xs text-gray-500 font-medium">Update diagnosis, notes, or the prescribed medicines.</p>
                    </div>
                    <Link :href="route('prescriptions.index')" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-white sm:bg-gray-50 px-3.5 py-2 rounded-xl border border-gray-200 shadow-2xs shrink-0">
                        &larr; Back
                    </Link>
                </div>

                <!-- Error Messages Alert -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-4 sm:mb-6 flex flex-col gap-1 bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                    <span class="font-bold">Please fix the following errors:</span>
                    <ul class="list-disc pl-5 space-y-0.5">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <!-- Form Card Container -->
                <form @submit.prevent="submit" class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 sm:p-6 space-y-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Patient Selection -->
                        <div>
                            <label for="patient_id" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Patient</label>
                            <select v-model="form.patient_id" id="patient_id" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition">
                                <option value="">Select Patient</option>
                                <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                                    {{ patient.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.patient_id" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.patient_id }}</div>
                        </div>

                        <!-- Doctor (read-only display, doctor cannot be reassigned after creation) -->
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Doctor</label>
                            <div class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-600">
                                Dr. {{ prescription.doctor?.name ?? 'N/A' }}
                            </div>
                        </div>

                        <!-- Prescribed Date -->
                        <div>
                            <label for="prescribed_date" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Date</label>
                            <input type="date" v-model="form.prescribed_date" id="prescribed_date" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                            <div v-if="form.errors.prescribed_date" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.prescribed_date }}</div>
                        </div>

                        <!-- Diagnosis -->
                        <div class="sm:col-span-2">
                            <label for="diagnosis" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Diagnosis</label>
                            <textarea v-model="form.diagnosis" id="diagnosis" rows="2" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"></textarea>
                            <div v-if="form.errors.diagnosis" class="text-rose-600 text-[11px] mt-1 font-semibold">{{ form.errors.diagnosis }}</div>
                        </div>
                    </div>

                    <!-- Medicines Repeater -->
                    <div class="border-t border-gray-100 pt-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Medicines</h3>
                            <button type="button" @click="addItem"
                                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-orange-600 hover:text-orange-700 bg-orange-50 px-3 py-1.5 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Add Medicine
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div v-for="(item, index) in form.items" :key="item.id ?? 'new-' + index"
                                class="bg-gray-50/60 border border-gray-100 rounded-xl p-3.5 relative">
                                <button v-if="form.items.length > 1" type="button" @click="removeItem(index)"
                                    class="absolute top-2.5 right-2.5 p-1 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Remove medicine">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pr-6">
                                    <div class="lg:col-span-2">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Medicine Name</label>
                                        <input type="text" v-model="item.medicine_name" required
                                            class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Dosage</label>
                                        <input type="text" v-model="item.dosage"
                                            class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Frequency</label>
                                        <input type="text" v-model="item.frequency"
                                            class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Duration</label>
                                        <input type="text" v-model="item.duration"
                                            class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                                    </div>
                                    <div class="lg:col-span-3">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Instructions</label>
                                        <input type="text" v-model="item.instructions"
                                            class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.errors.items" class="text-rose-600 text-[11px] mt-2 font-semibold">{{ form.errors.items }}</div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Additional Notes (optional)</label>
                        <textarea v-model="form.notes" id="notes" rows="2"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <Link :href="route('prescriptions.index')" class="w-full sm:w-auto text-center text-xs font-bold text-gray-500 hover:text-gray-900 transition px-4 py-2.5 rounded-xl border border-transparent hover:border-gray-200">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="w-full sm:w-auto inline-flex items-center justify-center bg-orange-500 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition disabled:opacity-50">
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Update Prescription</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
