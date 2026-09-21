<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
    departments: Array
})

const deleteDepartment = (id) => {
    if (confirm('Are you sure you want to delete this department?')) {
        router.delete(`/departments/${id}`)
    }
}
</script>

<template>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Departments</h2>
                <p class="text-muted mb-0">Manage hospital departments and specialties.</p>
            </div>

            <Link href="/departments/create" class="btn btn-primary">
                + Add Department
            </Link>
        </div>

        <div v-if="$page.props.flash?.success" class="alert alert-success">
            {{ $page.props.flash.success }}
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(department, index) in departments" :key="department.id">
                                <td class="px-4">{{ index + 1 }}</td>

                                <td>
                                    <strong>{{ department.name }}</strong>
                                </td>

                                <td>
                                    {{ department.description || 'No description' }}
                                </td>

                                <td>
                                    <span
                                        class="badge"
                                        :class="department.status ? 'bg-success' : 'bg-secondary'"
                                    >
                                        {{ department.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="text-end px-4">
                                    <Link
                                        :href="`/departments/${department.id}/edit`"
                                        class="btn btn-sm btn-outline-primary me-2"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        @click="deleteDepartment(department.id)"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="departments.length === 0">
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No departments found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
