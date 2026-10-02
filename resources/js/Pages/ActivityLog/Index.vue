<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({ logs: Object, filters: Object, subjects: Array });
const subject = ref(props.filters?.subject ?? '');
const event = ref(props.filters?.event ?? '');

const apply = () => router.get(route('activity-log.index'), {
    subject: subject.value || undefined,
    event: event.value || undefined,
}, { preserveState: true, replace: true });

const events = ['', 'created', 'updated', 'deleted', 'restored'];
const eventLabels = { '': 'All actions', created: 'Added', updated: 'Edited', deleted: 'Deleted', restored: 'Restored' };

const meta = {
    created: { verb: 'added', badge: 'bg-emerald-100 text-emerald-600', word: 'text-emerald-600', icon: 'M12 4.5v15m7.5-7.5h-15' },
    updated: { verb: 'edited', badge: 'bg-sky-100 text-sky-600', word: 'text-sky-600', icon: 'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125' },
    deleted: { verb: 'deleted', badge: 'bg-rose-100 text-rose-600', word: 'text-rose-600', icon: 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0' },
    restored: { verb: 'restored', badge: 'bg-amber-100 text-amber-600', word: 'text-amber-600', icon: 'M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3' },
};
const metaOf = (e) => meta[e] ?? { verb: e, badge: 'bg-gray-100 text-gray-500', word: 'text-gray-600', icon: 'M12 6v6h4.5' };

const show = (v) => {
    if (v == null || v === '') return 'empty';
    if (typeof v === 'string' && /^\d{4}-\d{2}-\d{2}T/.test(v)) {
        const d = new Date(v);
        return isNaN(d) ? v : d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }
    return typeof v === 'object' ? JSON.stringify(v, null, 2) : String(v);
};

const changeRows = (changes) => Object.entries(changes?.attributes ?? {}).map(([k, after]) => ({
    field: k.replace(/_/g, ' '),
    before: changes?.old?.[k],
    after,
}));

const groups = computed(() => {
    const out = [];
    for (const log of props.logs?.data ?? []) {
        const d = new Date(log.created_at);
        const label = d.toDateString() === new Date().toDateString() ? 'Today' : d.toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        const last = out[out.length - 1];
        if (last && last.label === label) last.items.push(log);
        else out.push({ label, items: [log] });
    }
    return out;
});

const tones = ['bg-orange-100 text-orange-700', 'bg-sky-100 text-sky-700', 'bg-violet-100 text-violet-700', 'bg-emerald-100 text-emerald-700', 'bg-rose-100 text-rose-700'];
const tone = (t = '') => tones[[...t].reduce((a, c) => a + c.charCodeAt(0), 0) % tones.length];
const initials = (t = '') => t.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || '?';
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-4xl mx-auto px-3 sm:px-0 pb-12">

            <!-- Header -->
            <div class="flex items-start gap-3 sm:gap-4 pt-2">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight">Activity log</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Track patient, appointment, bill and prescription updates.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Record Type</label>
                    <select v-model="subject" @change="apply" class="w-full text-xs font-semibold rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5">
                        <option value="">All Records</option>
                        <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Action Type</label>
                    <select v-model="event" @change="apply" class="w-full text-xs font-semibold rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5">
                        <option v-for="e in events" :key="e" :value="e">{{ eventLabels[e] }}</option>
                    </select>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!logs?.data?.length" class="bg-white border border-gray-100 shadow-sm rounded-2xl py-16 px-6 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <p class="mt-4 text-sm font-bold text-gray-800">No activity found</p>
                <p class="mt-1 text-xs text-gray-500">Try changing your filters.</p>
            </div>

            <!-- Timeline -->
            <div v-else class="space-y-6">
                <section v-for="group in groups" :key="group.label">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3 px-1">{{ group.label }}</h3>
                    <ol class="relative ml-3 sm:ml-5 border-l-2 border-gray-100 space-y-4">
                        <li v-for="log in group.items" :key="log.id" class="relative pl-6 sm:pl-7">
                            <span class="absolute -left-[17px] top-3.5 w-8 h-8 rounded-full border-4 border-gray-50 flex items-center justify-center shrink-0 shadow-sm" :class="metaOf(log.event).badge">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="metaOf(log.event).icon" />
                                </svg>
                            </span>
                            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-3.5 sm:p-4">
                                <!-- User & Action Info -->
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-black shrink-0" :class="tone(log.user)">{{ initials(log.user) }}</span>
                                    <p class="text-xs sm:text-sm text-gray-700 leading-snug">
                                        <span class="font-bold text-gray-900">{{ log.user }}</span>
                                        <span class="font-bold" :class="metaOf(log.event).word"> {{ metaOf(log.event).verb }} </span>
                                        <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded-md bg-gray-100 text-[11px] font-bold text-gray-700">{{ log.subject }} #{{ log.subject_id }}</span>
                                    </p>
                                </div>

                                <!-- Changes List (Without individual inner boxes/cards) -->
                                <div v-if="changeRows(log.changes).length" class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                                    <div v-for="(r, i) in changeRows(log.changes)" :key="i" class="text-xs flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-gray-400 uppercase text-[10px] w-20 shrink-0">{{ r.field }}:</span>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <template v-if="r.before !== undefined">
                                                <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 line-through decoration-rose-300 break-all text-[11px]">{{ show(r.before) }}</span>
                                                <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold break-all text-[11px]">{{ show(r.after) }}</span>
                                            </template>
                                            <span v-else class="font-medium text-gray-800 break-all text-[11px]">{{ show(r.after) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ol>
                </section>
            </div>

            <!-- Pagination -->
            <div v-if="logs?.links && logs.links.length > 3" class="py-3 px-3 bg-white border border-gray-100 rounded-2xl flex justify-center shadow-sm overflow-x-auto">
                <div class="flex flex-wrap gap-1 justify-center">
                    <template v-for="(link, index) in logs.links" :key="index">
                        <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                            class="px-2.5 py-1 text-[11px] font-bold rounded-lg border transition shrink-0" :class="[
                                link.active ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : link.url ? 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100' : 'opacity-50 cursor-not-allowed bg-white text-gray-300 border-gray-200'
                            ]" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>