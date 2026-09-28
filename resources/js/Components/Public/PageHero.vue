<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    bgImage: { type: String, default: null },
});
</script>

<template>
    <section 
        class="relative bg-slate-900 py-16 sm:py-24 overflow-hidden text-center bg-center bg-no-repeat"
        :style="bgImage ? `background-image: url('${bgImage}'); background-size: 100% 100%;` : ''"
    >
        <!-- Halka overlay taaki text saaf dikhe lekin image poori nazar aaye -->
        <div v-if="bgImage" class="absolute inset-0 bg-slate-900/40 z-0"></div>

        <!-- Fallback Gradient agar image na ho -->
        <div v-else class="absolute inset-0 bg-gradient-to-br from-brand-900 via-slate-900 to-slate-900 z-0"></div>

        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-brand-600/20 blur-3xl z-0"></div>
        <div class="absolute -left-24 -bottom-24 w-96 h-96 rounded-full bg-brand-500/10 blur-3xl z-0"></div>

        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">{{ title }}</h1>
            <p v-if="subtitle" class="mt-3 text-white font-bold text-xl max-w-2xl mx-auto">{{ subtitle }}</p>
        </div>
    </section>
</template>