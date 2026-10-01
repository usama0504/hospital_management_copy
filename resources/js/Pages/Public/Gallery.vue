<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import GalleryGrid from '@/Components/Public/GalleryGrid.vue';
import { galleryImages } from '@/data/galleryImages';

import galleryBg from '@/images/galleryBg.png';
</script>

<template>
    <PublicLayout>
        <PageHero title="Our Gallery" subtitle="Inside our hospital a picture tour of our Facility" :bgImage="galleryBg" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <GalleryGrid :images="galleryImages" />
        </section>
    </PublicLayout>
</template>