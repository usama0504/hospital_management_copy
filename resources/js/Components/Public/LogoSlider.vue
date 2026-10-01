<script setup>
import { computed } from 'vue';

const props = defineProps({
    logos: { type: Array, default: () => [] }, 
    speed: { type: Number, default: 6 },      
});

// Loop bina jhatke ke chalay isliye list do baar
const loopLogos = computed(() => [...props.logos, ...props.logos]);
const duration = computed(() => `${Math.max(props.logos.length, 1) * props.speed}s`);
</script>

<template>
    <div v-if="logos.length" class="logo-wrap overflow-hidden rounded-2xl bg-[#eef2f6] py-8">
        <div class="logo-track flex w-max items-center bg-[#eef2f6]" :style="{ animationDuration: duration }">
            <div v-for="(l, i) in loopLogos" :key="i"
                class="w-[210px] sm:w-[250px] shrink-0 flex flex-col items-center justify-center px-4 gap-3">
                
                <!-- Logo Image Container (Height badha di hai taake cut na ho) -->
                <div class="h-[60px] flex items-center justify-center">
                    <img v-if="l.image" :src="l.image" :alt="l.name"
                        class="logo-img max-w-[150px] max-h-[60px] object-contain"
                        :style="l.scale ? { transform: `scale(${l.scale})` } : null" />
                </div>
                
                <!-- Neeche Text -->
                <!-- <span class="text-xs font-semibold text-slate-600 text-center tracking-wide truncate max-w-full">
                    {{ l.name }}
                </span> -->
            </div>
        </div>
    </div>
</template>

<style scoped>
.logo-wrap {
    -webkit-mask-image: linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
    mask-image: linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
}
.logo-track {
    animation: logo-scroll linear infinite;
}
.logo-wrap:hover .logo-track {
    animation-play-state: paused;
}
.logo-img {
    mix-blend-mode: multiply;
    filter: grayscale(100%);
    opacity: 0.8;
    transition: filter 0.3s, opacity 0.3s;
}
.logo-img:hover {
    filter: grayscale(0);
    opacity: 1;
}
@keyframes logo-scroll {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}
</style>