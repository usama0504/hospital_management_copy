<script setup>
import { useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post('/contact', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <PublicLayout>
        <PageHero title="Contact Us" subtitle="We're Here to Help You" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid lg:grid-cols-[1fr,1.2fr] gap-12">
            <div>
                <h2 class="font-heading font-bold text-xl text-slate-900 mb-6">Get In Touch</h2>
                <div class="space-y-5 mb-10">
                    <div class="flex items-start gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <Icon name="map-pin" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Our Location</p>
                            <p class="text-sm text-slate-400">123 Health St, Lahore, Pakistan</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <Icon name="phone" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Contact Information</p>
                            <p class="text-sm text-slate-400">+92 300 1234567</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <Icon name="mail" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Email</p>
                            <p class="text-sm text-slate-400">info@careplus.com</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <Icon name="clock" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Working Hours</p>
                            <p class="text-sm text-slate-400">Mon - Fri: 8:00 AM - 8:00 PM<br />Sat - Sun: 9:00 AM -
                                5:00 PM</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-rose-50 border border-rose-100 p-5 flex items-center gap-3">
                    <span
                        class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <Icon name="ambulance" class="w-5 h-5" />
                    </span>
                    <div>
                        <p class="text-sm font-bold text-rose-700">Emergency</p>
                        <p class="text-xs text-rose-400">+92 300 0234567 &middot; Available 24/7</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 p-7 sm:p-8">
                <div v-if="$page.props.flash?.success"
                    class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-5 py-4">
                    {{ $page.props.flash.success }}
                </div>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1.5 block">Name</label>
                            <input v-model="form.name" type="text"
                                class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                            <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1.5 block">Email</label>
                            <input v-model="form.email" type="email"
                                class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                            <p v-if="form.errors.email" class="text-xs text-rose-500 mt-1">{{ form.errors.email }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Phone</label>
                        <input v-model="form.phone" type="text"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Subject</label>
                        <input v-model="form.subject" type="text"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1.5 block">Message</label>
                        <textarea v-model="form.message" rows="5"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400"></textarea>
                        <p v-if="form.errors.message" class="text-xs text-rose-500 mt-1">{{ form.errors.message }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing"
                        class="w-full bg-brand-600 text-white font-bold py-3.5 rounded-xl hover:bg-brand-700 transition disabled:opacity-60">
                        {{ form.processing ? 'Sending...' : 'Send Message' }}
                    </button>
                </form>
            </div>
        </section>
    </PublicLayout>
</template>