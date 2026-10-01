<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';
import CustomDropdown from '@/Components/CustomDropdown.vue'; // Custom dropdown component import kiya hai

import doctorBg from '@/images/doctor-bg.png';

const props = defineProps({
    doctors: { type: Object, required: true },
    departments: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search || '');
const departmentId = ref(props.filters.department_id || '');
const gender = ref(props.filters.gender || '');
const sort = ref(props.filters.sort || '');

// Department options ko label-value format mein convert karna
const departmentOptions = computed(() => [
    { label: 'All Departments', value: '' },
    ...props.departments.map(d => ({ label: d.name, value: d.id }))
]);

// Gender options list
const genderOptions = [
    { label: 'Any Gender', value: '' },
    { label: 'Male', value: 'Male' },
    { label: 'Female', value: 'Female' },
    { label: 'Other', value: 'Other' }
];

// Sort options list
const sortOptions = [
    { label: 'Sort: Name (A-Z)', value: '' },
    { label: 'Sort: Highest Rated', value: 'rating' },
    { label: 'Sort: Fee (Low to High)', value: 'fee_low' },
    { label: 'Sort: Fee (High to Low)', value: 'fee_high' }
];

const applyFilters = () => {
    router.get('/our-doctors', {
        search: search.value || undefined,
        department_id: departmentId.value || undefined,
        gender: gender.value || undefined,
        sort: sort.value || undefined,
    }, { preserveState: true, replace: true });
};

const hasActiveFilters = computed(() => search.value || departmentId.value || gender.value || sort.value);
const clearFilters = () => {
    search.value = '';
    departmentId.value = '';
    gender.value = '';
    sort.value = '';
    applyFilters();
};
</script> 

<template>
    <PublicLayout>
        <PageHero title="Our Doctors" subtitle="Meet Our Experienced Medical Professionals" :bgImage="doctorBg" />

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <!-- Filters Section -->
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 mb-10 items-center">
                
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[200px] w-full">
                    <Icon name="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
                    <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Search by name or specialization..."
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-brand-400 focus:ring-brand-400 outline-none" />
                </div>

                <!-- Department Custom Dropdown -->
                <div class="w-full sm:w-52">
                    <CustomDropdown 
                        v-model="departmentId" 
                        :options="departmentOptions" 
                        placeholder="All Departments"
                        @change="applyFilters" 
                    />
                </div>

                <!-- Gender Custom Dropdown -->
                <div class="w-full sm:w-44">
                    <CustomDropdown 
                        v-model="gender" 
                        :options="genderOptions" 
                        placeholder="Any Gender"
                        @change="applyFilters" 
                    />
                </div>

                <!-- Sort Custom Dropdown -->
                <div class="w-full sm:w-52">
                    <CustomDropdown 
                        v-model="sort" 
                        :options="sortOptions" 
                        placeholder="Sort By"
                        @change="applyFilters" 
                    />
                </div>

                <!-- Search Button -->
                <button @click="applyFilters"
                    class="bg-brand-600 text-white font-bold text-sm px-6 py-3 rounded-xl hover:bg-brand-700 transition w-full sm:w-auto cursor-pointer">
                    Search
                </button>

                <!-- Clear Filters Button -->
                <button v-if="hasActiveFilters" @click="clearFilters" type="button"
                    class="text-sm font-bold text-slate-400 hover:text-slate-600 px-3 transition cursor-pointer">
                    Clear
                </button>
            </div>

            <!-- Doctors Grid List -->
            <div v-if="doctors.data.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="doc in doctors.data" :key="doc.id"
                    class="bg-white rounded-2xl p-6 text-center border border-slate-100 hover:shadow-lg hover:shadow-slate-200/60 transition-all">
                    <Avatar :name="doc.name" :photo-url="doc.photo_url" size="lg" class="mx-auto mb-4" />
                    <h3 class="font-heading font-bold text-slate-900">Dr. {{ doc.name }}</h3>
                    <p class="text-xs text-brand-600 font-semibold mb-2">{{ doc.department?.name || doc.specialization }}</p>
                    <p v-if="doc.reviews_count" class="flex items-center justify-center gap-1 text-[11px] font-bold text-amber-500 mb-2">
                        <Icon name="star" class="w-3.5 h-3.5 fill-current" />
                        {{ doc.reviews_avg_rating }} <span class="text-slate-400 font-medium">({{ doc.reviews_count }})</span>
                    </p>
                    <p v-if="doc.consultation_fee" class="text-[11px] font-bold text-slate-500 mb-4">
                        Consultation: <span class="text-brand-700">Rs. {{ doc.consultation_fee }}</span>
                    </p>
                    <div v-else class="mb-4"></div>
                    <Link :href="`/our-doctors/${doc.id}`"
                        class="block text-xs font-bold bg-brand-600 text-white py-2.5 rounded-lg hover:bg-brand-700 transition">
                        View Profile
                    </Link>
                </div>
            </div>

            <!-- No Results Message -->
            <div v-else class="text-center py-20">
                <p class="text-slate-400">No doctors match your search.</p>
            </div>

            <!-- Pagination Links -->
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