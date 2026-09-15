<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    prescription: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

const formatRxId = (id) => {
    return String(id).padStart(5, '0');
};

const printPage = () => {
    window.print();
};
</script>

<template>

    <component :is="'style'">
        @media print {
        body * {
        visibility: hidden;
        }

        #printable-prescription,
        #printable-prescription * {
        visibility: visible;
        }

        #printable-prescription {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
        padding: 10px !important;
        margin: 0 !important;
        }
        }
    </component>

    <AuthenticatedLayout>
        <div class="py-6 sm:py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-6 flex items-center justify-between print:hidden gap-4">
                    <Link :href="route('prescriptions.index')"
                        class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                        &larr; Back to Prescriptions
                    </Link>
                    <button @click="printPage()"
                        class="inline-flex items-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-orange-500/20 hover:bg-orange-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Prescription
                    </button>
                </div>

                <div id="printable-prescription"
                    class="bg-white border border-gray-100 shadow-2xl shadow-gray-100/90 rounded-3xl p-6 sm:p-12 space-y-8 relative overflow-hidden">

                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-orange-400 to-orange-600"></div>

                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-100 pb-6 gap-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-2xl bg-orange-500 flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-orange-500/30">
                                HC
                            </div>
                            <div>
                                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Health Care</h1>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Official Medical Prescription</p>
                            </div>
                        </div>
                        <div
                            class="text-left sm:text-right bg-gray-50 px-5 py-3 rounded-2xl border border-gray-100 w-full sm:w-auto">
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Prescription
                                Reference</span>
                            <span class="text-base font-black text-orange-600">#RX-{{ formatRxId(prescription.id)
                                }}</span>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm bg-gray-50/60 p-6 rounded-2xl border border-gray-100">
                        <div>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Patient</span>
                            <h3 class="font-bold text-gray-900 text-base">{{ prescription.patient?.name ?? 'N/A' }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5 font-medium">Phone: {{ prescription.patient?.phone ??
                                'N/A' }}</p>
                        </div>

                        <div>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Prescribed
                                By</span>
                            <h3 class="font-bold text-gray-900 text-base">Dr. {{ prescription.doctor?.name ?? 'N/A' }}
                            </h3>
                            <p class="text-xs text-orange-600 font-semibold mt-0.5">{{
                                prescription.doctor?.specialization ?? 'Medical Specialist' }}</p>
                        </div>

                        <div class="sm:text-right">
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Date</span>
                            <span class="font-bold text-gray-800 text-xs sm:text-sm">{{
                                formatDate(prescription.prescribed_date) }}</span>
                        </div>
                    </div>

                    <div>
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Diagnosis</span>
                        <p
                            class="text-sm text-gray-800 font-medium bg-gray-50/60 border border-gray-100 rounded-xl px-4 py-3">
                            {{ prescription.diagnosis }}</p>
                    </div>

                    <div>
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Prescribed
                            Medicines (Rx)</span>
                        <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        <th class="py-3 px-4">Medicine</th>
                                        <th class="py-3 px-4">Dosage</th>
                                        <th class="py-3 px-4">Frequency</th>
                                        <th class="py-3 px-4">Duration</th>
                                        <th class="py-3 px-4">Instructions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                                    <tr v-for="item in prescription.items" :key="item.id">
                                        <td class="py-3 px-4 font-bold text-gray-900">{{ item.medicine_name }}</td>
                                        <td class="py-3 px-4 text-gray-600 font-medium">{{ item.dosage || '—' }}</td>
                                        <td class="py-3 px-4 text-gray-600 font-medium">{{ item.frequency || '—' }}</td>
                                        <td class="py-3 px-4 text-gray-600 font-medium">{{ item.duration || '—' }}</td>
                                        <td class="py-3 px-4 text-gray-600 font-medium">{{ item.instructions || '—' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="prescription.notes">
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Additional
                            Notes</span>
                        <p
                            class="text-sm text-gray-700 font-medium bg-gray-50/60 border border-gray-100 rounded-xl px-4 py-3">
                            {{ prescription.notes }}</p>
                    </div>

                    <div
                        class="border-t border-gray-100 pt-6 mt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400 gap-6">
                        <div class="text-center sm:text-left">
                            <p class="font-bold text-gray-700">Take medicines as prescribed. Consult again if symptoms
                                persist.</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">This is a computer-generated prescription.</p>
                        </div>
                        <div class="text-center sm:text-right min-w-[180px]">
                            <div class="h-8 mb-1"></div>
                            <p class="font-bold text-gray-800 border-t border-gray-300 pt-2">Doctor's Signature</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>