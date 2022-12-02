<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { inject } from 'vue'

    const swal = inject('$swal')

    const form = useForm({
        password: '',
        confirmPassword: ''
    });

    const submit = () => {
        if(form.password != form.confirmPassword) {
            swal.fire({
                icon: 'error',
                title: 'Password not matched.'
            })
        } else {
            form.post(route('change.password'), {
                onSuccess: () => {
                    form.reset('password')
                    swal.fire({
                        icon: 'success',
                        title: 'Password Reset'
                    })
                },
                onError: () => {
                    swal.fire({
                        icon: 'error',
                        title: 'Check you input.'
                    })
                }
            });
        }
    };
</script>

<template>
    <Head title="Change Password"/>

    <AuthenticatedLayout>
        <form @submit.prevent="submit" class="flex justify-center">
            <div class="w-1/2 py-40">
                <InputLabel for="password" value="Password" />
                <TextInput type="password" class="mt-1 block w-full" v-model="form.password" required autofocus autocomplete="password" />
                <InputError class="mt-2" :message="form.errors.password" />

                <InputLabel for="confirm password" value="Confirm Password" class="mt-4" />
                <TextInput type="password" class="mt-1 block w-full" v-model="form.confirmPassword" required autofocus autocomplete="consfirm-password" />
                <InputError class="mt-2" :message="form.errors.confirmPassword" />

                <div class="pt-4 mt-8">
                    <div>
                        <button type="submit" :disabled="form.processing" :class="{ 'opacity-25': form.processing }" class="font-bold text-lg flex w-full justify-center rounded-md border border-transparent bg-primary-blue py-2 px-4 text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Change Password</button>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>