<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    department: Object,
});

const form = useForm({
    name: props.department.name,
    description: props.department.description || '',
    status: Boolean(props.department.status),
});

const submit = () => {
    form.put(route('departments.update', props.department.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-4 sm:py-8">
            <div class="max-w-2xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight">Edit Department</h2>
                        <p class="text-[11px] sm:text-xs text-gray-500 font-medium">Update department information.</p>
                    </div>
                    <Link :href="route('departments.index')"
                        class="w-full sm:w-auto text-center text-xs font-bold text-gray-500 hover:text-gray-900 transition bg-white sm:bg-gray-50 px-3.5 py-2.5 sm:py-2 rounded-xl border border-gray-200 shadow-2xs shrink-0">
                        &larr; Back
                    </Link>
                </div>

                <!-- Form Container -->
                <form @submit.prevent="submit"
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 sm:p-6 space-y-4">
                    <div>
                        <label
                            class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Department
                            Name</label>
                        <input v-model="form.name" type="text" placeholder="e.g. Cardiology"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition" />
                        <div v-if="form.errors.name" class="text-rose-500 text-[10px] mt-1 font-semibold">{{
                            form.errors.name }}</div>
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Description</label>
                        <textarea v-model="form.description" rows="4" placeholder="Enter department description"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/25 transition"></textarea>
                        <div v-if="form.errors.description" class="text-rose-500 text-[10px] mt-1 font-semibold">{{
                            form.errors.description }}</div>
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer w-fit">
                        <input v-model="form.status" type="checkbox"
                            class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500/40" />
                        <span class="text-xs font-bold text-gray-700">Active Department</span>
                    </label>

                    <div
                        class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <Link :href="route('departments.index')"
                            class="w-full sm:w-auto text-center text-xs font-bold text-gray-500 hover:text-gray-900 transition px-4 py-2.5 rounded-xl border border-transparent hover:border-gray-200">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="w-full sm:w-auto inline-flex items-center justify-center bg-orange-500 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:bg-orange-600 transition disabled:opacity-50">
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update Department</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>