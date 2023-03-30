<script setup>
    import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import VTailwindModal from '@/Modals/VTailwindModal.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, inject, onMounted, ref } from 'vue';
import { useStore } from 'vuex';

    const store = useStore()
    const addresses = computed(() => store.state.addresses.addresses)
    const pagination = computed(() => store.state.addresses.pagination)
    const swal = inject('$swal')

    let show = ref(false)
    let isEdit = ref(false)
    const search = ref('')

    watchDebounced(search, () => {
        getAddresses()
    }, {debounce: 500})

    const form = useForm({
        id: '',
        prk: '',
    });

    function showModal(type, address) {
        if(type === 'create') {
            isEdit.value = false
            form.id = ''
            form.prk = ''
        } else {
            isEdit.value = true
            form.id = address.id
            form.prk = address.prk
        }

        show.value = true
    }

    function cancel(close) {
        show.value = false
    }

    function getAddresses(page) {
        store.dispatch('addresses/getAddresses', {
            params: {
                page: page,
                search: search.value
            }
        })
    }

    const submit = (id) => {
        if(isEdit.value) {
            form.put(`/address/${form.id}`, {
                onSuccess: () => submitSuccess()
            });
        } else {
            form.post(route('address.create'), {
                onSuccess: () => submitSuccess()
            });
        }
    };

    function submitSuccess() {
        show.value = false
        store.dispatch('addresses/getAddresses')
        swal.fire({
            icon: 'success',
            title: isEdit.value ? 'Address Updated!' : 'Address Saved!',
            confirmButtonColor: '#23408E'
        })
    }

    onMounted(() => store.dispatch('addresses/getAddresses'))
</script>

<template>
    <Head title="Addresses"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Clients
            </h2>
            <p class="mt-2 text-sm text-white">A list of all Barangay Consolacion Addresses.</p>
        </template>

    <div class="py-12">
        <div>
            <v-tailwind-modal v-model="show" @cancel="cancel()">
                    <template v-slot:title>{{isEdit ? 'Update Address' : 'Create Address'}}</template>
                    <div>
                        <form @submit.prevent="submit">
                            <div class="mt-4">
                                <InputLabel for="prk" value="Purok" />
                                <TextInput id="prk" type="text" class="mt-1 block w-full" v-model="form.prk" required autofocus autocomplete="first_name" />
                                <InputError class="mt-2" :message="form.errors.prk" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{isEdit ? 'Update Address' : 'Save Address'}}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </v-tailwind-modal>
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center mt-4 w-full">
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="search" value="Search" />
                        <TextInput id="search" type="text" class="mt-1 block w-full" v-model="search"/>
                    </div>
                    <div class="ml-auto flex">
                        <button  @click="showModal('create', null)" type="button" class="ml-2 mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Add Address</button>
                    </div>
                </div>
                <div class="-mx-4 mt-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:-mx-6 md:mx-0 md:rounded-lg bg-white">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-primary-blue">
                            <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Address</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="address in addresses.data">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    <p class="font-bold text-primary-blue hover:opacity-70 cursor-pointer">{{ address.address }}</p>
                                </td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a @click="showModal('update', address)" href="#" class="text-primary-blue hover:opacity-75">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <pagination class="mt-6"
                            :pagination="pagination"
                            @paginate="getAddresses"
                            :offset="4" />
                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
</template>
