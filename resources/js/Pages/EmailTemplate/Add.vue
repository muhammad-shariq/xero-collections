<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AddEmailTemplateForm from './Partials/AddEmailTemplateForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    organizations: Array;
    templates?: Array;
    options?: Array;
}>();
</script>

<template>
    <Head title="Add Template" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Template</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <AddEmailTemplateForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :templates="templates"
                        :organizations="organizations"
                        :options="options"
                        class="max-w-xl"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
