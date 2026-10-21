<script setup>
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';

const props = defineProps({
    prescription: Object,
});

const formatDate = (v) => v
    ? new Date(v).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
    : '-';
const rx = 'RX-' + String(props.prescription.id).padStart(5, '0');
</script>

<template>
    <PortalLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <Link :href="route('portal.prescriptions')"
                class="text-center text-xs font-bold text-gray-600 hover:text-gray-900 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                &larr; Back to Prescriptions
            </Link>
            <a :href="route('portal.prescriptions.pdf', prescription.id)"
                class="text-center text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 transition px-5 py-2.5 rounded-xl shadow-md shadow-orange-500/20">
                Download PDF
            </a>
        </div>

        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-5">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Prescription</p>
                    <p class="text-lg font-black text-orange-600">#{{ rx }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-sm font-bold text-gray-900">Dr. {{ prescription.doctor?.name ?? 'N/A' }}</p>
                    <p class="text-xs text-orange-600 font-semibold">{{ prescription.doctor?.specialization ?? 'Medical Specialist' }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">{{ formatDate(prescription.prescribed_date) }}</p>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Diagnosis</p>
                <p class="text-sm text-gray-800 font-medium bg-gray-50/60 border border-gray-100 rounded-xl px-4 py-3">{{ prescription.diagnosis }}</p>
            </div>

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Medicines (Rx)</p>
                <p v-if="!prescription.items?.length" class="text-xs text-gray-500 font-medium">No medicines listed.</p>
                <ul v-else class="space-y-2.5">
                    <li v-for="item in prescription.items" :key="item.id" class="border border-gray-100 rounded-xl px-4 py-3">
                        <p class="text-sm font-bold text-gray-900">{{ item.medicine_name }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                            <span v-if="item.dosage">Dosage: {{ item.dosage }}</span>
                            <span v-if="item.frequency"> &middot; Frequency: {{ item.frequency }}</span>
                            <span v-if="item.duration"> &middot; Duration: {{ item.duration }}</span>
                        </p>
                        <p v-if="item.instructions" class="text-xs text-gray-600 mt-1">{{ item.instructions }}</p>
                    </li>
                </ul>
            </div>

            <div v-if="prescription.notes">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Doctor's Notes</p>
                <p class="text-sm text-gray-700 whitespace-pre-line bg-gray-50/60 border border-gray-100 rounded-xl px-4 py-3">{{ prescription.notes }}</p>
            </div>
        </div>
    </PortalLayout>
</template>
