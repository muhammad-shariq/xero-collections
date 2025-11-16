<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import QuillEditor from '@/Components/QuillEditor.vue';
import NavLink from '@/Components/NavLink.vue';
import { ref } from 'vue';

const content = ref('');

import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
    data: Array;
    error: Boolean;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    contact_id: props.data.ref_contact_id,
    contact_name: props.data.name,
    contact_email: props.data.email,
    mobile_number: props.data.mobile_number,
    id: props.data.id
});
</script>

<template>
    <section>
        <!-- <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Template</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Edit your Email Data with template sent.
            </p>
        </header> -->
        <!-- {{ form }} -->
        <div v-if="form.wasSuccessful && !props.error" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Success!</span> Data updated successfully.
        </div>

        <div v-if="props.error" class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>

            <div>
                <span class="font-medium">Alert!</span> Error in Xero Contact Updated.
            </div>
        </div>

        <form @submit.prevent="form.patch(route('contact.update'))" class="mt-6 space-y-6">


            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.contact_name"
                    required
                />

                <InputError class="mt-2" :message="form.errors.contact_name" />
            </div>



            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.contact_email"
                    required
                />

                <InputError class="mt-2" :message="form.errors.contact_email" />
            </div>

            <div>
                <InputLabel for="mobile_number" value="Mobile Number" />

                <TextInput
                    id="mobile_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.mobile_number"
                    required
                />

                <InputError class="mt-2" :message="form.errors.mobile_number" />
            </div>




            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Link
                    :href="route('contact')"
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
