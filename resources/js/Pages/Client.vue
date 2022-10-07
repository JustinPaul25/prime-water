<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import VTailwindModal from '@/Modals/VTailwindModal.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import Pagination from '@/Components/Pagination.vue'
    import { Inertia } from '@inertiajs/inertia';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { computed, onMounted, inject, ref, watch } from 'vue'
    import { useStore } from 'vuex'
    import debounce from 'lodash/debounce'
    import { watchDebounced } from '@vueuse/core'

    defineProps({
        status: String,
    });

    const store = useStore()
    const swal = inject('$swal')
    const clients = computed(() => store.state.clients.clients)
    const pagination = computed(() => store.state.clients.pagination)

    let show = ref(false)
    let showQr = ref(false)
    let isEdit = ref(false)
    const search = ref('')
    const qrUrl = ref('http://prime-water.test/dashboard')
    
    watchDebounced(search, () => {
        getClients()
    }, {debounce: 500})

    const form = useForm({
        id: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        contact_no: '',
        address: '',
        status: ''
    });

    function showModal(type, client) {
        if(type === 'create') {
            isEdit.value = false
            form.id = ''
            form.first_name = ''
            form.middle_name = ''
            form.last_name = ''
            form.email = ''
            form.contact_no = ''
            form.address = ''
            form.status = ''
        } else {
            isEdit.value = true
            form.id = client.id
            form.first_name = client.first_name
            form.middle_name = client.middle_name
            form.last_name = client.last_name
            form.email = client.email
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

    function submitSuccess() {
        show.value = false
        store.dispatch('clients/getClients')
        swal.fire({
            icon: 'success',
            title: isEdit.value ? 'Client Updated!' : 'Client Saved!',
            text: isEdit.value ? '' : `The user password is default "PW-Client"`,
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
                search: search.value
            }
        })
    }

    const submit = (id) => {
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

    onMounted(() => store.dispatch('clients/getClients'))
</script>
    
<template>
    <Head title="Staff"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Clients
            </h2>
            <p class="mt-2 text-sm text-white">A list of all Prime Waters Clients including their information.</p>
        </template>

        <div class="py-12">
            <div>
                <v-tailwind-modal v-model="show" @cancel="cancel()">
                    <template v-slot:title>{{isEdit ? 'Update Client' : 'Create Client'}}</template>
                    <div>
                        <form @submit.prevent="submit">
                            <div class="mt-4">
                                <InputLabel for="first_name" value="First Name" />
                                <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required autofocus autocomplete="first_name" />
                                <InputError class="mt-2" :message="form.errors.first_name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="middle_name" value="Middle Name" />
                                <TextInput id="middle_name" type="text" class="mt-1 block w-full" v-model="form.middle_name" autofocus autocomplete="middle_name" />
                                <InputError class="mt-2" :message="form.errors.middle_name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="last_name" value="Last Name" />
                                <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" required autofocus autocomplete="last_name" />
                                <InputError class="mt-2" :message="form.errors.last_name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="contact_no" value="Contact Number" />
                                <TextInput id="contact_no" type="text" class="mt-1 block w-full" v-model="form.contact_no" required autofocus autocomplete="contact_no" />
                                <InputError class="mt-2" :message="form.errors.contact_no" />
                            </div>
                            
                            <div class="mt-4">
                                <InputLabel for="address" value="Complete Address" />
                                <TextInput id="address" type="text" class="mt-1 block w-full" v-model="form.address" required autofocus autocomplete="address" />
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="status" value="Status" />
                                <select v-model="form.status" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="1">Active</option>
                                    <option value="0">In-Active</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
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
                    <div class="ml-auto">
                        <button  @click="showModal('create', null)" type="button" class="mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Add Client</button>
                    </div>
                </div>
                <div class="-mx-4 mt-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:-mx-6 md:mx-0 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">Email</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">Status</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell"></th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="client in clients.data">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ client.name }}</td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">{{ client.email }}</td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full uppercase last:mr-0 mr-1" :class="client.status === 0 ? 'text-red-600 bg-red-200' : 'text-green-600 bg-green-200'">
                                        {{ client.status === 0 ? 'In-Active' : 'Active'}}
                                    </span>
                                </td>
                                <td class="hidden whitespace-nowrap px-3 text-sm text-gray-500 lg:table-cell">
                                    <span @click="showQrModal(client.id)" class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full uppercase last:mr-0 mr-1 text-primary-blue hover:opacity-75 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                        </svg>
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a @click="deleteClient(client.id)" href="#" class="text-red-400 hover:opacity-75 mr-2">Delete</a>
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
    