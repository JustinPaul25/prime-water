<script setup>
    import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecieptModal from '@/Modals/RecieptModal.vue';
import VTailwindModal from '@/Modals/VTailwindModal.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { watchDebounced } from '@vueuse/core';
import axios from 'axios';
import moment from 'moment';
import { computed, inject, onMounted, ref } from 'vue';
import { useStore } from 'vuex';
    // Import stylesheet
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

    const search = ref('')
    const reminder = ref(false)
    const showPayment = ref(false)
    const showReciept = ref(false)
    const sending = ref(false)
    const client = ref(null)
    const change = ref(0)
    const amount = ref(null)
    const paidAmount = ref(null)
    const amountToPrint = ref(0)
    const newBalance = ref(0)
    const month = ref(["January","February","March","April","May","June","July","August","September","October","November","December"])
    const transactions = ref([])

    const store = useStore()
    const swal = inject('$swal')
    const clients = computed(() => store.state.clients.clients)
    const pagination = computed(() => store.state.clients.pagination)

    const form = useForm({
        id: '',
        amount: '',
    });

    watchDebounced(search, () => {
        getClients()
    }, {debounce: 500})

    watchDebounced(reminder, () => {
        getClients()
    }, {debounce: 500})

    watchDebounced(amount, () => {
        if(amount.value) {
            let totalBill = Number(client.value.account.prev_balance) + Number(client.value.account.current_charges)
            paidAmount.value = amount.value
            if(Number(amount.value) > totalBill) {
                change.value = Number(amount.value) - totalBill
                form.amount = totalBill
                newBalance.value = 0
            } else {
                console.log(amount.value)
                form.amount = amount.value
                newBalance.value = totalBill - Number(amount.value)
                change.value = 0
            }
        }
    }, {debounce: 500})

    function getClients(page) {
        store.dispatch('clients/getClients', {
            params: {
                page: page,
                search: search.value,
                reminder: reminder.value
            }
        })
    }

    function showPaymentModal(cust) {
        client.value = cust
        amount.value = ''
        getTransactions()

        showPayment.value = true
    }

    function cancel(close) {
        showPayment.value = false
    }

    function cancelReciept(close) {
        showReciept.value = false
    }

    const submit = () => {
        form.id = client.value.id
        amountToPrint.value = form.amount

        axios.post('/pay-bill', form)
        .then(response => {
            form.reset('id')
            form.reset('amount')
            showPayment.value = false
            showReciept.value = true
            amount.value = ''
            getClients()
            swal.fire({
                icon: 'success',
                title: 'Successfully Paid',
                text: `Payment reflected to ${client.value.first_name} ${client.value.last_name}`,
                confirmButtonColor: '#23408E'
            })
        })
    };

    const sendReminder = (id) => {
        axios.get(`/send-sms/${id}`)
        .then(response => {
            swal.fire({
                icon: 'success',
                title: 'Reminder Sent',
                confirmButtonColor: '#23408E',
                backdrop: 'static',
                allowOutsideClick: false,
                allowEscapeKey: false
            })
        })
    }

    const notifyAll = () => {
        sending.value = true
        axios.get('/notify-all')
        .then(response => {
            sending.value = false
            swal.fire({
                title: "Sent",
                text: "Successfully sent to selected users.",
                icon: "success",
                buttons: false,
                timer: 2000,
            })
        })
    }

    const getMonth = () => {
        const d = new Date();

        return month.value[d.getMonth()];
    }

    const getPrevMonth = () => {
        const d = new Date();

        return month.value[d.getMonth()-1];
    }

    const getCurrentYear = () => {
        const d = new Date();

        return d.getFullYear();
    }

    const getTransactions = () => {
        axios.get(`/client-transactions/${client.value.id}`)
        .then(response => {
            transactions.value = response.data
        })
    }

    const renderChange = (amount) => {
        if(amount < 0) {
            return Math.abs(Number(amount));
        } else {
            return 0
        }
    }

    onMounted(() => store.dispatch('clients/getClients'))
</script>

<template>
    <Head title="Payments"/>

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
                                        <p>Previous Balance: ₱ {{ Number(client.account.prev_balance).toLocaleString() }}</p>
                                        <p>Current Bill: ₱ {{ Number(client.account.current_charges).toLocaleString() }}</p>
                                        <p>Billed At: {{ client.account.current_date_reading }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mt-1 text-base text-gray-500">Total Balance:</p>
                                        <p class="text-4xl font-bold tracking-tight text-indigo-600 sm:text-5xl">₱ {{ (Number(client.account.current_charges) + Number(client.account.prev_balance)).toLocaleString() }}</p>
                                    </div>
                                </main>
                                <div>
                                    <div class="relative rounded-md shadow-sm mt-10">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <!-- Heroicon name: mini/envelope -->
                                        <p class="text-gray-400">₱</p>
                                        </div>
                                        <input v-model="amount" type="number" name="amount" placeholder="Input Amount" class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.amount" />
                                    <div class="flex mt-2 space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                                        <button @click="cancel()" href="#" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ml-auto">Close</button>
                                        <button @click="submit()" class="inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Process Payment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </v-tailwind-modal>
            <reciept-modal v-if="client" v-model="showReciept" @cancel="cancelReciept()">
                <section class="bg-white" id="printReciept">
                    <div class="max-w-5xl mx-auto bg-white">
                    <article class="overflow-hidden">
                    <div class="bg-[white] rounded-b-md">
                        <div class="p-4 text-center text-xs">
                            <p>Barangay Consolacion</p>
                            <p>Water Bill</p>
                            <p class="font-bold">Month of {{ getMonth() }}</p>
                        </div>

                        <div class="flex text-xs">
                            <p>Name of Consumer: </p>
                            <p class="font-bold mx-auto">{{client.first_name}} {{client.last_name}}</p>
                        </div>
                        <div class="flex text-xs">
                            <p>Address: </p>
                            <p class="mx-auto">{{client.address}}</p>
                        </div>
                        <div class="flex text-xs">
                            <p>Previous Reading: </p>
                            <p class="mx-auto">{{client.account.prev_reading}}</p>
                        </div>
                        <div v-if="client.account.prev_date_reading" class="flex text-xs">
                            <p>Current Date Metered:</p>
                            <p class="mx-auto">{{ client.account.prev_date_reading }}</p>
                        </div>
                        <div class="flex text-xs">
                            <p>Current Reading: </p>
                            <p class="mx-auto">{{client.account.current_reading}}</p>
                        </div>
                        <div v-if="client.account.current_date_reading" class="flex text-xs">
                            <p>Current Date Metered:</p>
                            <p class="mx-auto">{{ client.account.current_date_reading }}</p>
                        </div>
                        <div class="flex text-xs">
                            <p class="font-bold">Consumed Cu. M: </p>
                            <p class="mx-auto font-bold">{{client.account.current_reading-client.account.prev_reading}}</p>
                        </div>

                        <div class="flex text-xs mt-4">
                            <p>Last Month Remaining Balance: </p>
                            <p class="mx-auto">₱ {{Number(client.account.prev_balance).toLocaleString()}}</p>
                        </div>
                        <div class="flex text-xs">
                            <p>Current Bill - {{getMonth()}} {{getCurrentYear()}}: </p>
                            <p class="mx-auto">₱ {{Number(client.account.current_charges).toLocaleString()}}.00</p>
                        </div>
                        <div class="flex text-sm font-bold mt-2">
                            <p>Total Bill: </p>
                            <p class="ml-auto">₱ {{(Number(client.account.current_charges)+Number(client.account.prev_balance)).toLocaleString()}}.00</p>
                        </div>
                        <div class="flex text-sm font-bold">
                            <p>Paid Amount: </p>
                            <p class="ml-auto">₱ {{Number(paidAmount).toLocaleString()}}.00</p>
                        </div>
                        <div class="flex text-sm font-bold">
                            <p>New Remaining Balance: </p>
                            <p class="ml-auto">₱ {{newBalance.toLocaleString()}}.00</p>
                        </div>
                        <div class="flex text-sm font-bold">
                            <p>Change: </p>
                            <p class="ml-auto">₱ {{Number(change).toLocaleString()}}.00</p>
                        </div>

                        <div v-if="transactions.length !== 0" class="flex text-xs mt-8">
                            <p>Last Payment:</p>
                            <p class="ml-4">{{moment(transactions[transactions.length - 1]?.created_at).format('YYYY-MM-DD')}}</p>
                            <p class="ml-4 ">Amount:</p>
                            <p class="ml-4 font-bold">₱ {{Number(transactions[transactions.length - 1]?.amount).toLocaleString()}}.00</p>
                        </div>
                    </div>
                    </article>
                    </div>
                </section>
                <div class="flex">
                    <button v-print="'#printReciept'" class="ml-auto bg-blue-800 px-4 py-2 rounded-md text-white font-bold hover:opacity-75">Print</button>
                </div>
            </reciept-modal>
        </div>
        <div class="py-12">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center justify-center mt-4 w-full">
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="search" value="Search" />
                        <TextInput id="search" type="text" class="mt-1 block w-full" v-model="search"/>
                    </div>
                    <div class="mt-auto mb-2 flex items-center">
                        <input v-model="reminder" type="checkbox" class="appearance-none bg-gray-300 my" />
                        <p class="ml-2 font-bold text-gray-700">View Only With Reminders</p>
                    </div>
                    <div class="ml-auto">
                        <button :disabled="sending" @click="notifyAll()" class="flex bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            <p v-if="sending" class="flex">Sending... <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="animate-spin ml-2"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg></p>
                            <p v-else>Remind All User</p>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mx-4">
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
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full last:mr-0 mr-1" :class="client.deleted_at ? 'text-red-600 bg-red-200' : 'text-green-600 bg-green-200'">
                                        {{ client.deleted_at ? 'In-Active' : 'Active'}}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a v-if="parseInt(client.account.month_last_payment) >= 3" @click="sendReminder(client.id)" href="#" class="text-yellow-500 hover:opacity-75 mr-8">Send Reminder!</a>
                                    <button @click="showPaymentModal(client)" class="text-primary-blue hover:opacity-75">Generate Payment</button>
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
