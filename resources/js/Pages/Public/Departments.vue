<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

import departBg from '@/images/department-bg.png';

const props = defineProps({
    departments: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search || '');
const applySearch = () => {
    router.get('/our-departments', { search: search.value || undefined }, { preserveState: true, replace: true });
};

const iconFor = (name) => props.meta?.[name]?.icon || 'stethoscope';
</script>

<template>
    <PublicLayout>
        <PageHero title="Our Departments" subtitle="Specialized Care for Every Stage of Life"  :bgImage="departBg" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="relative max-w-md mb-10">
                <Icon name="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
                <input v-model="search" @keyup.enter="applySearch" type="text" placeholder="Search departments..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
            </div>
            <div v-if="departments.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="d in departments" :key="d.id" :href="`/our-departments/${d.id}`"
                    class="group rounded-2xl border border-slate-100 hover:border-brand-200 hover:shadow-lg hover:shadow-slate-200/60 transition-all bg-white overflow-hidden">
                    <div class="h-40 bg-brand-50 overflow-hidden">
                        <img v-if="d.image_url" :src="d.image_url" :alt="d.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        <div v-else class="w-full h-full flex items-center justify-center text-brand-300">
                            <Icon :name="iconFor(d.name)" class="w-12 h-12" />
                        </div>
                    </div>
                    <div class="p-7">
                        <span v-if="!d.image_url"
                            class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-5 group-hover:bg-brand-600 group-hover:text-white transition-colors">
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
                    </div>
                </Link>
            </div>
            <div v-else class="text-center py-20">
                <p class="text-slate-400">
                    {{ search ? 'No departments match your search.' : 'Departments will appear here once they are added from the admin panel.' }}
                </p>
            </div>
        </section>
    </PublicLayout>
</template>