<script setup>
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

const props = defineProps({
    images: { type: Array, default: () => [] },
});

const categories = ['All', 'Facilities', 'Departments', 'Events'];
const active = ref('All');

const filtered = computed(() => {
    if (active.value === 'All') return props.images;
    return props.images.filter((i) => i.category === active.value);
});

const gradients = [
    'from-brand-100 to-brand-200',
    'from-sky-100 to-sky-200',
    'from-violet-100 to-violet-200',
    'from-amber-100 to-amber-200',
];
</script>

<template>
    <PublicLayout>
        <PageHero title="Our Gallery" subtitle="Take a Look at Our Facilities" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="flex flex-wrap justify-center gap-2 mb-10">
                <button v-for="c in categories" :key="c" @click="active = c"
                    :class="['px-5 py-2 rounded-full text-sm font-bold transition', active === c ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200']">
                    {{ c }}
                </button>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div v-for="(img, i) in filtered" :key="img.title"
                    :class="['aspect-square rounded-2xl bg-gradient-to-br flex flex-col items-center justify-center gap-2 p-4 text-center', gradients[i % gradients.length]]">
                    <Icon name="building" class="w-8 h-8 text-slate-500/60" />
                    <p class="text-xs font-bold text-slate-600">{{ img.title }}</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>