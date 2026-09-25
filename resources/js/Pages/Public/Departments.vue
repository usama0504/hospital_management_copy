<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

const props = defineProps({
    departments: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
});

const iconFor = (name) => props.meta?.[name]?.icon || 'stethoscope';
</script>

<template>
    <PublicLayout>
        <PageHero title="Our Departments" subtitle="Specialized Care for Every Stage of Life" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div v-if="departments.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="d in departments" :key="d.id" :href="`/our-departments/${d.id}`"
                    class="group p-7 rounded-2xl border border-slate-100 hover:border-brand-200 hover:shadow-lg hover:shadow-slate-200/60 transition-all bg-white">
                    <span class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-5 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                        <Icon :name="iconFor(d.name)" class="w-7 h-7" />
                    </span>
                    <h3 class="font-heading font-bold text-lg text-slate-900 mb-2">{{ d.name }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4 line-clamp-2">
                        {{ d.description || 'Specialized, compassionate care delivered by an experienced team.' }}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">{{ d.doctors_count ?? 0 }} Doctors</span>
                        <span class="inline-flex items-center gap-1 text-sm font-bold text-brand-600 group-hover:gap-2 transition-all">
                            View Details <Icon name="arrow-right" class="w-4 h-4" />
                        </span>
                    </div>
                </Link>
            </div>
            <div v-else class="text-center py-20">
                <p class="text-slate-400">Departments will appear here once they are added from the admin panel.</p>
            </div>
        </section>
    </PublicLayout>
</template>