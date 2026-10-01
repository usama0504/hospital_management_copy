<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';

const props = defineProps({
    doctor: { type: Object, required: true },
    reviews: { type: Array, default: () => [] },
    related: { type: Array, default: () => [] },
});

const page = usePage();

const tabs = ['About', 'Availability', 'Reviews'];
const activeTab = ref('About');

const avgRating = computed(() => Number(props.doctor.reviews_avg_rating || 0));
const reviewsCount = computed(() => props.doctor.reviews_count || props.reviews.length || 0);

const reviewForm = useForm({
    patient_name: '',
    rating: 0,
    comment: '',
});

const setRating = (n) => { reviewForm.rating = n; };

const submitReview = () => {
    reviewForm.post(route('public.doctors.reviews.store', props.doctor.id), {
        preserveScroll: true,
        onSuccess: () => reviewForm.reset(),
    });
};

const formatDate = (value) => {
    if (!value) return '';
    return new Date(value).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

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
                        <Icon v-for="i in 5" :key="i" name="star" class="w-4 h-4"
                            :class="i <= Math.round(avgRating) ? 'fill-current' : 'text-slate-200'" />
                        <span class="text-xs text-slate-400 ml-1">
                            {{ reviewsCount ? `${avgRating} (${reviewsCount} review${reviewsCount === 1 ? '' : 's'})` : 'No reviews yet' }}
                        </span>
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

                <div v-if="activeTab === 'Reviews'">
                    <div v-if="page.props.flash?.success" class="mb-6 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-semibold px-4 py-3">
                        {{ page.props.flash.success }}
                    </div>

                    <h2 class="font-heading font-bold text-lg text-slate-900 mb-4">Patient Reviews</h2>
                    <div v-if="reviews.length" class="space-y-4 mb-10">
                        <div v-for="r in reviews" :key="r.id" class="border border-slate-100 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-1.5">
                                <p class="text-sm font-bold text-slate-800">{{ r.patient_name }}</p>
                                <span class="text-xs text-slate-400">{{ formatDate(r.created_at) }}</span>
                            </div>
                            <div class="flex items-center gap-0.5 text-amber-500 mb-2">
                                <Icon v-for="i in 5" :key="i" name="star" class="w-3.5 h-3.5"
                                    :class="i <= r.rating ? 'fill-current' : 'text-slate-200'" />
                            </div>
                            <p v-if="r.comment" class="text-sm text-slate-500 leading-relaxed">{{ r.comment }}</p>
                        </div>
                    </div>
                    <p v-else class="text-sm text-slate-400 mb-10">No reviews yet &mdash; be the first to share your experience.</p>

                    <div class="border border-slate-100 rounded-xl p-5">
                        <h3 class="font-heading font-bold text-slate-900 mb-4">Write a Review</h3>
                        <form @submit.prevent="submitReview" class="space-y-4">
                            <div>
                                <label class="text-xs font-bold text-slate-500 mb-1.5 block">Your Name</label>
                                <input v-model="reviewForm.patient_name" type="text" placeholder="Your name"
                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                                <p v-if="reviewForm.errors.patient_name" class="text-xs text-rose-500 mt-1">{{ reviewForm.errors.patient_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-500 mb-1.5 block">Rating</label>
                                <div class="flex items-center gap-1">
                                    <button v-for="i in 5" :key="i" type="button" @click="setRating(i)">
                                        <Icon name="star" class="w-6 h-6 text-amber-500"
                                            :class="i <= reviewForm.rating ? 'fill-current' : 'text-slate-200'" />
                                    </button>
                                </div>
                                <p v-if="reviewForm.errors.rating" class="text-xs text-rose-500 mt-1">{{ reviewForm.errors.rating }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-500 mb-1.5 block">Comment (optional)</label>
                                <textarea v-model="reviewForm.comment" rows="3" placeholder="Share your experience..."
                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400"></textarea>
                            </div>
                            <button type="submit" :disabled="reviewForm.processing"
                                class="bg-brand-600 text-white font-bold text-sm px-6 py-2.5 rounded-xl hover:bg-brand-700 transition disabled:opacity-60">
                                {{ reviewForm.processing ? 'Posting...' : 'Post Review' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-2xl border border-slate-100 p-6">
                    <h3 class="font-heading font-bold text-slate-900 mb-4">Consultation Fee</h3>
                    <p v-if="doctor.consultation_fee" class="font-heading font-extrabold text-2xl text-brand-600">
                        Rs. {{ doctor.consultation_fee }}
                    </p>
                    <p v-else class="font-heading font-bold text-lg text-slate-500">Contact for pricing</p>
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