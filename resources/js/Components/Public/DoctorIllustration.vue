<script setup>
defineProps({
    class: { type: String, default: 'w-full h-full' },
});
</script>

<template>
    <svg :class="$props.class" viewBox="0 0 400 520" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="coatGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#ffffff" />
                <stop offset="100%" stop-color="#eef6f5" />
            </linearGradient>
        </defs>

        <!-- soft ground shadow -->
        <ellipse cx="200" cy="500" rx="130" ry="18" fill="#0a2530" opacity="0.15" />

        <!-- decorative rings -->
        <circle cx="90" cy="90" r="34" fill="#ffffff" opacity="0.12" />
        <circle cx="330" cy="130" r="20" fill="#ffffff" opacity="0.15" />
        <circle cx="340" cy="420" r="28" fill="#ffffff" opacity="0.1" />

        <!-- legs -->
        <rect x="160" y="420" width="34" height="80" rx="14" fill="#164a45" />
        <rect x="206" y="420" width="34" height="80" rx="14" fill="#164a45" />

        <!-- coat body -->
        <path d="M128 500 L124 300 Q124 224 200 224 Q276 224 276 300 L272 500 Z" fill="url(#coatGrad)" />

        <!-- coat center opening -->
        <path d="M196 250 L200 500 M204 250 L200 500" stroke="#dce8e6" stroke-width="2" />

        <!-- scrub top -->
        <path d="M172 236 Q200 262 228 236 L232 320 Q200 340 168 320 Z" fill="#1f9a8f" />

        <!-- collar -->
        <path d="M172 236 L200 268 L228 236 L214 224 L200 240 L186 224 Z" fill="#eef6f5" />

        <!-- arms -->
        <path d="M124 320 Q108 360 118 410 L146 404 Q140 360 152 322 Z" fill="url(#coatGrad)" />
        <path d="M276 320 Q292 360 282 410 L254 404 Q260 360 248 322 Z" fill="url(#coatGrad)" />

        <!-- hands -->
        <circle cx="122" cy="412" r="14" fill="#f2c29c" />
        <circle cx="278" cy="412" r="14" fill="#f2c29c" />

        <!-- clipboard -->
        <rect x="100" y="392" width="40" height="52" rx="5" fill="#ffffff" stroke="#d9e6e4" stroke-width="2" />
        <rect x="112" y="386" width="16" height="10" rx="3" fill="#1f9a8f" />
        <rect x="108" y="406" width="24" height="4" rx="2" fill="#c9dad8" />
        <rect x="108" y="416" width="24" height="4" rx="2" fill="#c9dad8" />
        <rect x="108" y="426" width="16" height="4" rx="2" fill="#c9dad8" />

        <!-- neck -->
        <rect x="183" y="176" width="34" height="54" rx="16" fill="#f2c29c" />

        <!-- head -->
        <circle cx="200" cy="146" r="58" fill="#f6d2ab" />

        <!-- ears -->
        <circle cx="144" cy="150" r="9" fill="#f6d2ab" />
        <circle cx="256" cy="150" r="9" fill="#f6d2ab" />

        <!-- hair -->
        <path d="M142 140 Q140 84 200 82 Q260 84 258 140 Q252 104 200 100 Q148 104 142 140 Z" fill="#3d2b1f" />
        <path d="M142 138 Q140 168 152 190 L156 150 Z" fill="#3d2b1f" />
        <path d="M258 138 Q260 168 248 190 L244 150 Z" fill="#3d2b1f" />

        <!-- simple face -->
        <circle cx="178" cy="150" r="4.5" fill="#3d2b1f" />
        <circle cx="222" cy="150" r="4.5" fill="#3d2b1f" />
        <path d="M182 172 Q200 184 218 172" stroke="#c98a5e" stroke-width="4" fill="none" stroke-linecap="round" />
        <path d="M164 132 Q178 122 192 130" stroke="#3d2b1f" stroke-width="4" fill="none" stroke-linecap="round" />
        <path d="M208 130 Q222 122 236 132" stroke="#3d2b1f" stroke-width="4" fill="none" stroke-linecap="round" />

        <!-- stethoscope -->
        <path d="M158 234 Q146 268 164 292 Q182 314 204 298" stroke="#1f9a8f" stroke-width="7" fill="none" stroke-linecap="round" />
        <path d="M242 234 Q254 268 236 292 Q222 310 204 298" stroke="#1f9a8f" stroke-width="7" fill="none" stroke-linecap="round" />
        <circle cx="204" cy="300" r="11" fill="#167c74" />
        <circle cx="158" cy="236" r="9" fill="#1f9a8f" />
        <circle cx="242" cy="236" r="9" fill="#1f9a8f" />

        <!-- id badge -->
        <rect x="220" y="270" width="20" height="26" rx="3" fill="#ffffff" stroke="#d9e6e4" stroke-width="2" />
        <circle cx="230" cy="279" r="4" fill="#1f9a8f" />
    </svg>
</template>