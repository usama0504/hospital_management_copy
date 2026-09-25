<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <section class="relative bg-slate-900 py-16 sm:py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-900 via-slate-900 to-slate-900"></div>
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-brand-600/20 blur-3xl"></div>
        <div class="absolute -left-24 -bottom-24 w-96 h-96 rounded-full bg-brand-500/10 blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">{{ title }}</h1>
            <p v-if="subtitle" class="mt-3 text-slate-300 max-w-2xl mx-auto">{{ subtitle }}</p>
        </div>
    </section>
</template>