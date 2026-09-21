<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
    department: Object
})

const form = useForm({
    name: props.department.name,
    description: props.department.description || '',
    status: Boolean(props.department.status)
})

const submit = () => {
    form.put(`/departments/${props.department.id}`)
}
</script>

<template>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Edit Department</h2>
                <p class="text-muted mb-0">Update department information.</p>
            </div>

            <Link href="/departments" class="btn btn-light border">
                ← Back
            </Link>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form @submit.prevent="submit">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Department Name
                        </label>

                        <input v-model="form.name" type="text" class="form-control" placeholder="e.g. Cardiology">

                        <div v-if="form.errors.name" class="text-danger small mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Description
                        </label>

                        <textarea v-model="form.description" class="form-control" rows="4"
                            placeholder="Enter department description"></textarea>

                        <div v-if="form.errors.description" class="text-danger small mt-1">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input v-model="form.status" type="checkbox" class="form-check-input" id="status">

                        <label class="form-check-label" for="status">
                            Active Department
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Department' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
