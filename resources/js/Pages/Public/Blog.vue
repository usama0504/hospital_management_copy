<script setup>
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';

const props = defineProps({
    posts: { type: Array, default: () => [] },
});

const search = ref('');
const filtered = computed(() => {
    if (!search.value) return props.posts;
    return props.posts.filter((p) => p.title.toLowerCase().includes(search.value.toLowerCase()));
});
</script>

<template>
    <PublicLayout>
        <PageHero title="Latest News" subtitle="Stay Updated with Our Health Tips and News" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="relative max-w-md mx-auto mb-12">
                <Icon name="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
                <input v-model="search" type="text" placeholder="Search articles..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
                <article v-for="p in filtered" :key="p.title"
                    class="rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg hover:shadow-slate-200/60 transition-all flex gap-4 p-4">
                    <div class="w-28 h-28 rounded-xl bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center shrink-0">
                        <Icon name="heart" class="w-8 h-8 text-brand-400" />
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-semibold mb-1">
                            <span class="text-brand-600">{{ p.category }}</span>
                            <span>&middot;</span>
                            <span>{{ p.date }}</span>
                        </div>
                        <h3 class="font-heading font-bold text-slate-900 mb-1">{{ p.title }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">{{ p.excerpt }}</p>
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 mt-2">
                            Read More <Icon name="arrow-right" class="w-3 h-3" />
                        </span>
                    </div>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>