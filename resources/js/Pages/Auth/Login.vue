<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import VTailwindModal from '@/Modals/VTailwindModal.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { inject, ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const swal = inject('$swal')
const showPass = ref(false)

const showResetPassword = ref(false)

const form = useForm({
    login: '',
    password: '',
    remember: false,
    username: '',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const submitChangePassword = () => {
    axios.post(`/user-change-password`, {
        'username': form.username
    })
    .then(response => {
        swal.fire({
            icon: 'success',
            title: 'Temporary Password sent Via SMS',
            text: `Change password as soon as possible`,
            confirmButtonColor: '#23408E'
        })
    })
    .catch(response => {
        swal.fire({
            icon: 'error',
            title: 'Failed to Change Password',
            text: response.response.data.message,
            confirmButtonColor: '#23408E'
        })
    })
}

const enterEmail = () => {
    showResetPassword.value = true
}

function cancel(close) {
    showResetPassword.value = false
}
</script>

<template>
    <Head title="Login" />
    <div class="flex h-screen">
        <v-tailwind-modal v-model="showResetPassword" @cancel="cancel()">
            <div class="flex justify-center">
                <div class="min-h-full bg-white w-full">
                    <div class="mx-auto max-w-max">
                        <div>
                            <div class="relative rounded-md shadow-sm mt-10">
                                <input v-model="form.username" type="text" name="amount" placeholder="Input Registered Username" class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="flex mt-2 space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                                <button @click="cancel()" href="#" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ml-auto">Close</button>
                                <a @click="submitChangePassword()" class="inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Change Password</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-tailwind-modal>
        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
            <div>
                <div class="flex items-center">
                    <div>
                        <img :src="$page.props.logo" alt="My Image" class="w-20 h-20 mr-2">
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-primary-blue text-center lg:text-left">Brgy. Consolacion</h2>
                        <h2 class="text-3xl font-bold tracking-tight text-secondary-blue text-center lg:text-left">Water Billing System</h2>
                    </div>
                </div>
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
                            <div class="flex items-center">
                                <TextInput id="password" :type="showPass ? 'text' : 'password'" class="mt-1 block w-full mr-2" v-model="form.password" required autocomplete="current-password" />
                                <svg v-if="!showPass" @click="showPass = true" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 cursor-pointer hover:text-blue-900"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                <svg v-else @click="showPass = false" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 cursor-pointer hover:text-blue-900"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="pt-4">
                            <div>
                                <button type="submit" :disabled="form.processing" :class="{ 'opacity-25': form.processing }" class="font-bold text-lg flex w-full justify-center rounded-md border border-transparent bg-primary-blue py-2 px-4 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Sign in</button>
                            </div>
                        </div>
                    </form>
                    <button @click="enterEmail()" class="ml-auto text-right text-primary-blue hover:opacity-75 mt-2 w-full">
                        Forgot Password?
                    </button>
                </div>
            </div>
            </div>
        </div>
        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 h-full w-full object-cover" :src="$page.props.cover_url" alt="">
        </div>
    </div>
</template>
