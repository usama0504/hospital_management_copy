<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    images: { type: Array, default: () => [] },
    limit: { type: Number, default: 0 },       // 0 = sab dikhao
    showFilters: { type: Boolean, default: true },
});

const categories = ['All', 'Facilities', 'Departments', 'Events'];
const active = ref('All');

const filtered = computed(() => {
    const list = active.value === 'All' ? props.images : props.images.filter((i) => i.category === active.value);
    return props.limit > 0 ? list.slice(0, props.limit) : list;
});
</script>

<template>
    <div>
        <div v-if="showFilters" class="flex flex-wrap justify-center gap-2 mb-10">
            <button v-for="c in categories" :key="c" @click="active = c"
                :class="['px-5 py-2 rounded-full text-sm font-bold transition', active === c ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200']">
                {{ c }}
            </button>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            <div v-for="img in filtered" :key="img.title"
                class="group relative aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 shadow-sm hover:shadow-xl transition-shadow">
                <img :src="img.image" :alt="img.title"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                <p class="absolute bottom-3 left-4 right-4 text-white text-sm font-bold">{{ img.title }}</p>
            </div>
        </div>
    </div>
</template>