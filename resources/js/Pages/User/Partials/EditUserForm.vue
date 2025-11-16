<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const content = ref('');

import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
    roles: Array;
    user_data: Array;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    id: props.user_data.id,
    email: props.user_data.email,
    name: props.user_data.name,
    role_id: props.user_data.role_id,
    password: '',
    xero_tenant_id: props.user_data.xero_tenant_id,
    xero_access_token: props.user_data.xero_access_token,
    xero_refresh_token: props.user_data.xero_refresh_token,
    xero_token_expiry: props.user_data.xero_token_expiry,
});

function submit(){
    form.patch(route('users.store'));
}

</script>

<template>
    <section>

        <!-- {{ user_data }} -->
        <div v-if="form.wasSuccessful" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Success!</span> Data updated successfully.
        </div>

        <form @submit.prevent="submit" class="mt-6 space-y-6">


            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"

                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="name"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"

                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>


            <div>
                <InputLabel for="role_id" value="Role" />

                <select class="mt-1 block w-full"  v-model="form.role_id">
                    <option value="">Select Role</option>
                    <option
                        v-for="role in roles"
                        :key="role.id"
                        :value="role.id"
                        >
                        {{ role.role_name }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.role_id" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"

                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- <div>
                <InputLabel for="xero_access_token" value="Xero Access Token" />

                <TextInput
                    id="xero_access_token"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.xero_access_token"

                />

                <InputError class="mt-2" :message="form.errors.xero_access_token" />
            </div>

            <div>
                <InputLabel for="xero_refresh_token" value="Xero Refresh Token" />

                <TextInput
                    id="xero_refresh_token"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.xero_refresh_token"

                />

                <InputError class="mt-2" :message="form.errors.xero_refresh_token" />
            </div>

            <div>
                <InputLabel for="xero_token_expiry" value="Xero Token Expiry" />

                <TextInput
                    id="xero_token_expiry"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.xero_token_expiry"

                />

                <InputError class="mt-2" :message="form.errors.xero_token_expiry" />
            </div>

            <div>
                <InputLabel for="xero_tenant_id" value="Xero Tenant ID" />

                <TextInput
                    id="xero_tenant_id"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.xero_tenant_id"

                />

                <InputError class="mt-2" :message="form.errors.xero_tenant_id" />
            </div> -->

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Link
                    :href="route('users')"
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
