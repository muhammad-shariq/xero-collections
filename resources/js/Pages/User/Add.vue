<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AddUserForm from './Partials/AddUserForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    options?: Array;
    roles: Array;
}>();
</script>

<template>
    <Head title="Add User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight font-bold">Add User</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <AddUserForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :options="options"
                        class="max-w-xl"
                        :roles="roles"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
