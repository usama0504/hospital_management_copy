<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const sidebarOpen = ref(false);

const page = usePage();
const user = page.props.auth?.user;
</script>

<template>
    <div class="bg-gray-50/50 font-sans antialiased text-gray-800 min-h-screen"
        style="font-family: 'Poppins', sans-serif;">

        <div class="flex h-screen overflow-hidden">

            <div v-show="sidebarOpen" @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden transition-opacity">
            </div>

            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-white border-r border-gray-100 transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto">

                <div class="flex items-center justify-between px-6 h-20 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold shadow-sm shadow-orange-500/30 text-lg">
                            🏥
                        </div>
                        <span class="text-lg font-black tracking-tight text-gray-900">Health Care</span>
                    </div>

                    <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto text-xs font-semibold">
                    <Link :href="route('dashboard')"
                        :class="route().current('dashboard') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard
                    </Link>
                    <Link :href="route('appointments.index')"
                        :class="route().current('appointments.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Appointment
                    </Link>
                    <Link :href="route('prescriptions.index')"
                        :class="route().current('prescriptions.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Prescriptions
                    </Link>
                    <Link :href="route('bills.index')"
                        :class="route().current('bills.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Billing & Invoices
                    </Link>
                    <Link :href="route('users.index')"
                        :class="route().current('users.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Manage Users
                    </Link>
                    <Link :href="route('doctors.index')"
                        :class="route().current('doctors.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Doctors
                    </Link>
                    <Link :href="route('patients.index')"
                        :class="route().current('patients.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Patients
                    </Link>
                    <Link :href="route('reports.index')"
                        :class="route().current('reports.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13h2v8H3v-8zm4-6h2v14H7V7zm4-4h2v18h-2V3zm4 8h2v10h-2V11zm4-5h2v15h-2V6z" />
                        </svg>
                        Reports
                    </Link>
                    <Link :href="route('profile.edit')"
                        :class="route().current('profile.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </Link>
                </nav>

                <div class="p-4 border-t border-gray-100">
                    <Link :href="route('logout')" method="post" as="button"
                        class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition font-semibold text-xs text-left">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Log Out
                    </Link>
                </div>
            </aside>

            <div class="flex-1 flex flex-col overflow-hidden">

                <header
                    class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 z-10">
                    <div class="flex items-center gap-3">

                        <button @click="sidebarOpen = true"
                            class="md:hidden text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-base sm:text-xl font-black text-gray-900 tracking-tight truncate">Dashboard
                            Overview</h1>
                    </div>

                    <div class="flex items-center gap-4">
                        <div v-if="user"
                            class="flex items-center gap-2 sm:gap-3 sm:pl-4 sm:border-l sm:border-gray-100">
                            <img v-if="user.profile_photo_path" :src="`/storage/${user.profile_photo_path}`"
                                :alt="user.name"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover shadow-sm border border-gray-200">
                            <div v-else
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                {{ user.name.substring(0, 2).toUpperCase() }}
                            </div>

                            <div class="hidden sm:block">
                                <p class="text-xs font-bold text-gray-900 leading-none">{{ user.name }}</p>
                                <!-- Dynamic Role Check -->
                                <p class="text-[11px] text-gray-400 mt-1 capitalize font-semibold">
                                    {{ user.roles && user.roles.length > 0 ? (typeof user.roles[0] === 'string' ?
                                        user.roles[0] : user.roles[0].name) : 'User' }}
                                </p>
                            </div>

                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                    <slot />
                </main>

            </div>

        </div>

    </div>
</template>