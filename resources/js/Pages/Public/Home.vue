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
        <!-- Hero -->
        <section class="relative overflow-hidden bg-gradient-to-b from-brand-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="font-heading font-extrabold text-4xl sm:text-5xl text-slate-900 leading-tight">
                        Your Health<br /><span class="text-brand-600">Our Priority</span>
                    </h1>
                    <p class="mt-5 text-slate-500 text-lg max-w-md">Providing quality healthcare with modern
                        facilities and experienced medical professionals.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link href="/book-appointment"
                            class="inline-flex items-center gap-2 bg-brand-600 text-white font-bold px-6 py-3.5 rounded-xl hover:bg-brand-700 transition shadow-lg shadow-brand-600/25">
                            Book Appointment
                            <Icon name="arrow-right" class="w-4 h-4" />
                        </Link>
                        <Link href="/about"
                            class="inline-flex items-center gap-2 bg-white text-slate-700 font-bold px-6 py-3.5 rounded-xl border border-slate-200 hover:border-brand-300 transition">
                            Learn More
                        </Link>
                    </div>

                    <div class="mt-10 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div v-for="h in highlights" :key="h.title" class="flex items-start gap-2.5">
                            <span
                                class="w-9 h-9 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center shrink-0">
                                <Icon :name="h.icon" class="w-4.5 h-4.5" />
                            </span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 leading-tight">{{ h.title }}</p>
                                <p class="text-[11px] text-slate-400">{{ h.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                <!-- Height fix ki gayi hai taaki container collapse na ho -->
             <div
                        class="h-[450px] sm:h-[500px] w-full rounded-3xl bg-gradient-to-br from-blue-500 to-blue-700 overflow-hidden shadow-2xl shadow-blue-600/20">
                         <img
                            src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=500&auto=format&fit=crop&q=80"
                            alt="Doctor" class="w-full h-full object-cover object-top">
                         </div>
                    
                    <div
                        class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-xl p-4 flex items-center gap-3">
                     <span
                            class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center">
                        
                            <Icon name="check-circle" class="w-5 h-5" />
                        
                        </span>
                     <div>
                            <p class="text-sm font-heading font-bold text-slate-900">{{ stats.patients ||  '10,000+' }}</p>
                             <p class="text-xs text-slate-400">Happy Patients</p>
                             </div>
                         </div>
                </div>
            </div>
        </section>

        <!-- Departments -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="text-brand-600 font-bold text-sm mb-1">What We Offer</p>
                    <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">Our Departments</h2>
                </div>
                <Link href="/departments"
                    class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">
                    View All
                    <Icon name="arrow-right" class="w-4 h-4" />
                </Link>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <Link v-for="d in departments" :key="d.id" :href="`/departments/${d.id}`"
                    class="group p-6 rounded-2xl border border-slate-100 hover:border-brand-200 hover:shadow-lg hover:shadow-slate-200/60 transition-all bg-white">
                    <span
                        class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                        <Icon :name="iconFor(d.name)" class="w-6 h-6" />
                    </span>
                    <h3 class="font-heading font-bold text-slate-900 mb-1">{{ d.name }}</h3>
                    <p class="text-xs text-slate-400 line-clamp-2">{{ d.description || 'Specialized, compassionate care from our expert team.' }}</p>
                </Link>
            </div>
        </section>

        <!-- Doctors -->
        <section class="bg-slate-50 py-16 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-brand-600 font-bold text-sm mb-1">Meet The Team</p>
                        <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">Our Doctors</h2>
                    </div>
                    <Link href="/our-doctors"
                        class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">
                        View All
                        <Icon name="arrow-right" class="w-4 h-4" />
                    </Link>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div v-for="doc in doctors" :key="doc.id"
                        class="bg-white rounded-2xl p-5 text-center border border-slate-100 hover:shadow-lg hover:shadow-slate-200/60 transition-all">
                        <Avatar :name="doc.name" :photo-url="doc.photo_url" size="lg" class="mx-auto mb-4" />
                        <h3 class="font-heading font-bold text-slate-900">Dr.{{ doc.name }}</h3>
                        <p class="text-xs text-brand-600 font-semibold mb-4">{{ doc.department?.name ||
                            doc.specialization }}</p>
                        <Link :href="`/our-doctors/${doc.id}`"
                            class="block text-xs font-bold bg-brand-600 text-white py-2.5 rounded-lg hover:bg-brand-700 transition">
                            View Profile
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
                <div>
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.years }}</p>
                    <p class="text-xs text-slate-400 font-semibold mt-1">Years Experience</p>
                </div>
                <div>
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.doctors }}</p>
                    <p class="text-xs text-slate-400 font-semibold mt-1">Expert Doctors</p>
                </div>
                <div>
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.patients }}</p>
                    <p class="text-xs text-slate-400 font-semibold mt-1">Happy Patients</p>
                </div>
                <div>
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.satisfaction }}
                    </p>
                    <p class="text-xs text-slate-400 font-semibold mt-1">Positive Reviews</p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div
                class="rounded-3xl bg-gradient-to-r from-brand-700 to-brand-900 px-6 sm:px-12 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="text-center sm:text-left">
                    <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-white">Book Your Appointment Today
                    </h3>
                    <p class="text-brand-100 text-sm mt-1">Get the best medical care from our experienced doctors.</p>
                </div>
                <Link href="/book-appointment"
                    class="shrink-0 bg-white text-brand-700 font-bold px-6 py-3.5 rounded-xl hover:bg-brand-50 transition">
                    Book Appointment
                </Link>
            </div>
        </section>

        <!-- Latest News -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <div class="flex items-end justify-between mb-10">
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">Latest News</h2>
                <Link href="/blog"
                    class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">
                    View All
                    <Icon name="arrow-right" class="w-4 h-4" />
                </Link>
            </div>
            <div class="grid sm:grid-cols-3 gap-5">
                <Link v-for="n in news" :key="n.title" href="/blog"
                    class="rounded-2xl overflow-hidden border border-slate-100 hover:shadow-lg hover:shadow-slate-200/60 transition-all group">
                    <div class="h-36 bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center">
                        <Icon name="heart" class="w-10 h-10 text-brand-400" />
                    </div>
                    <div class="p-5">
                        <p class="text-[11px] text-slate-400 font-semibold">{{ n.date }}</p>
                        <h3
                            class="font-heading font-bold text-slate-900 mt-1 group-hover:text-brand-600 transition-colors">
                            {{ n.title }}</h3>
                    </div>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>