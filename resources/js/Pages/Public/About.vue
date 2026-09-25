<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

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

const facilities = ['ICU', 'Operation Theatre', 'Emergency', 'Laboratory'];
</script>

<template>
    <PublicLayout>
        <PageHero title="About Us" subtitle="Compassionate Care for a Healthier Tomorrow" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-brand-600 font-bold text-sm mb-2">Our Story</p>
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 mb-4">Built Around Our
                    Patients, For Over a Decade</h2>
                <p class="text-slate-500 leading-relaxed mb-4">CarePlus was founded with a simple goal: make quality
                    healthcare accessible to everyone. What started as a small clinic has grown into a full-service
                    hospital with specialists across nine departments.</p>
                <p class="text-slate-500 leading-relaxed">Today, our team of experienced doctors and modern
                    facilities serve thousands of patients every year, always guided by the same promise of
                    compassionate, patient-first care.</p>
            </div>
            <div class="aspect-video rounded-3xl bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center">
                <Icon name="building" class="w-24 h-24 text-brand-400" />
            </div>
        </section>

        <section class="bg-slate-50 py-16 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid sm:grid-cols-3 gap-6">
                <div v-for="v in values" :key="v.title" class="bg-white rounded-2xl p-7 border border-slate-100 text-center">
                    <span class="w-12 h-12 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center mx-auto mb-4">
                        <Icon :name="v.icon" class="w-6 h-6" />
                    </span>
                    <h3 class="font-heading font-bold text-slate-900 mb-2">{{ v.title }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ v.desc }}</p>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-10">Why Choose Us</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div v-for="w in whyUs" :key="w.title" class="p-6 rounded-2xl border border-slate-100 text-center hover:border-brand-200 transition">
                    <span class="w-11 h-11 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-3">
                        <Icon :name="w.icon" class="w-5 h-5" />
                    </span>
                    <h4 class="font-heading font-bold text-sm text-slate-900 mb-1">{{ w.title }}</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ w.desc }}</p>
                </div>
            </div>
        </section>

        <section class="bg-slate-50 py-16 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-10">Our Facilities</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div v-for="f in facilities" :key="f"
                        class="aspect-video rounded-2xl bg-gradient-to-br from-brand-100 to-brand-200 flex items-end p-4">
                        <p class="font-heading font-bold text-brand-800 text-sm">{{ f }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
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
                    <p class="text-xs text-slate-400 font-semibold mt-1">Patients Treated</p>
                </div>
                <div>
                    <p class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-600">{{ stats.satisfaction }}</p>
                    <p class="text-xs text-slate-400 font-semibold mt-1">Success Rate</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>