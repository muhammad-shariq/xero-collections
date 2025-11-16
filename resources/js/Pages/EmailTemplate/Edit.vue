<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateEmailTemplateForm from './Partials/UpdateEmailTemplateForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    templates?: Array;
    options?: Array;
    data: Array;
    organizations?: Array;
}>();
</script>

<template>
    <Head title="Edit Scheduled Template" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Scheduled Template</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <UpdateEmailTemplateForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :templates="templates"
                        :organizations="organizations"
                        :data="data"
                        :options="options"
                        class="max-w-xl"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
