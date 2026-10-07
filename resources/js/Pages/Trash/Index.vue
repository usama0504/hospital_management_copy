<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    type: String,
    items: Object,
    counts: Object,
});

const icons = {
    patients: 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
    appointments: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
    bills: 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
    prescriptions: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
};

const tabs = [
    { key: 'patients', label: 'Patients' },
    { key: 'appointments', label: 'Appointments' },
    { key: 'bills', label: 'Bills' },
    { key: 'prescriptions', label: 'Prescriptions' },
];

const current = computed(() => tabs.find(t => t.key === props.type) ?? tabs[0]);
const totalDeleted = computed(() => Object.values(props.counts ?? {}).reduce((a, b) => a + b, 0));

const setType = (key) => router.get(route('trash.index'), { type: key }, { preserveState: true, replace: true });

const restore = (item) => {
    if (confirm(`Restore "${item.label}"?`)) {
        router.patch(route('trash.restore', { type: props.type, id: item.id }), {}, { preserveScroll: true });
    }
};

const tones = [
    'bg-orange-100 text-orange-700',
    'bg-sky-100 text-sky-700',
    'bg-violet-100 text-violet-700',
    'bg-emerald-100 text-emerald-700',
    'bg-rose-100 text-rose-700',
];
const tone = (text = '') => tones[[...text].reduce((a, c) => a + c.charCodeAt(0), 0) % tones.length];
const initials = (text = '') => text.split(' ').filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase() || '?';

const pills = (sub) => (sub ? sub.split(' · ').filter(Boolean) : []);

const timeAgo = (d) => {
    const mins = Math.round((Date.now() - new Date(d).getTime()) / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins} min ago`;
    const hrs = Math.round(mins / 60);
    if (hrs < 24) return `${hrs} hr ago`;
    const days = Math.round(hrs / 24);
    if (days < 30) return `${days} day${days === 1 ? '' : 's'} ago`;
    return new Date(d).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};
const fullDate = (d) => new Date(d).toLocaleString();
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-5xl mx-auto px-3 sm:px-0 pb-12">

            <!-- Header -->
            <div class="flex items-start gap-3 sm:gap-4 pt-2">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base sm:text-2xl font-black text-gray-900 tracking-tight">Deleted records</h2>
                    <p class="text-[11px] sm:text-sm text-gray-500 mt-0.5 sm:mt-1">
                        <template v-if="totalDeleted">{{ totalDeleted }} deleted in total. Nothing here is lost; restore a record and it returns exactly as it was.</template>
                        <template v-else>Nothing has been deleted. Anything you delete will wait here until you restore it.</template>
                    </p>
                </div>
            </div>

            <div v-if="$page.props.flash?.success"
                class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                {{ $page.props.flash.success }}
            </div>

            <!-- Section switcher -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <button v-for="t in tabs" :key="t.key" type="button" @click="setType(t.key)"
                    class="text-left rounded-2xl border p-3.5 sm:p-4 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                    :class="type === t.key
                        ? 'bg-orange-50 border-orange-300 shadow-sm'
                        : 'bg-white border-gray-100 hover:border-gray-200 hover:bg-gray-50/60'">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center"
                            :class="type === t.key ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-500'">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="icons[t.key]" />
                            </svg>
                        </span>
                        <span class="text-xl sm:text-2xl font-black tabular-nums" :class="type === t.key ? 'text-orange-600' : 'text-gray-900'">
                            {{ counts[t.key] ?? 0 }}
                        </span>
                    </div>
                    <p class="mt-2.5 sm:mt-3 text-xs font-bold" :class="type === t.key ? 'text-orange-700' : 'text-gray-600'">{{ t.label }}</p>
                </button>
            </div>

            <!-- List -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <p class="text-xs font-bold text-gray-700">Deleted {{ current.label.toLowerCase() }}</p>
                    <p class="text-[11px] text-gray-400">Newest first</p>
                </div>

                <div v-if="!items.data.length" class="py-16 px-6 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="icons[type]" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-bold text-gray-800">No deleted {{ current.label.toLowerCase() }}</p>
                    <p class="mt-1 text-xs text-gray-500">When one is deleted, it will show up here so you can restore it.</p>
                </div>

                <ul v-else class="divide-y divide-gray-50">
                    <li v-for="item in items.data" :key="item.id"
                        class="px-4 sm:px-5 py-3.5 sm:py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50 transition">
                        
                        <!-- Item Details -->
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-xs font-black shrink-0" :class="tone(item.label)">
                                {{ initials(item.label) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ item.label }}</p>
                                <div v-if="pills(item.sub).length" class="mt-1.5 flex flex-wrap gap-1">
                                    <span v-for="(p, i) in pills(item.sub)" :key="i"
                                        class="px-2 py-0.5 rounded-md bg-gray-100 text-[10px] sm:text-[11px] text-gray-600 font-medium">{{ p }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamp & Action Button -->
                        <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4 shrink-0 pt-1 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                            <span class="text-[10px] sm:text-[11px] text-gray-400" :title="fullDate(item.deleted_at)">Deleted {{ timeAgo(item.deleted_at) }}</span>
                            <button type="button" @click="restore(item)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-white border border-emerald-200 text-emerald-700 text-xs font-bold hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                                Restore
                            </button>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Pagination -->
            <div v-if="items.links && items.links.length > 3"
                class="py-3 px-3 sm:px-4 bg-white border border-gray-100 rounded-2xl flex justify-center shadow-sm overflow-x-auto">
                <div class="flex flex-wrap gap-1 justify-center">
                    <template v-for="(link, index) in items.links" :key="index">
                        <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                            class="px-2.5 py-1 text-[11px] font-bold rounded-lg border transition shrink-0" :class="[
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