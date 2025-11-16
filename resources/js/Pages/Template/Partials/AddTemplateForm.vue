<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import QuillEditor from '@/Components/QuillEditor.vue';
import { ref } from 'vue';

const content = ref('');

import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
    templates?: Array;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    template: '',
    name: '',
    email_name_from:'',
    from_email: '',
    email_subject: '',
    document: '',
});

function submit(){
    form.post(route('temp.store'),{
        onSuccess: () => {
            form.reset();
            form.template = '<p></p>';
        }

    });
}

</script>

<template>
    <section>
        <!-- <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add Template</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add your Email Data with template sent.
            </p>
        </header> -->
        <!-- {{ form }} -->
        <div v-if="form.wasSuccessful" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Success!</span> Data saved successfully.
        </div>

        <form @submit.prevent="submit" class="mt-6 space-y-6">


            <div>
                <InputLabel for="name" value="Template Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"

                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="document" value="Template File" />
                <input type="file"
                    class="mt-1 block w-full"
                     @input="form.document = $event.target.files[0]"
                />

                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>
                <InputError class="mt-2" :message="form.errors.document" />
            </div>


            <div>
                <InputLabel for="email_name_from" value="From Email Name" />

                <TextInput
                    id="email_name_from"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.email_name_from"

                />

                <InputError class="mt-2" :message="form.errors.email_name_from" />
            </div>

            <div>
                <InputLabel for="from_email" value="Email From" />

                <TextInput
                    id="from_email"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.from_email"

                />

                <InputError class="mt-2" :message="form.errors.from_email" />
            </div>

            <div>
                <InputLabel for="email_subject" value="Email Subject" />

                <TextInput
                    id="email_subject"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.email_subject"

                />

                <InputError class="mt-2" :message="form.errors.email_subject" />
            </div>

            <div>
                <InputLabel for="template" value="Email Message Body" />

                <QuillEditor
                    v-model:content="form.template"
                    contentType="html"
                    id="template"
                    type="text"
                    class="mt-1 block w-full"

                 />

                <InputError class="mt-2" :message="form.errors.template" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Link
                    :href="route('template')"
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
