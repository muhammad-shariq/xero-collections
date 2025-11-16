<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pb-20 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div style="margin: -80px;">
            <Link href="/">
                <ApplicationLogo class="w-80 h-80 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
