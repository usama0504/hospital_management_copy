<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';

const props = defineProps({
    doctors: { type: Object, required: true },
    departments: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search || '');
const departmentId = ref(props.filters.department_id || '');

const applyFilters = () => {
    router.get('/our-doctors', {
        search: search.value || undefined,
        department_id: departmentId.value || undefined,
    }, { preserveState: true, replace: true });
};
</script>

<template>
    <PublicLayout>
        <PageHero title="Our Doctors" subtitle="Meet Our Experienced Medical Professionals" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="flex flex-col sm:flex-row gap-3 mb-10">
                <div class="relative flex-1">
                    <Icon name="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
                    <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Search doctors..."
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
                </div>
                <select v-model="departmentId" @change="applyFilters"
                    class="rounded-xl border border-slate-200 text-sm py-3 px-4 focus:border-brand-400 focus:ring-brand-400">
                    <option value="">All Departments</option>
                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
                <button @click="applyFilters"
                    class="bg-brand-600 text-white font-bold text-sm px-6 py-3 rounded-xl hover:bg-brand-700 transition">
                    Search
                </button>
            </div>

            <div v-if="doctors.data.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="doc in doctors.data" :key="doc.id"
                    class="bg-white rounded-2xl p-6 text-center border border-slate-100 hover:shadow-lg hover:shadow-slate-200/60 transition-all">
                    <Avatar :name="doc.name" :photo-url="doc.photo_url" size="lg" class="mx-auto mb-4" />
                    <h3 class="font-heading font-bold text-slate-900">Dr. {{ doc.name }}</h3>
                    <p class="text-xs text-brand-600 font-semibold mb-4">{{ doc.department?.name || doc.specialization }}</p>
                    <Link :href="`/our-doctors/${doc.id}`"
                        class="block text-xs font-bold bg-brand-600 text-white py-2.5 rounded-lg hover:bg-brand-700 transition">
                        View Profile
                    </Link>
                </div>
            </div>
            <div v-else class="text-center py-20">
                <p class="text-slate-400">No doctors match your search.</p>
            </div>

            <div v-if="doctors.links && doctors.links.length > 3" class="flex items-center justify-center gap-1.5 mt-12">
                <template v-for="(link, i) in doctors.links" :key="i">
                    <Link v-if="link.url" :href="link.url" preserve-state preserve-scroll
                        v-html="link.label"
                        :class="['w-9 h-9 flex items-center justify-center rounded-lg text-xs font-bold', link.active ? 'bg-brand-600 text-white' : 'bg-white border border-slate-200 text-slate-500 hover:border-brand-300']" />
                    <span v-else v-html="link.label"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-xs font-bold text-slate-300" />
                </template>
            </div>
        </section>
    </PublicLayout>
</template>