<script setup>
import { router, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    users: Object,
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);
const authUser = computed(() => page.props.auth?.user);

const approveUser = (userId) => {
    router.post(route('users.approve', userId), {}, {
        preserveScroll: true,
    });
};

const deleteUser = (userId) => {
    if (confirm('Remove this user permanently?')) {
        router.delete(route('users.destroy', userId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 px-3 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Manage Users</h1>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Approve new staff registrations and manage
                            existing accounts.</p>
                    </div>
                </div>

                <!-- Flash Messages -->
                <div v-if="flashSuccess"
                    class="flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                    {{ flashSuccess }}
                </div>
                <div v-if="flashError"
                    class="flex items-center bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl text-xs font-semibold shadow-sm">
                    {{ flashError }}
                </div>

                <!-- ================= MOBILE CARDS VIEW ================= -->
                <div class="block sm:hidden space-y-3">
                    <template v-if="users.data && users.data.length > 0">
                        <div v-for="user in users.data" :key="user.id"
                            class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 transition space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-900">{{ user.name }}</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                    :class="user.is_approved ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                    {{ user.is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </div>

                            <div class="text-[11px] text-gray-500 space-y-1">
                                <div class="truncate"><span class="font-semibold text-gray-700">Email:</span> {{
                                    user.email }}</div>
                                <div class="flex items-center justify-between pt-1">
                                    <div>
                                        <span class="font-semibold text-gray-700">Role:</span>
                                        <span
                                            class="bg-gray-100 px-2 py-0.5 rounded text-[10px] font-semibold text-gray-600 ml-1">
                                            {{user.roles?.map(r => r.name).join(', ') || '—'}}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">
                                        {{ new Date(user.created_at).toLocaleDateString('en-GB', {
                                            day: '2-digit',
                                            month: 'short', year: 'numeric'
                                        }) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-2 pt-2.5 border-t border-gray-50">
                                <button v-if="!user.is_approved" @click="approveUser(user.id)" type="button"
                                    class="inline-flex items-center bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-lg font-bold text-[11px] hover:bg-emerald-100 transition">
                                    Approve
                                </button>
                                <button v-if="authUser && user.id !== authUser.id" @click="deleteUser(user.id)"
                                    type="button"
                                    class="inline-flex items-center bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1 rounded-lg font-bold text-[11px] hover:bg-rose-100 transition">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>
                    <div v-else
                        class="bg-white border border-gray-100 rounded-2xl p-8 text-center text-gray-400 text-xs font-medium">
                        No users found.
                    </div>
                </div>

                <!-- ================= DESKTOP TABLE VIEW ================= -->
                <div
                    class="hidden sm:block bg-white border border-gray-100 shadow-xl shadow-gray-100/80 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="py-3 px-4">Name</th>
                                    <th class="py-3 px-4">Email</th>
                                    <th class="py-3 px-4">Role</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Registered</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                                <template v-if="users.data && users.data.length > 0">
                                    <tr v-for="user in users.data" :key="user.id"
                                        class="hover:bg-gray-50/50 transition">
                                        <td class="py-3 px-4 font-bold text-gray-900">{{ user.name }}</td>
                                        <td class="py-3 px-4 text-gray-600">{{ user.email }}</td>
                                        <td class="py-3 px-4 capitalize text-gray-600">
                                            <span class="bg-gray-100 px-2.5 py-1 rounded-lg text-[11px] font-semibold">
                                                {{user.roles?.map(r => r.name).join(', ') || '—'}}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span v-if="user.is_approved"
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Approved
                                            </span>
                                            <span v-else
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Pending
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-500 text-[11px]">
                                            {{ new Date(user.created_at).toLocaleDateString('en-GB', {
                                                day: '2-digit',
                                                month: 'short', year: 'numeric'
                                            }) }}
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap text-right">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button v-if="!user.is_approved" @click="approveUser(user.id)"
                                                    type="button"
                                                    class="inline-flex items-center bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-lg font-bold text-[11px] hover:bg-emerald-100 transition shadow-2xs">
                                                    Approve
                                                </button>
                                                <button v-if="authUser && user.id !== authUser.id"
                                                    @click="deleteUser(user.id)" type="button"
                                                    class="inline-flex items-center bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1 rounded-lg font-bold text-[11px] hover:bg-rose-100 transition shadow-2xs">
                                                    Remove
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-else>
                                    <td colspan="6" class="text-center py-10 text-gray-400 font-medium text-xs">No users
                                        found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div v-if="users.links && users.links.length > 3"
                    class="mt-4 py-3 px-4 bg-white sm:bg-gray-50/50 border border-gray-100 rounded-2xl flex justify-center shadow-sm">
                    <div class="flex flex-wrap gap-1">
                        <template v-for="(link, index) in users.links" :key="index">
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
        </div>
    </AuthenticatedLayout>
</template>