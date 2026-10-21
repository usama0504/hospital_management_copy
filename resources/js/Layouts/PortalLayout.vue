<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash ?? {});

const links = [
    { label: 'Overview', route: 'portal.dashboard', match: 'portal.dashboard' },
    { label: 'Appointments', route: 'portal.appointments', match: 'portal.appointments*' },
    { label: 'Prescriptions', route: 'portal.prescriptions', match: 'portal.prescriptions*' },
    { label: 'Bills', route: 'portal.bills', match: 'portal.bills*' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50/50 text-gray-800 antialiased" style="font-family: 'Poppins', sans-serif;">
        <header class="bg-white border-b border-gray-100 sticky top-0 z-20">
            <div class="max-w-5xl mx-auto px-4 sm:px-6">
                <div class="h-16 flex items-center justify-between gap-4">
                    <Link :href="route('portal.dashboard')" class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-2xl bg-orange-500 text-white flex items-center justify-center shadow-sm shadow-orange-500/30">
                            🏥</div>
                        <div class="leading-tight">
                            <span class="block text-base font-black tracking-tight text-gray-900">Health Care</span>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Patient
                                Portal</span>
                        </div>
                    </Link>

                    <nav class="hidden md:flex items-center gap-1 text-xs font-semibold">
                        <Link v-for="l in links" :key="l.route" :href="route(l.route)"
                            :class="route().current(l.match) ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
                            class="px-4 py-2.5 rounded-xl transition">
                            {{ l.label }}
                        </Link>
                    </nav>

                    <div class="flex items-center gap-3">
                        <span v-if="user" class="hidden sm:block text-xs font-bold text-gray-700 truncate max-w-[10rem]">{{
                            user.name }}</span>
                        <Link :href="route('logout')" method="post" as="button"
                            class="text-xs font-bold text-gray-500 hover:text-red-600 hover:bg-red-50 transition px-3 py-2 rounded-xl">
                            Log Out
                        </Link>
                    </div>
                </div>

                <!-- Mobile nav -->
                <nav class="md:hidden flex gap-1 overflow-x-auto pb-3 text-xs font-semibold">
                    <Link v-for="l in links" :key="l.route" :href="route(l.route)"
                        :class="route().current(l.match) ? 'bg-orange-500 text-white' : 'text-gray-500 bg-gray-50'"
                        class="px-4 py-2 rounded-xl whitespace-nowrap transition">
                        {{ l.label }}
                    </Link>
                </nav>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-5">
            <div v-if="flash.success"
                class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-xs font-semibold px-4 py-3">
                {{ flash.success }}
            </div>
            <div v-if="flash.error"
                class="rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-xs font-semibold px-4 py-3">
                {{ flash.error }}
            </div>

            <slot />
        </main>
    </div>
</template>
