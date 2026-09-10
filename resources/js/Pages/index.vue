<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    recentAppointments: Array
});
</script>

<template>
    <AuthenticatedLayout>
        <!-- Aapka page ka sara content yahan aayega -->
        <div class="py-8">
            <h2 class="text-2xl font-black text-gray-900">Appointments List</h2>
            <!-- Table content... -->
        </div>
    </AuthenticatedLayout>
</template>