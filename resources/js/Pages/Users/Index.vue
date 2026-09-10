<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Controller se ane walay props
defineProps({
    users: Object, // Paginated users collection
});

// Flash messages from Inertia page props
const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);
const authUser = computed(() => page.props.auth?.user);

// Approve user action
const approveUser = (userId) => {
    router.post(route('users.approve', userId), {}, {
        preserveScroll: true,
    });
};

// Delete user action
const deleteUser = (userId) => {
    if (confirm('Remove this user permanently?')) {
        router.delete(route('users.destroy', userId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="mb-6">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Manage Users</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Approve new staff registrations and manage existing accounts.</p>
            </div>

            <!-- Session Alerts -->
            <div v-if="flashSuccess" class="mb-4 flex items-center bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-2xl text-xs font-semibold shadow-sm">
                {{ flashSuccess }}
            </div>
            <div v-if="flashError" class="mb-4 flex items-center bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs font-semibold shadow-sm">
                {{ flashError }}
            </div>

            <!-- Users Table Card -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-5">Name</th>
                                <th class="py-3.5 px-5">Email</th>
                                <th class="py-3.5 px-5">Role</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5">Registered</th>
                                <th class="py-3.5 px-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                            <template v-if="users.data && users.data.length > 0">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-5 font-bold text-gray-900">{{ user.name }}</td>
                                    <td class="py-4 px-5 text-gray-600">{{ user.email }}</td>
                                    <td class="py-4 px-5 capitalize text-gray-600">
                                        <span class="bg-gray-100 px-2.5 py-1 rounded-lg text-[11px] font-semibold">
                                            {{ user.roles?.map(r => r.name).join(', ') || '—' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5">
                                        <span v-if="user.is_approved" class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            Approved
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-gray-500">
                                        <!-- Date formatting -->
                                        {{ new Date(user.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button v-if="!user.is_approved" @click="approveUser(user.id)" type="button" class="inline-flex items-center bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1.5 rounded-xl font-bold text-[11px] hover:bg-emerald-100 transition">
                                                Approve
                                            </button>
                                            <button v-if="authUser && user.id !== authUser.id" @click="deleteUser(user.id)" type="button" class="inline-flex items-center bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1.5 rounded-xl font-bold text-[11px] hover:bg-rose-100 transition">
                                                Remove
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="6" class="text-center py-10 text-gray-400 font-medium">No users found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Links (Agar Laravel paginator use ho raha hai) -->
            <div v-if="users.links" class="mt-4 flex items-center justify-center gap-1">
                <!-- Simple component or v-html if rendering raw pagination links -->
                <div v-html="users.links" class="pagination-wrapper flex gap-1"></div>
            </div>

        </div>
    </div>
</template>