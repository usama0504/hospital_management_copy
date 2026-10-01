<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

import hospitalImg from '@/images/hospital-building.avif';
import icuImg from '@/images/icu.avif';
import otImg from '@/images/ot.webp';
import emergencyImg from '@/images/emergency.avif';
import labImg from '@/images/lab.avif';
import aboutBg from '@/images/aboutbg.png';


defineProps({
    stats: { type: Object, default: () => ({}) },
});

const values = [
    { icon: 'shield', title: 'Our Mission', desc: 'To deliver accessible, compassionate and high-quality healthcare to every patient who walks through our doors.' },
    { icon: 'globe', title: 'Our Vision', desc: 'To be the most trusted healthcare provider in the region, known for excellence and innovation.' },
    { icon: 'heart', title: 'Our Values', desc: 'Compassion, integrity, and continuous improvement guide everything we do for our patients.' },
];

const whyUs = [
    { icon: 'award', title: 'Experienced Doctors', desc: '10+ years of combined clinical expertise.' },
    { icon: 'building', title: 'Modern Technology', desc: 'Advanced diagnostic and treatment equipment.' },
    { icon: 'users', title: 'Patient-Centered', desc: 'Care plans built around every individual.' },
    { icon: 'clock', title: 'Rapid Response', desc: '24/7 emergency and on-call support.' },
];

const facilities = [
    { name: 'ICU', image: icuImg },
    { name: 'Operation Theatre', image: otImg },
    { name: 'Emergency', image: emergencyImg },
    { name: 'Laboratory', image: labImg },
];
</script>

<template>
    <PublicLayout>
        <!-- Hero Section -->
        <PageHero title="About Us" subtitle="Compassionate Care for a Healthier Tomorrow" :bgImage="aboutBg" />

        <!-- 1. Our Story Section (White Background) -->
        <section class="bg-white py-8 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="text-teal-600 font-bold text-xs uppercase tracking-wider mb-2">Our Story</p>
                    <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-[#0f172a] mb-4">
                        Built Around Our Patients, For Over a Decade
                    </h2>
                    <p class="text-slate-500 text-sm leading-relaxed mb-4">
                        Health Care was founded with a simple goal: make quality healthcare accessible to everyone. What started as a small clinic has grown into a full-service hospital with specialists across nine departments.
                    </p>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Today, our team of experienced doctors and modern facilities serve thousands of patients every year, always guided by the same promise of compassionate, patient-first care.
                    </p>
                </div>
                <div class="rounded-3xl overflow-hidden shadow-sm border border-slate-100 bg-slate-100 h-72 sm:h-80">
                    <img :src="hospitalImg" alt="Hospital Building" class="w-full h-full object-cover" />
                </div>
            </div>
        </section>

        <!-- 2. Mission, Vision, Values Section (Light Slate Background matching image layout) -->
        <section class="bg-white py-8 sm:py-16 ">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid sm:grid-cols-3 gap-6">
                    <div v-for="v in values" :key="v.title" class="bg-slate-50/60 rounded-3xl p-8 border border-slate-100 shadow-sm text-center">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto mb-5">
                            <Icon :name="v.icon" class="w-6 h-6" />
                        </div>
                        <h3 class="font-heading font-extrabold text-[#0f172a] text-base mb-2">{{ v.title }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ v.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Why Choose Us Section (White Background) -->
        <section class="bg-white py-8 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-[#0f172a] text-center mb-12">Why Choose Us</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="w in whyUs" :key="w.title" class="p-6 rounded-3xl bg-gray-50/60 border border-slate-100 shadow-sm text-center hover:border-teal-200 transition">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto mb-4">
                            <Icon :name="w.icon" class="w-6 h-6" />
                        </div>
                        <h4 class="font-heading font-extrabold text-sm text-[#0f172a] mb-1">{{ w.title }}</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ w.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Our Facilities Section (Light Slate Background) -->
        <section class="bg-white py-8 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-[#0f172a] text-center mb-12">Our Facilities</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="f in facilities" :key="f.name"
                        class="rounded-3xl overflow-hidden border border-slate-100 bg-white shadow-sm flex flex-col group">
                        <div class="h-36 bg-slate-100 overflow-hidden">
                            <img :src="f.image" :alt="f.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div class="p-4 bg-white">
                            <p class="font-heading font-bold text-[#0f172a] text-sm">{{ f.name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

            <!-- Achievements -->
        <section class="bg-white py-8 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                 <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-[#0f172a] text-center mb-12">Our Achievement</h2>
                <div class="rounded-3xl bg-[#0b314a] px-6 py-10 sm:px-10">
                    <div class="text-center">
                        <h2 class="mt-3 font-heading text-2xl font-extrabold text-white sm:text-3xl">
                            Trusted By Thousands of Patients
                        </h2>
                    </div>
                    <div class="mt-10 grid grid-cols-2 gap-y-8 md:grid-cols-4 md:gap-y-0">

                        <div class="text-center md:border-r md:border-white/10">
                            <p class="font-heading text-3xl font-extrabold text-teal-300">
                                {{ stats.years || '10+' }}
                            </p>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-white/50">
                                Years Experience
                            </p>
                        </div>
                        <div class="text-center md:border-r md:border-white/10">
                            <p class="font-heading text-3xl font-extrabold text-white">
                                {{ stats.doctors || '50+' }}
                            </p>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-white/50">
                                Expert Doctors
                            </p>
                        </div>
                        <div class="text-center md:border-r md:border-white/10">
                            <p class="font-heading text-3xl font-extrabold text-teal-300">
                                {{ stats.patients || '10,000+' }}
                            </p>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-white/50">
                                Patients Treated
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="font-heading text-3xl font-extrabold text-white">
                                {{ stats.satisfaction || '95%' }}
                            </p>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-white/50">
                                Success Rate
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>