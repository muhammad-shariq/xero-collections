<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
// import QuillEditor from '@/Components/QuillEditor.vue';
import { ref } from 'vue';

const content = ref('');

import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
    templates?: Array;
    organizations?: Array;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    template: '',
    days: '',
    user_id: ''
});

</script>

<template>
    <section>
        <!-- {{ form }} -->
        <!-- <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add Template</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add your template and set the days for sending an email.
            </p>
        </header> -->

        <div v-if="form.wasSuccessful" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Success!</span> Data saved successfully.
        </div>

        <form @submit.prevent="form.post(route('template.store'),{ onSuccess: () => form.reset()})" class="mt-6 space-y-6">
            <div>

                <!-- <QuillEditor v-model="form.content" /> -->

                <InputLabel for="Template" value="Template" />

                <select class="mt-1 block w-full"  v-model="form.template">
                    <option value="">Select Template</option>
                    <option
                        v-for="template in templates"
                        :key="template.id"
                        :value="template.id"
                        >
                        {{ template.name }}
                        </option>
                </select>

                <InputError class="mt-2" :message="form.errors.template" />
            </div>

            <div>
                <InputLabel for="Organization" value="Organization" />

                <select class="mt-1 block w-full"  v-model="form.user_id">
                    <option value="">Select Organization</option>
                    <option
                        v-for="organization in organizations"
                        :key="organization.id"
                        :value="organization.id"
                        >
                        {{ organization.xero_organization_name }}
                        </option>
                </select>

                <InputError class="mt-2" :message="form.errors.user_id" />
            </div>

            <div>
                <InputLabel for="days" value="Trigger Days" />

                <TextInput
                    id="days"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.days"

                    autocomplete="0"
                />

                <InputError class="mt-2" :message="form.errors.days" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Link
                    :href="route('email.template')"
                    method="get"
                    as="button"
                >
                    <button type="button" class="px-3 py-2 text-sm font-medium text-center text-gray text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                </Link>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
