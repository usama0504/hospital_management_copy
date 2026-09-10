<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const mobileMenuOpen = ref(false);

// Inertia page se authenticated user ka data lena
const page = usePage();
const user = page.props.auth?.user;
</script>

<template>
    <nav class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">

                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold shadow-sm shadow-orange-500/30 text-lg">
                        🏥
                    </div>
                    <Link :href="route('dashboard')" class="text-lg font-black tracking-tight text-gray-900">
                        HospitalMS
                    </Link>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden md:flex space-x-2 text-xs font-semibold">
                    <Link :href="route('dashboard')" 
                        :class="route().current('dashboard') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="px-4 py-2.5 rounded-xl transition">
                        Dashboard
                    </Link>
                    <Link :href="route('patients.index')" 
                        :class="route().current('patients.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="px-4 py-2.5 rounded-xl transition">
                        Patients
                    </Link>
                    <Link :href="route('doctors.index')" 
                        :class="route().current('doctors.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="px-4 py-2.5 rounded-xl transition">
                        Doctors
                    </Link>
                    <Link :href="route('appointments.index')" 
                        :class="route().current('appointments.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="px-4 py-2.5 rounded-xl transition">
                        Appointments
                    </Link>
                    <Link :href="route('bills.index')" 
                        :class="route().current('bills.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="px-4 py-2.5 rounded-xl transition">
                        Billing
                    </Link>
                    <Link :href="route().has('reports.index') ? route('reports.index') : '#'" 
                        :class="route().current('reports.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="px-4 py-2.5 rounded-xl transition">
                        Reports
                    </Link>
                </div>

                <!-- Right Profile / Actions Area -->
                <div class="hidden md:flex items-center gap-4">
                    <div v-if="user" class="flex items-center gap-3 pl-4 border-l border-gray-100">
                        <div>
                            <p class="text-xs font-bold text-gray-900 leading-none">{{ user.name }}</p>
                            <p class="text-[11px] text-gray-400 mt-1 capitalize font-semibold">Admin</p>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2.5 rounded-full bg-gray-50 text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                        <svg class="h-6 w-6" v-if="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" v-else fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Vue toggled) -->
        <div v-show="mobileMenuOpen" class="md:hidden px-4 pt-2 pb-4 space-y-1.5 border-t border-gray-100 bg-white text-xs font-semibold">
            <Link :href="route('dashboard')" :class="route().current('dashboard') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-3 rounded-xl">
                Dashboard
            </Link>
            <Link :href="route('patients.index')" :class="route().current('patients.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-3 rounded-xl">
                Patients
            </Link>
            <Link :href="route('doctors.index')" :class="route().current('doctors.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-3 rounded-xl">
                Doctors
            </Link>
            <Link :href="route('appointments.index')" :class="route().current('appointments.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-3 rounded-xl">
                Appointments
            </Link>
            <Link :href="route('bills.index')" :class="route().current('bills.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-3 rounded-xl">
                Billing
            </Link>
            <Link :href="route().has('reports.index') ? route('reports.index') : '#'" :class="route().current('reports.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-3 rounded-xl">
                Reports
            </Link>
        </div>
    </nav>
</template>