<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Icon from '@/Components/Public/Icon.vue';

const page = usePage();
const currentUrl = computed(() => page.url);
const mobileOpen = ref(false);

const nav = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    { label: 'Departments', href: '/our-departments' },
    { label: 'Doctors', href: '/our-doctors' },
    { label: 'Services', href: '/services' },
    { label: 'Contact', href: '/contact' },
];

const isActive = (href) => (href === '/' ? currentUrl.value === '/' : currentUrl.value.startsWith(href));

const quickLinks = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    { label: 'Departments', href: '/our-departments' },
    { label: 'Doctors', href: '/our-doctors' },
    { label: 'Services', href: '/services' },
];

const departmentLinks = ['Cardiology', 'Pediatrics', 'Orthopedics', 'Dermatology', 'General Medicine'];
</script>

<template>
    <div class="min-h-screen flex flex-col bg-white font-sans text-slate-700">
        <!-- Navbar -->
        <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-100">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-2 shrink-0">
                    <span class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center">
                        <Icon name="heart" class="w-5 h-5 text-white" />
                    </span>
                    <span class="font-heading font-extrabold text-lg text-slate-900">Care<span
                            class="text-brand-600">Plus</span></span>
                </Link>

                <div class="hidden lg:flex items-center gap-8">
                    <Link v-for="item in nav" :key="item.href" :href="item.href"
                        :class="['text-sm font-semibold transition-colors', isActive(item.href) ? 'text-brand-600' : 'text-slate-600 hover:text-brand-600']">
                        {{ item.label }}
                    </Link>
                </div>

                <div class="hidden lg:block">
                    <Link href="/book-appointment"
                        class="inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-brand-700 transition shadow-sm shadow-brand-600/20">
                        Book Appointment
                    </Link>
                </div>

                <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600">
                    <Icon :name="mobileOpen ? 'close' : 'menu'" class="w-5 h-5" />
                </button>
            </nav>

            <div v-if="mobileOpen" class="lg:hidden border-t border-slate-100 bg-white">
                <div class="px-4 py-4 space-y-1">
                    <Link v-for="item in nav" :key="item.href" :href="item.href" @click="mobileOpen = false"
                        :class="['block px-3 py-2.5 rounded-lg text-sm font-semibold', isActive(item.href) ? 'bg-brand-50 text-brand-600' : 'text-slate-600']">
                        {{ item.label }}
                    </Link>
                    <Link href="/book-appointment" @click="mobileOpen = false"
                        class="block text-center mt-2 bg-brand-600 text-white text-sm font-bold px-5 py-3 rounded-xl">
                        Book Appointment
                    </Link>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center">
                            <Icon name="heart" class="w-4 h-4 text-white" />
                        </span>
                        <span class="font-heading font-extrabold text-white text-lg">CarePlus</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">Your Health, Our Priority. Quality healthcare
                        with modern facilities and experienced medical professionals.</p>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-white text-sm mb-4">Quick Links</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li v-for="l in quickLinks" :key="l.href">
                            <Link :href="l.href" class="hover:text-brand-400 transition-colors">{{ l.label }}</Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-white text-sm mb-4">Departments</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li v-for="d in departmentLinks" :key="d">
                            <Link href="/our-departments" class="hover:text-brand-400 transition-colors">{{ d }}</Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-white text-sm mb-4">Contact Info</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2.5">
                            <Icon name="map-pin" class="w-4 h-4 mt-0.5 shrink-0 text-brand-400" />
                            123 Health St, Lahore, Pakistan
                        </li>
                        <li class="flex items-center gap-2.5">
                            <Icon name="phone" class="w-4 h-4 shrink-0 text-brand-400" />
                            +92 300 1234567
                        </li>
                        <li class="flex items-center gap-2.5">
                            <Icon name="mail" class="w-4 h-4 shrink-0 text-brand-400" />
                            info@careplus.com
                        </li>
                    </ul>
                    <div class="flex items-center gap-3 mt-4">
                        <span v-for="s in ['facebook', 'twitter', 'instagram', 'linkedin']" :key="s"
                            class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-600 transition cursor-pointer">
                            <Icon :name="s" class="w-3.5 h-3.5" />
                        </span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 py-5">
                <div
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <p>&copy; {{ new Date().getFullYear() }} CarePlus Hospital. All rights reserved.</p>
                    <div class="flex items-center gap-5">
                        <span class="hover:text-slate-300 cursor-pointer">Privacy Policy</span>
                        <span class="hover:text-slate-300 cursor-pointer">Terms &amp; Conditions</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>