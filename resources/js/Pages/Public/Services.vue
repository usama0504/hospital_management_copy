<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

const props = defineProps({
    services: { type: Array, default: () => [] },
});

const search = ref('');
const filtered = computed(() => {
    if (!search.value) return props.services;
    return props.services.filter((s) => s.name.toLowerCase().includes(search.value.toLowerCase()));
});
</script>

<template>
    <PublicLayout>
        <PageHero title="Our Medical Services" subtitle="Comprehensive Healthcare Services For You" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="relative max-w-md mx-auto mb-12">
                <Icon name="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
                <input v-model="search" type="text" placeholder="Search services..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="s in filtered" :key="s.name"
                    class="p-7 rounded-2xl border border-slate-100 hover:border-brand-200 hover:shadow-lg hover:shadow-slate-200/60 transition-all bg-white">
                    <span class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4">
                        <Icon :name="s.icon" class="w-6 h-6" />
                    </span>
                    <h3 class="font-heading font-bold text-slate-900 mb-2">{{ s.name }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4">{{ s.desc }}</p>
                    <Link href="/book-appointment" class="inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">
                        View Details <Icon name="arrow-right" class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>