<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import VTailwindModal from '@/Modals/VTailwindModal.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { computed, ref, inject, onMounted } from 'vue'
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import Pagination from '@/Components/Pagination.vue';
    import { watchDebounced } from '@vueuse/core';
    import { useStore } from 'vuex';
    import InputError from '@/Components/InputError.vue';

    const search = ref('')
    const showPayment = ref(false)
    const client = ref('')

    const store = useStore()
    const swal = inject('$swal')
    const clients = computed(() => store.state.clients.clients)
    const pagination = computed(() => store.state.clients.pagination)

    const form = useForm({
        id: '',
        amount: 0,
    });

    watchDebounced(search, () => {
        getClients()
    }, {debounce: 500})

    function getClients(page) {
        store.dispatch('clients/getClients', {
            params: {
                page: page,
                search: search.value
            }
        })
    }

    function showPaymentModal(cust) {
        client.value = cust
        
        showPayment.value = true
    }

    function cancel(close) {
        showPayment.value = false
    }

    const submit = () => {
        form.id = client.value.id
        form.post(route('pay-bill'), {
            onSuccess: () => {
                form.reset('id')
                form.reset('amount')
                showPayment.value = false
                getClients()
                swal.fire({
                    icon: 'success',
                    title: 'Successfully Paid',
                    text: `Payment reflected to ${client.value.first_name} ${client.value.last_name}`,
                    confirmButtonColor: '#23408E'
                })
            },
        });
    };

    const sendReminder = (id) => {
        axios.get(`/send-sms/${id}`)
        .then(response => {
            swal.fire({
                icon: 'success',
                title: 'Reminder Sent',
                confirmButtonColor: '#23408E'
            })
        })
    }

    onMounted(() => store.dispatch('clients/getClients'))
</script>
    
<template>
    <Head title="Cashier" />
    
    <AuthenticatedLayout>
        <div>
            <v-tailwind-modal v-model="showPayment" @cancel="cancel()">
                <div class="flex justify-center">
                        <div class="min-h-full bg-white px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
                            <div class="mx-auto max-w-max">
                                <main v-if="client" class="sm:flex">
                                    <div class="sm:mr-6">
                                        <div class="sm:border-r sm:border-gray-200 sm:pr-6">
                                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">{{ client.first_name+' '+client.last_name }}</h1>
                                        <p class="mt-1 text-base text-gray-500">Prev. Bill: ₱ {{ client.account.prev_balance }} | Current Bill: ₱ {{ client.account.current_charges }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mt-1 text-base text-gray-500">Total Balance:</p>
                                        <p class="text-4xl font-bold tracking-tight text-indigo-600 sm:text-5xl">₱ {{ client.account.prev_balance + client.account.current_charges }}</p>
                                    </div>
                                </main>
                                <form @submit.prevent="submit">
                                    <div class="relative rounded-md shadow-sm mt-10">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <!-- Heroicon name: mini/envelope -->
                                        <p class="text-gray-400">₱</p>
                                        </div>
                                        <input v-model="form.amount" type="number" name="amount" class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.amount" />
                                    <div class="flex mt-2 space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                                        <button @click="cancel()" href="#" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ml-auto">Close</button>
                                        <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Process Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
            </v-tailwind-modal>
        </div>
        <div class="py-12">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center mt-4 w-full">
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="search" value="Search" />
                        <TextInput id="search" type="text" class="mt-1 block w-full" v-model="search"/>
                    </div>
                </div>
            </div>
            <div class="mx-4">
                <div class="-mx-4 mt-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:-mx-6 md:mx-0 md:rounded-lg bg-white mx-4">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-primary-blue">
                            <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white font-bold sm:pl-6">Name</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-white font-bold lg:table-cell">Username</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-white font-bold lg:table-cell">Status</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell"></th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="client in clients.data">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    <a class="font-bold text-primary-blue hover:opacity-70 cursor-pointer">{{ client.first_name+' '+client.last_name }}</a>
                                </td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">{{ client.username }}</td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full last:mr-0 mr-1" :class="client.status === 0 ? 'text-red-600 bg-red-200' : 'text-green-600 bg-green-200'">
                                        {{ client.status === 0 ? 'In-Active' : 'Active'}}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a v-if="parseInt(client.account.month_last_payment) >= 3" @click="sendReminder(client.id)" href="#" class="text-yellow-500 hover:opacity-75 mr-8">Send Reminder!</a>
                                    <a @click="showPaymentModal(client)" href="#" class="text-primary-blue hover:opacity-75">Generate Payment</a>
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
    