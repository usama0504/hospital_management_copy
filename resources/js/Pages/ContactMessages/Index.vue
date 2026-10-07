<script setup>
import { ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    messages: Object,
    filters: Object,
    unreadCount: Number,
});

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? 'all');
const openId = ref(null);

// Reply form (email app ke andar se bhejna)
const replyingId = ref(null);
const replyForm = useForm({ reply: '' });

const startReply = (m) => {
    replyingId.value = m.id;
    replyForm.reset();
    replyForm.clearErrors();
};
const cancelReply = () => {
    replyingId.value = null;
    replyForm.reset();
};
const sendReply = (m) => {
    replyForm.post(route('contact-messages.reply', m.id), {
        preserveScroll: true,
        onSuccess: () => cancelReply(),
    });
};

const applyFilters = () => {
    router.get(route('contact-messages.index'), {
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

// Message kholne par automatically "read" mark ho jata hai.
const toggle = (m) => {
    if (openId.value === m.id) {
        openId.value = null;
        return;
    }
    openId.value = m.id;
    if (!m.read_at) {
        router.patch(route('contact-messages.read', m.id), {}, { preserveScroll: true, preserveState: true });
    }
};

const markUnread = (id) => router.patch(route('contact-messages.unread', id), {}, { preserveScroll: true, preserveState: true });
const remove = (id) => {
    if (confirm('Delete this message permanently?')) {
        router.delete(route('contact-messages.destroy', id), { preserveScroll: true });
    }
};

const formatDate = (d) => new Date(d).toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

const tabs = [
    { key: 'all', label: 'All' },
    { key: 'unread', label: 'Unread' },
    { key: 'read', label: 'Read' },
];
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight">Contact Messages</h2>
                    <p class="text-[11px] sm:text-xs text-gray-500 mt-1 font-medium">
                        Messages sent from the public website contact form.
                        <span v-if="unreadCount" class="text-orange-600 font-bold">{{ unreadCount }} unread.</span>
                    </p>
                </div>
                <input v-model="search" type="text" placeholder="Search name, email or message..."
                    class="w-full sm:w-72 text-xs rounded-xl border border-gray-200 px-3.5 py-2.5 focus:border-orange-400 focus:ring-0" />
            </div>

            <div v-if="$page.props.flash?.success"
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ $page.props.flash.success }}
            </div>

            <div v-if="$page.props.flash?.error"
                class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ $page.props.flash.error }}
            </div>

            <div class="flex gap-2">
                <button v-for="t in tabs" :key="t.key" type="button" @click="setStatus(t.key)"
                    class="px-4 py-2 rounded-xl text-xs font-bold border transition"
                    :class="status === t.key ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'">
                    {{ t.label }}
                </button>
            </div>

            <div v-if="!messages.data.length"
                class="bg-white border border-gray-100 shadow-sm rounded-2xl py-16 text-center">
                <p class="text-xs font-semibold text-gray-500">No messages found.</p>
            </div>

            <div v-else class="space-y-3">
                <div v-for="m in messages.data" :key="m.id"
                    class="bg-white border shadow-sm rounded-2xl overflow-hidden"
                    :class="m.read_at ? 'border-gray-100' : 'border-orange-200'">

                    <button type="button" @click="toggle(m)" class="w-full text-left p-4 sm:p-5 flex items-start gap-3">
                        <span class="mt-1.5 w-2 h-2 rounded-full shrink-0" :class="m.read_at ? 'bg-transparent' : 'bg-orange-500'"></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm truncate" :class="m.read_at ? 'font-bold text-gray-800' : 'font-black text-gray-900'">
                                    {{ m.name }}
                                    <span class="text-[11px] font-medium text-gray-500">&lt;{{ m.email }}&gt;</span>
                                </p>
                                <span class="text-[10px] text-gray-400 font-medium shrink-0">{{ formatDate(m.created_at) }}</span>
                            </div>
                            <p class="text-xs font-semibold text-gray-700 mt-0.5 truncate">{{ m.subject || '(No subject)' }}</p>
                            <p v-if="openId !== m.id" class="text-[11px] text-gray-500 mt-1 truncate">{{ m.message }}</p>
                        </div>
                    </button>

                    <div v-if="openId === m.id" class="px-4 sm:px-5 pb-4 sm:pb-5 pl-9 sm:pl-10 space-y-3">
                        <p class="text-xs text-gray-700 whitespace-pre-line break-words">{{ m.message }}</p>
                        <p v-if="m.phone" class="text-[11px] text-gray-500">Phone: <span class="font-semibold text-gray-700">{{ m.phone }}</span></p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <button type="button" @click="startReply(m)"
                                class="px-3.5 py-2 rounded-lg bg-orange-500 text-white text-[11px] font-bold hover:bg-orange-600 transition">
                                {{ m.replied_at ? 'Reply again' : 'Reply by email' }}
                            </button>
                            <button type="button" @click="markUnread(m.id)"
                                class="px-3.5 py-2 rounded-lg bg-gray-100 text-gray-700 text-[11px] font-bold hover:bg-gray-200 transition">
                                Mark as unread
                            </button>
                            <button type="button" @click="remove(m.id)"
                                class="px-3.5 py-2 rounded-lg bg-rose-50 text-rose-600 text-[11px] font-bold hover:bg-rose-100 transition">
                                Delete
                            </button>
                        </div>

                        <p v-if="m.replied_at" class="text-[11px] text-emerald-600 font-semibold">
                            Replied on {{ formatDate(m.replied_at) }}
                        </p>

                        <form v-if="replyingId === m.id" @submit.prevent="sendReply(m)" class="space-y-2 pt-1">
                            <p class="text-[11px] text-gray-500">To: <span class="font-semibold text-gray-700">{{ m.email }}</span></p>
                            <textarea v-model="replyForm.reply" rows="5" placeholder="Write your reply..."
                                class="w-full text-xs rounded-xl border border-gray-200 px-3.5 py-2.5 focus:border-orange-400 focus:ring-0"></textarea>
                            <p v-if="replyForm.errors.reply" class="text-[11px] text-rose-600 font-semibold">{{ replyForm.errors.reply }}</p>
                            <div class="flex gap-2">
                                <button type="submit" :disabled="replyForm.processing"
                                    class="px-3.5 py-2 rounded-lg bg-orange-500 text-white text-[11px] font-bold hover:bg-orange-600 transition disabled:opacity-50">
                                    {{ replyForm.processing ? 'Sending...' : 'Send reply' }}
                                </button>
                                <button type="button" @click="cancelReply"
                                    class="px-3.5 py-2 rounded-lg bg-gray-100 text-gray-700 text-[11px] font-bold hover:bg-gray-200 transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div v-if="messages.links && messages.links.length > 3"
                class="py-3 px-4 bg-white border border-gray-100 rounded-2xl flex justify-center shadow-sm">
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, index) in messages.links" :key="index">
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
