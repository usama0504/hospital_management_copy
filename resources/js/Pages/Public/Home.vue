<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';

const props = defineProps({
    departments: { type: Array, default: () => [] },
    doctors: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
    meta: { type: Object, default: () => ({}) },
});

const highlights = [
    { icon: 'award', title: 'Expert Doctors', desc: 'Experienced specialists' },
    { icon: 'building', title: 'Modern Facilities', desc: 'State-of-the-art care' },
    { icon: 'ambulance', title: '24/7 Emergency', desc: 'Always here for you' },
    { icon: 'calendar', title: 'Online Booking', desc: 'Book in seconds' },
];

const news = [
    { title: 'Tips for a Healthy Heart', date: 'Mar 10, 2024' },
    { title: 'Importance of Regular Checkups', date: 'Mar 5, 2024' },
    { title: 'Child Health and Nutrition', date: 'Feb 28, 2024' },
];

const iconFor = (name) => props.meta?.[name]?.icon || 'stethoscope';
</script>

<template>
    <PublicLayout>
        <!-- Hero Section with Background Clinic Image -->
        <section class="relative bg-slate-900 overflow-hidden pt-12 pb-20">
            <!-- Background Image with Gradient Overlay -->
            <div class="absolute inset-0 z-0">
                <img 
                    src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=1600&auto=format&fit=crop" 
                    alt="Hospital Background" 
                    class="w-full h-full object-cover opacity-25"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/95 to-white/60"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
                <!-- Left Text Side -->
                <div class="lg:col-span-7 space-y-6">
                    <h1 class="font-heading font-extrabold text-4xl sm:text-6xl text-slate-900 tracking-tight leading-[1.1]">
                        Your Health<br />
                        <span class="text-brand-600">Our Priority</span>
                    </h1>
                    <p class="text-slate-600 text-base sm:text-lg max-w-md leading-relaxed font-normal">
                        Providing quality healthcare with modern facilities and experienced medical professionals.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <Link href="/book-appointment"
                            class="inline-flex items-center gap-2 bg-brand-600 text-white font-bold px-7 py-3.5 rounded-xl hover:bg-brand-700 transition shadow-lg shadow-brand-600/20 text-sm">
                            Book Appointment
                        </Link>
                        <Link href="/about"
                            class="inline-flex items-center gap-2 bg-white text-slate-700 font-bold px-7 py-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-sm shadow-sm">
                            Learn More
                        </Link>
                    </div>
                </div>

                <!-- Right Doctor Image Side -->
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="w-full max-w-[380px] aspect-[4/5] relative flex items-end justify-center">
                        <img
                            src="https://images.unsplash.com/photo-1758691462651-611d730c5272?q=80&amp;w=1400&amp;auto=format&amp;fit=crop"
                            alt="CarePlus doctor" class="w-full h-full object-cover object-top rounded-3xl shadow-xl" loading="eager" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Strip (Hero ke neeche alag se) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20 mb-16">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/60 grid grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 lg:divide-x divide-slate-100">
                <div v-for="h in highlights" :key="h.title" class="flex items-center gap-4 p-6">
                    <span class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                        <Icon :name="h.icon" class="w-6 h-6" />
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ h.title }}</p>
                        <p class="text-xs text-slate-400 truncate mt-0.5">{{ h.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Departments -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <p class="text-brand-600 font-bold text-xs uppercase tracking-wider mb-1">What We Offer</p>
                    <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">Our Departments</h2>
                </div>
                <Link href="/our-departments" class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">
                    View All <Icon name="arrow-right" class="w-4 h-4" />
                </Link>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <Link v-for="d in departments" :key="d.id" :href="`/our-departments/${d.id}`"
                    class="group p-5 rounded-2xl border border-slate-100 hover:border-brand-200 hover:shadow-xl hover:shadow-slate-100 transition-all bg-white">
                    <span class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                        <Icon :name="iconFor(d.name)" class="w-6 h-6" />
                    </span>
                    <h3 class="font-heading font-bold text-slate-900 text-base mb-1">{{ d.name }}</h3>
                    <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">{{ d.description || 'Specialized, compassionate care from our expert team.' }}</p>
                </Link>
            </div>
        </section>

        <!-- Doctors -->
        <section class="bg-slate-50/50 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <p class="text-brand-600 font-bold text-xs uppercase tracking-wider mb-1">Meet The Team</p>
                        <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">Our Doctors</h2>
                    </div>
                    <Link href="/our-doctors" class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">
                        View All <Icon name="arrow-right" class="w-4 h-4" />
                    </Link>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div v-for="doc in doctors" :key="doc.id"
                        class="bg-white rounded-2xl p-5 text-center border border-slate-100 hover:shadow-xl hover:shadow-slate-100 transition-all flex flex-col items-center">
                        <Avatar :name="doc.name" :photo-url="doc.photo_url" size="lg" class="mx-auto mb-3" />
                        <h3 class="font-heading font-bold text-slate-900 text-base">{{ doc.name }}</h3>
                        <p class="text-xs text-brand-600 font-semibold mt-0.5 mb-4">{{ doc.department?.name || doc.specialization }}</p>
                        <Link :href="`/our-doctors/${doc.id}`"
                            class="w-full mt-auto text-xs font-bold bg-brand-600 text-white py-2.5 rounded-xl hover:bg-brand-700 transition shadow-sm">
                            View Profile
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.years }}</p>
                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">Years Experience</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.doctors }}</p>
                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">Expert Doctors</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.patients }}</p>
                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">Happy Patients</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.satisfaction }}</p>
                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">Positive Reviews</p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="rounded-3xl bg-brand-700 px-6 sm:px-12 py-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
                <div class="text-center sm:text-left">
                    <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-white">Book Your Appointment Today</h3>
                    <p class="text-brand-100 text-sm mt-1">Get the best medical care from our experienced doctors.</p>
                </div>
                <Link href="/book-appointment"
                    class="shrink-0 bg-white text-brand-700 font-bold px-6 py-3.5 rounded-xl hover:bg-brand-50 transition shadow-md">
                    Book Appointment
                </Link>
            </div>
        </section>

        <!-- Latest News -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 mb-8">
            <div class="files flex items-end justify-between mb-8">
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">Latest News</h2>
                <Link href="/blog" class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">
                    View All <Icon name="arrow-right" class="w-4 h-4" />
                </Link>
            </div>
            <div class="grid sm:grid-cols-3 gap-5">
                <Link v-for="n in news" :key="n.title" href="/blog"
                    class="rounded-2xl overflow-hidden border border-slate-100 hover:shadow-xl hover:shadow-slate-100 transition-all group bg-white">
                    <div class="h-36 bg-gradient-to-br from-brand-50 to-brand-100 flex items-center justify-center">
                        <Icon name="heart" class="w-10 h-10 text-brand-400 group-hover:scale-110 transition-transform" />
                    </div>
                    <div class="p-5">
                        <p class="text-[11px] text-slate-400 font-semibold">{{ n.date }}</p>
                        <h3 class="font-heading font-bold text-slate-900 text-base mt-1 group-hover:text-brand-600 transition-colors">{{ n.title }}</h3>
                    </div>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>