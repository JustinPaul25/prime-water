<script setup>
    import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecieptModal from '@/Modals/RecieptModal.vue';
import VTailwindModal from '@/Modals/VTailwindModal.vue';
import { Inertia } from '@inertiajs/inertia';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { watchDebounced } from '@vueuse/core';
import axios from 'axios';
import { computed, inject, onMounted, ref } from 'vue';
import { useStore } from 'vuex';

    defineProps({
        status: String,
    });

    const store = useStore()
    const swal = inject('$swal')
    const clients = computed(() => store.state.clients.clients)
    const pagination = computed(() => store.state.clients.pagination)

    let show = ref(false)
    let showQr = ref(false)
    let showQrAll = ref(false)
    let isEdit = ref(false)
    const search = ref('')
    const status = ref('')
    const purok = ref('')
    const reminder = ref(false)
    const qrUrl = ref('http://prime-water.test/dashboard')
    const clientsQr = ref([])

    const form = useForm({
        id: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        username: '',
        contact_no: '',
        address: '',
        status: '',
        name: ''
    });

    watchDebounced(search, () => {
        getClients()
    }, {debounce: 500})

    watchDebounced(status, () => {
        getClients()
    }, {debounce: 500})

    watchDebounced(purok, () => {
        getClients()
    }, {debounce: 500})

    watchDebounced(
        () => form.first_name,
        (first_name) => {
            form.first_name = capitalizeString(first_name)
        },
        {debounce: 300}
    )

    watchDebounced(
        () => form.middle_name,
        (middle_name) => {
            form.middle_name = capitalizeString(middle_name)
        },
        {debounce: 300}
    )

    watchDebounced(
        () => form.last_name,
        (last_name) => {
            form.last_name = capitalizeString(last_name)
        },
        {debounce: 300}
    )

    watchDebounced(
        () => form.contact_no,
        (contact_no) => {
            form.contact_no = checkContact(contact_no)
        },
        {debounce: 300}
    )

    const checkContact = (text) => {
        let result = text.replace(/[^a-zA-Z0-9 ]/g, "");
        result = result.replace(/([a-zA-Z ])/g, "")

        return result;
    }

    const capitalizeString = (text) => {
        const words = text.split(" ");

        // return text.toLowerCase();
        for (let i = 0; i < words.length; i++) {
            words[i] = words[i].charAt(0).toUpperCase() + words[i].substr(1);
        }

        return words.join(" ");
    }

    function showModal(type, client) {
        if(type === 'create') {
            isEdit.value = false
            form.id = ''
            form.first_name = ''
            form.middle_name = ''
            form.last_name = ''
            form.username = ''
            form.contact_no = ''
            form.address = ''
            form.status = ''
        } else {
            isEdit.value = true
            form.id = client.id
            form.first_name = client.first_name
            form.middle_name = client.middle_name
            form.last_name = client.last_name
            form.username = client.username
            form.contact_no = client.contact_no
            form.address = client.address
            form.status = client.status
        }

        show.value = true
    }

    function showQrModal(id) {
        showQr.value = true
        qrUrl.value = id
    }

    function cancel(close) {
        show.value = false
        showQr.value = false
    }

    const allQr = () => {
        showQrAll.value = true
        getAllUsers()
    }

    function cancelShowAll(close) {
        showQrAll.value = false
    }

    function submitSuccess() {
        show.value = false
        store.dispatch('clients/getClients')
        swal.fire({
            icon: 'success',
            title: isEdit.value ? 'Client Updated!' : 'Client Saved!',
            text: isEdit.value ? '' : `The user password is default "pwclient"`,
            confirmButtonColor: '#23408E'
        })
    }

    function deleteSuccess() {
        store.dispatch('clients/getClients')
        swal.fire({
            title: 'Deleted!',
            text: 'Client has been deleted.',
            icon: 'success',
            confirmButtonColor: '#23408E'
        })
    }

    function deleteClient(id) {
        swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#23408E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Inertia.delete(route("client.delete", id), {
                    onSuccess: () => deleteSuccess()
                })
            }
        })
    }

    function getClients(page) {
        store.dispatch('clients/getClients', {
            params: {
                page: page,
                search: search.value,
                status: status.value,
                purok: purok.value
            }
        })
    }

    const submit = (id) => {
        const fullname = form.middle_name ? form.first_name+' '+form.middle_name+' '+form.last_name : form.first_name+' '+form.last_name
        form.name = fullname
        if(isEdit.value) {
            form.put(`/client/${form.id}`, {
                onSuccess: () => submitSuccess()
            });
        } else {
            form.post(route('client.create'), {
                onSuccess: () => submitSuccess()
            });
        }
    };

    const getAllUsers = () => {
        axios.get('/all-clients')
        .then(response => {
            clientsQr.value = response.data
        })
    }

    const statusSwitch = (id) => {
        swal.fire({
            title: 'Are you sure?',
            text: "Client will switch status!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#23408E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, switch it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.get(`/switch-status/${id}`)
                .then(response => {
                    store.dispatch('clients/getClients')
                })
            }
        })
    }

    onMounted(() => store.dispatch('clients/getClients'))
</script>

<template>
    <Head title="Client"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Clients
            </h2>
            <p class="mt-2 text-sm text-white">A list of all Barangay Consolacion Clients including their information.</p>
        </template>

        <div class="py-12">
            <div>
                <RecieptModal v-model="showQrAll" @cancel="cancelShowAll()">
                    <section class="bg-white" id="printQr">
                        <div class="max-w-5xl mx-auto bg-white">
                            <article class="overflow-hidden">
                                <div class="bg-[white] rounded-b-md">
                                    <div class="p-9">
                                        <p class="font-bold text-lg">WBS Client Qr's</p>
                                        <p>Barangay Consolacion, Panabo City, Davao del Norte</p>
                                    </div>

                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="text-center" v-for="client in clientsQr">
                                            <vue-qrcode :value="`${client.id}`" :options="{ width: 200 }"></vue-qrcode>
                                            <p class="text-center my-auto">{{ client.first_name }} {{ client.last_name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>
                    <div class="flex">
                        <button v-print="'#printQr'" class="ml-auto bg-blue-800 px-4 py-2 rounded-md text-white font-bold hover:opacity-75">Print</button>
                    </div>
                </RecieptModal>
                <v-tailwind-modal v-model="show" @cancel="cancel()">
                    <template v-slot:title>{{isEdit ? 'Update Client' : 'Create Client'}}</template>
                    <div>
                        <form @submit.prevent="submit">
                            <div class="mt-4">
                                <InputLabel for="first_name" value="First Name" />
                                <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required autofocus autocomplete="first_name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="middle_name" value="Middle Name" />
                                <TextInput id="middle_name" type="text" class="mt-1 block w-full" v-model="form.middle_name" autofocus autocomplete="middle_name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="last_name" value="Last Name" />
                                <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" required autofocus autocomplete="last_name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="contact_no" value="Contact Number" />
                                <TextInput maxlength="11" id="contact_no" type="text" class="mt-1 block w-full" v-model="form.contact_no" required autofocus autocomplete="contact_no" />
                                <InputError class="mt-2" :message="form.errors.contact_no" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="address" value="Complete Address" />
                                <select v-model="form.address" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="Prk - 1 Consolacion Panabo City Davao Del Norte">Prk - 1 Consolacion Panabo City Davao Del Norte</option>
                                    <option value="Prk - 1A Consolacion Panabo City Davao Del Norte">Prk - 1A Consolacion Panabo City Davao Del Norte</option>
                                    <option value="Prk - 2 Consolacion Panabo City Davao Del Norte">Prk - 2 Consolacion Panabo City Davao Del Norte</option>
                                    <option value="Prk - 3 Consolacion Panabo City Davao Del Norte">Prk - 3 Consolacion Panabo City Davao Del Norte</option>
                                    <option value="Prk - 4 Consolacion Panabo City Davao Del Norte">Prk - 4 Consolacion Panabo City Davao Del Norte</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="username" value="Username" />
                                <TextInput id="username" type="text" class="mt-1 block w-full" v-model="form.username" required autocomplete="username" />
                                <InputError class="mt-2" :message="form.errors.username" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{isEdit ? 'Update Client' : 'Save Client'}}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </v-tailwind-modal>
            </div>
            <div>
                <v-tailwind-modal v-model="showQr" @cancel="cancel()">
                    <div class="flex justify-center">
                        <vue-qrcode :value="qrUrl" :options="{ width: 200 }"></vue-qrcode>
                    </div>
                </v-tailwind-modal>
            </div>
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center mt-4 w-full">
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="search" value="Search" />
                        <TextInput id="search" type="text" class="mt-1 block w-full" v-model="search"/>
                    </div>
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="search" value="Status" />
                        <select v-model="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">In-Active</option>
                        </select>
                    </div>
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="search" value="Status" />
                        <select v-model="purok" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">All</option>
                            <option value="Prk - 1 Consolacion Panabo City Davao Del Norte">Prk - 1 Consolacion Panabo City Davao Del Norte</option>
                            <option value="Prk - 1A Consolacion Panabo City Davao Del Norte">Prk - 1A Consolacion Panabo City Davao Del Norte</option>
                            <option value="Prk - 2 Consolacion Panabo City Davao Del Norte">Prk - 2 Consolacion Panabo City Davao Del Norte</option>
                            <option value="Prk - 3 Consolacion Panabo City Davao Del Norte">Prk - 3 Consolacion Panabo City Davao Del Norte</option>
                            <option value="Prk - 4 Consolacion Panabo City Davao Del Norte">Prk - 4 Consolacion Panabo City Davao Del Norte</option>
                        </select>
                    </div>
                    <div class="ml-auto flex">
                        <button  @click="allQr()" type="button" class="mr-2 mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Print All QR</button>
                        <button  @click="showModal('create', null)" type="button" class="ml-2 mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Add Client</button>
                    </div>
                </div>
                <div class="-mx-4 mt-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:-mx-6 md:mx-0 md:rounded-lg bg-white">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-primary-blue">
                            <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Name</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-white lg:table-cell">Username</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-white lg:table-cell">Status</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell"></th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="client in clients.data">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    <a :href="route('client.profile', client.id)" class="font-bold text-primary-blue hover:opacity-70 cursor-pointer">{{ client.first_name+' '+client.last_name }}</a>
                                </td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">{{ client.username }}</td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full last:mr-0 mr-1" :class="client.status === false ? 'text-red-600 bg-red-200' : 'text-green-600 bg-green-200'">
                                        {{ client.status === false ? 'In-Active' : 'Active'}}
                                    </span>
                                </td>
                                <td class="hidden whitespace-nowrap px-3 text-sm text-gray-500 lg:table-cell">
                                    <span @click="showQrModal(client.id)" class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full last:mr-0 mr-1 text-primary-blue hover:opacity-75 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                        </svg>
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <button @click="statusSwitch(client.id)" :class=" client.status === 1 ? 'text-red-600': 'text-green-600'" class="hover:opacity-75 mr-4">{{client.status === 1 ? 'Switch to Inactive' : 'Switch to Active'}}</button>
                                    <a @click="showModal('update', client)" href="#" class="text-primary-blue hover:opacity-75">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                    <pagination class="mt-6"
                            :pagination="pagination"
                            @paginate="getClients"
                            :offset="4" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
