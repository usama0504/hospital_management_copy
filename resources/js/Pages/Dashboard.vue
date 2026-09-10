<script setup>
import { Link } from '@inertiajs/vue3';

// Controller se ane walay props ko define karna
defineProps({
    appointmentsCount: Number,
    operationsCount: Number,
    patientsCount: Number,
    totalEarnings: Number,
    isDoctor: Boolean,
    popularDoctors: Array,
    recentAppointments: Array,
    recentBills: Array,
});
</script>

<template>
    <div class="space-y-6">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 space-y-6">

                <div>
                    <h3 class="text-base font-bold text-gray-800 mb-4">Activity Overview</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-black text-gray-900">{{ appointmentsCount ?? 0 }}</p>
                                <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">Appointments</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-black text-gray-900">{{ operationsCount ?? 0 }}</p>
                                <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">Operations</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-black text-gray-900">{{ patientsCount ?? 0 }}</p>
                                <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">New Patients</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>

                        <template v-if="!isDoctor">
                            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-black text-gray-900">${{ Number(totalEarnings ?? 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</p>
                                    <p class="text-xs font-semibold text-gray-400 tracking-wider uppercase mt-1">Earning</p>
                                </div>
                                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                        <h3 class="text-base font-bold text-gray-800">Hospital Survey</h3>
                        <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-gray-500">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-400"></span> Patients 2019</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-indigo-600"></span> Patients 2020</span>
                        </div>
                    </div>
                    <div class="h-60 w-full bg-gray-50/50 rounded-xl border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <span>Interactive Line Chart Analytics Preview</span>
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-800">Patient Visit By Department</h3>
                    </div>

                    <div class="flex items-center justify-center py-6">
                        <div class="w-36 h-36 rounded-full border-[14px] border-indigo-600 border-t-rose-400 border-r-amber-400 flex items-center justify-center text-xs font-bold text-gray-600 shadow-inner">
                            Visits
                        </div>
                    </div>

                    <div class="space-y-3 pt-2 text-xs font-semibold">
                        <div class="flex items-center justify-between text-gray-600">
                            <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span> Cardiology</span>
                            <span class="text-gray-900 font-bold">40%</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> Neurology</span>
                            <span class="text-gray-900 font-bold">30%</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Dermatology</span>
                            <span class="text-gray-900 font-bold">20%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-800">Popular Doctor List</h3>
                        <Link :href="route('doctors.index')" class="text-xs text-orange-500 font-semibold hover:underline">View All</Link>
                    </div>
                    <div class="divide-y divide-gray-100 text-sm">
                        <template v-if="popularDoctors && popularDoctors.length > 0">
                            <div v-for="doctor in popularDoctors" :key="doctor.id" class="px-6 py-3.5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                        {{ doctor.name.substring(0, 2).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-xs">Dr. {{ doctor.name }}</p>
                                        <p class="text-[11px] text-gray-400">{{ doctor.specialization ?? 'Specialist' }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Available</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="p-6 text-center text-gray-400 text-xs">No doctors found in database.</div>
                        </template>
                    </div>
                </div>

            </div>

        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-800">Appointment Activity</h3>
                    <Link :href="route('appointments.index')" class="text-xs text-orange-500 font-semibold hover:underline">View All</Link>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/60 text-gray-400 text-xs uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6 font-semibold">Name</th>
                                <th class="py-3.5 px-4 sm:px-6 font-semibold">Date</th>
                                <th class="py-3.5 px-4 sm:px-6 font-semibold">Visit Time</th>
                                <th class="py-3.5 px-4 sm:px-6 font-semibold">Doctor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            <template v-if="recentAppointments && recentAppointments.length > 0">
                                <tr v-for="appointment in recentAppointments" :key="appointment.id" class="hover:bg-gray-50/40 transition">
                                    <td class="py-4 px-4 sm:px-6 font-bold text-gray-900">
                                        <span class="truncate max-w-[120px] sm:max-w-none">{{ appointment.patient?.name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-4 px-4 sm:px-6 text-xs whitespace-nowrap">
                                        {{ appointment.appointment_date ? new Date(appointment.appointment_date).toLocaleDateString('en-GB') : 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4 sm:px-6 text-xs whitespace-nowrap font-medium text-gray-700">
                                        {{ appointment.appointment_date ? new Date(appointment.appointment_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4 sm:px-6 font-bold text-gray-800 text-xs sm:text-sm whitespace-nowrap">
                                        Dr. {{ appointment.doctor?.name ?? 'N/A' }}
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-400 text-sm">
                                        No appointments found. Add an appointment to see it here!
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-800">Average Patient Visits</h3>
                    </div>
                    <div class="h-44 bg-gray-50/50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center text-gray-400 text-xs">
                        [ Bar Chart Widget Preview ]
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-800">Employes</h3>
                        <span class="text-[11px] text-gray-400">Staff according to department</span>
                    </div>
                    <div class="h-44 bg-gray-50/50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center text-gray-400 text-xs">
                        [ Employees Metrics ]
                    </div>
                </div>
            </div>

        </div>

        <template v-if="!isDoctor">
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-800">Recent Billing & Payments</h3>
                        <span class="text-xs text-rose-500 font-bold">Total Earnings: ${{ Number(totalEarnings ?? 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50/60 text-gray-400 text-xs uppercase tracking-wider">
                                    <th class="py-3.5 px-6 font-semibold">Patient Name</th>
                                    <th class="py-3.5 px-6 font-semibold">Amount</th>
                                    <th class="py-3.5 px-6 font-semibold">Status</th>
                                    <th class="py-3.5 px-6 font-semibold">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                <template v-if="recentBills && recentBills.length > 0">
                                    <tr v-for="bill in recentBills" :key="bill.id" class="hover:bg-gray-50/40 transition">
                                        <td class="py-4 px-6 font-bold text-gray-900">
                                            {{ bill.patient?.name ?? 'Walk-in Patient' }}
                                        </td>
                                        <td class="py-4 px-6 font-extrabold text-gray-800">
                                            ${{ Number(bill.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
                                        </td>
                                        <td class="py-4 px-6 text-xs">
                                            <span v-if="String(bill.status).toLowerCase() === 'paid'" class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full font-bold">Paid</span>
                                            <span v-else class="px-2.5 py-1 bg-amber-50 text-amber-600 rounded-full font-bold">{{ bill.status.charAt(0).toUpperCase() + bill.status.slice(1) }}</span>
                                        </td>
                                        <td class="py-4 px-6 text-xs text-gray-500">
                                            {{ bill.created_at ? new Date(bill.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A' }}
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-gray-400 text-xs">
                                            No billing records found in database.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>

    </div>
</template>