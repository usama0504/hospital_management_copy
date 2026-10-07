<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    reviews: Object,
    filters: Object,
    pendingCount: Number,
});

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? 'pending');

const applyFilters = () => {
    router.get(route('reviews.index'), {
        status: status.value,
        search: search.value || undefined,
    }, { preserveState: true, replace: true });
};

let timer = null;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(applyFilters, 400);
});

const setStatus = (value) => {
    status.value = value;
    applyFilters();
};

const approve = (id) => router.patch(route('reviews.approve', id), {}, { preserveScroll: true });
const hide = (id) => router.patch(route('reviews.hide', id), {}, { preserveScroll: true });
const remove = (id) => {
    if (confirm('Delete this review permanently?')) {
        router.delete(route('reviews.destroy', id), { preserveScroll: true });
    }
};

const formatDate = (d) => new Date(d).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });

const tabs = [
    { key: 'pending', label: 'Pending' },
    { key: 'approved', label: 'Approved' },
    { key: 'all', label: 'All' },
];
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight">Doctor Reviews</h2>
                    <p class="text-[11px] sm:text-xs text-gray-500 mt-1 font-medium">
                        Reviews posted on the public website. Only approved reviews are shown to visitors.
                        <span v-if="pendingCount" class="text-orange-600 font-bold">{{ pendingCount }} waiting for approval.</span>
                    </p>
                </div>
                <input v-model="search" type="text" placeholder="Search name, doctor or comment..."
                    class="w-full sm:w-72 text-xs rounded-xl border border-gray-200 px-3.5 py-2.5 focus:border-orange-400 focus:ring-0" />
            </div>

            <div v-if="$page.props.flash?.success"
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ $page.props.flash.success }}
            </div>

            <div class="flex gap-2">
                <button v-for="t in tabs" :key="t.key" type="button" @click="setStatus(t.key)"
                    class="px-4 py-2 rounded-xl text-xs font-bold border transition"
                    :class="status === t.key ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'">
                    {{ t.label }}
                </button>
            </div>

            <div v-if="!reviews.data.length"
                class="bg-white border border-gray-100 shadow-sm rounded-2xl py-16 text-center">
                <p class="text-xs font-semibold text-gray-500">No reviews found.</p>
            </div>

            <div v-else class="space-y-3">
                <div v-for="r in reviews.data" :key="r.id"
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-black text-gray-900">{{ r.patient_name }}</p>
                                <span class="text-amber-500 text-xs font-bold tracking-wide">
                                    {{ '★'.repeat(r.rating) }}<span class="text-gray-200">{{ '★'.repeat(5 - r.rating) }}</span>
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                    :class="r.is_approved ? 'bg-emerald-50 text-emerald-700' : 'bg-orange-50 text-orange-600'">
                                    {{ r.is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1">
                                For Dr. {{ r.doctor?.name ?? 'Unknown' }} &middot; {{ formatDate(r.created_at) }}
                            </p>
                            <p class="text-xs text-gray-700 mt-3 whitespace-pre-line break-words">
                                {{ r.comment || 'No comment written.' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button v-if="!r.is_approved" type="button" @click="approve(r.id)"
                                class="px-3.5 py-2 rounded-lg bg-orange-500 text-white text-[11px] font-bold hover:bg-orange-600 transition">
                                Approve
                            </button>
                            <button v-else type="button" @click="hide(r.id)"
                                class="px-3.5 py-2 rounded-lg bg-gray-100 text-gray-700 text-[11px] font-bold hover:bg-gray-200 transition">
                                Hide
                            </button>
                            <button type="button" @click="remove(r.id)"
                                class="px-3.5 py-2 rounded-lg bg-rose-50 text-rose-600 text-[11px] font-bold hover:bg-rose-100 transition">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="reviews.links && reviews.links.length > 3"
                class="py-3 px-4 bg-white border border-gray-100 rounded-2xl flex justify-center shadow-sm">
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, index) in reviews.links" :key="index">
                        <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                            class="px-2.5 py-1 text-[11px] font-bold rounded-lg border transition" :class="[
                                link.active
                                    ? 'bg-orange-500 text-white border-orange-500 shadow-sm'
                                    : link.url
                                        ? 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'
                                        : 'opacity-50 cursor-not-allowed bg-white text-gray-300 border-gray-200'
                            ]" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
