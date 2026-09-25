<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: { type: String, default: '' },
    photoUrl: { type: String, default: null },
    size: { type: String, default: 'md' }, // sm | md | lg | xl
});

const initials = computed(() => {
    return props.name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
});

const palette = [
    'from-brand-400 to-brand-600',
    'from-sky-400 to-sky-600',
    'from-violet-400 to-violet-600',
    'from-rose-400 to-rose-600',
    'from-amber-400 to-amber-600',
];

const gradient = computed(() => {
    const code = (props.name || '').split('').reduce((a, c) => a + c.charCodeAt(0), 0);
    return palette[code % palette.length];
});

const sizes = {
    sm: 'w-10 h-10 text-xs',
    md: 'w-16 h-16 text-lg',
    lg: 'w-24 h-24 text-2xl',
    xl: 'w-36 h-36 text-4xl',
};
</script>

<template>
    <img v-if="photoUrl" :src="photoUrl" :alt="name"
        :class="[sizes[size], 'rounded-full object-cover ring-2 ring-white shadow-sm']" />
    <div v-else
        :class="[sizes[size], 'rounded-full bg-gradient-to-br flex items-center justify-center font-heading font-bold text-white ring-2 ring-white shadow-sm', gradient]">
        {{ initials || '?' }}
    </div>
</template>