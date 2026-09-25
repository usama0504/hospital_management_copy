<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';

const props = defineProps({
    doctor: { type: Object, required: true },
    related: { type: Array, default: () => [] },
});

const tabs = ['About', 'Availability'];
const activeTab = ref('About');

const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
const availability = computed(() => {
    return [...(props.doctor.availabilities || [])].sort(
        (a, b) => dayOrder.indexOf(a.day_of_week) - dayOrder.indexOf(b.day_of_week)
    );
});

const formatTime = (t) => {
    if (!t) return '';
    const [h, m] = t.split(':');
    const hour = parseInt(h, 10);
    const suffix = hour >= 12 ? 'PM' : 'AM';
    const h12 = hour % 12 === 0 ? 12 : hour % 12;
    return `${h12}:${m} ${suffix}`;
};
</script>

<template>
    <PublicLayout>
        <section class="bg-slate-50 py-10 sm:py-14">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid sm:grid-cols-[auto,1fr] gap-6 items-center">
                <Avatar :name="doctor.name" :photo-url="doctor.photo_url" size="xl" />
                <div>
                    <h1 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">Dr. {{ doctor.name }}</h1>
                    <p class="text-brand-600 font-semibold mt-1">{{ doctor.department?.name || doctor.specialization }}</p>
                    <p v-if="doctor.experience_years" class="text-sm text-slate-400 mt-1">{{ doctor.experience_years }}+ Years Experience</p>
                    <div class="flex items-center gap-1 mt-2 text-amber-500">
                        <Icon v-for="i in 5" :key="i" name="star" class="w-4 h-4 fill-current" />
                        <span class="text-xs text-slate-400 ml-1">4.9 (Patient Rated)</span>
                    </div>
                    <Link :href="`/book-appointment?doctor_id=${doctor.id}`"
                        class="inline-flex mt-4 items-center gap-2 bg-brand-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-brand-700 transition">
                        Book Appointment
                    </Link>
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 grid lg:grid-cols-[1fr,320px] gap-10">
            <div>
                <div class="flex gap-2 border-b border-slate-100 mb-6">
                    <button v-for="t in tabs" :key="t" @click="activeTab = t"
                        :class="['px-4 py-2.5 text-sm font-bold border-b-2 -mb-px transition-colors', activeTab === t ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-400 hover:text-slate-600']">
                        {{ t }}
                    </button>
                </div>

                <div v-if="activeTab === 'About'">
                    <h2 class="font-heading font-bold text-lg text-slate-900 mb-3">About</h2>
                    <p class="text-slate-500 leading-relaxed">
                        {{ doctor.bio || `Dr. ${doctor.name} is a dedicated ${doctor.specialization} specialist committed to providing personalized, evidence-based care for every patient.` }}
                    </p>
                </div>

                <div v-else>
                    <h2 class="font-heading font-bold text-lg text-slate-900 mb-4">Working Days</h2>
                    <div v-if="availability.length" class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
                        <div v-for="a in availability" :key="a.id" class="flex items-center justify-between px-5 py-3.5 text-sm">
                            <span class="font-semibold text-slate-700">{{ a.day_of_week }}</span>
                            <span class="text-slate-400">{{ formatTime(a.start_time) }} - {{ formatTime(a.end_time) }}</span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-slate-400">Availability schedule will be published soon &mdash; please call to confirm timing.</p>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-2xl border border-slate-100 p-6">
                    <h3 class="font-heading font-bold text-slate-900 mb-4">Consultation Fee</h3>
                    <p class="font-heading font-extrabold text-2xl text-brand-600">Rs. {{ doctor.consultation_fee || '1,500' }}</p>
                    <p class="text-xs text-slate-400 mt-1">Per visit</p>
                </div>
                <div v-if="related.length" class="rounded-2xl border border-slate-100 p-6">
                    <h3 class="font-heading font-bold text-slate-900 mb-4">Other Specialists</h3>
                    <div class="space-y-3">
                        <Link v-for="r in related" :key="r.id" :href="`/our-doctors/${r.id}`"
                            class="flex items-center gap-3 hover:bg-slate-50 rounded-lg p-2 -mx-2 transition">
                            <Avatar :name="r.name" :photo-url="r.photo_url" size="sm" />
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">Dr. {{ r.name }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ r.specialization }}</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </aside>
        </section>
    </PublicLayout>
</template>