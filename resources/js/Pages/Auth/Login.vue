<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    login: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
        <Head title="Login" />
    <div class="flex h-screen">
        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
            <div>
                <h2 class="mt-6 text-3xl font-bold text-primary-blue text-center text-center lg:text-left">Brgy. Concolasion</h2>
                <h2 class="text-3xl font-bold tracking-tight text-secondary-blue text-center lg:text-left">Water Billing System</h2>
                <h2 class="mt-6 text-xl font-bold tracking-tight text-gray-800 text-center lg:text-left">Sign in to your account</h2>
            </div>

            <div class="mt-8">
                <div class="mt-6">
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="login" value="Username" />
                            <TextInput id="login" type="text" class="mt-1 block w-full" v-model="form.login" required autofocus autocomplete="username" />
                            <InputError class="mt-2" :message="form.errors.login" />
                        </div>

                        <div class="space-y-1 mt-2">
                            <InputLabel for="password" value="Password" />
                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="pt-4">
                            <div>
                                <button type="submit" :disabled="form.processing" :class="{ 'opacity-25': form.processing }" class="font-bold text-lg flex w-full justify-center rounded-md border border-transparent bg-primary-blue py-2 px-4 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Sign in</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 h-full w-full object-cover" :src="$page.props.cover_url" alt="">
        </div>
    </div>
</template>
