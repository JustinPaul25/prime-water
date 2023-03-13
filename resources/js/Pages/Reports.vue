<script setup>
    import InputLabel from '@/Components/InputLabel.vue';
import ReportsChart from '@/Components/ReportsChart.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecieptModal from '@/Modals/RecieptModal.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { computed, inject, onMounted, ref, watch } from 'vue';
import { useStore } from 'vuex';

    defineProps({
        income_data: Object,
    });

    const store = useStore()
    const swal = inject('$swal')
    const staffs = computed(() => store.state.staffs.staffs)
    const pagination = computed(() => store.state.staffs.pagination)

    let show = ref(false)
    let isEdit = ref(false)
    const role = ref('')
    const searchYear = ref('')
    const searchDate = ref('')
    const searchFrom = ref('')
    const searchTo = ref('')
    const searchBy = ref('')
    const sortBy = ref('')
    const transactions = ref([])
    const showPrint = ref(false)

    const amount = computed(() => {
        return transactions.value.reduce((accumulator, object) => {
            return parseInt(accumulator) + parseInt(object.amount);
        }, 0);
    })

    const consumed = computed(() => {
        return transactions.value.reduce((accumulator, object) => {
            return accumulator + (object.client.account.current_reading - object.client.account.prev_reading);
        }, 0);
    })

    watch(searchDate, (newValue, oldValue) => {
        getTransactions()
    })

    watch(searchYear, (newValue, oldValue) => {
        getTransactions()
    })

    watch(searchFrom, (newValue, oldValue) => {
        if(searchTo.value !== '') {
            getTransactions()
        }
    })

    watch(searchTo, (newValue, oldValue) => {
        if(searchFrom.value !== '') {
            getTransactions()
        }
    })


    async function getTransactions() {
        await axios.get('/transactions', {
                params: {
                    searchYear: searchYear.value,
                    searchDate: searchDate.value,
                    searchFrom: searchFrom.value,
                    searchTo: searchTo.value
                }
        })
        .then(response => {
            console.log(response.data)
            transactions.value = response.data
        })
    }

    const cancel = () => {
        showPrint.value = false
    }

    onMounted(() => getTransactions())
</script>

<template>
    <Head title="Income Report"/>

    <AuthenticatedLayout>
        <RecieptModal v-model="showPrint" @cancel="cancel()">
            <div class="flex mt-4">
                <button v-print="'#printQr'" class="mx-auto bg-blue-800 px-4 py-2 rounded-md text-white font-bold hover:opacity-75">Print</button>
            </div>
            <section class="bg-white" id="printQr">
                <div class="max-w-5xl mx-auto bg-white">
                    <article class="overflow-hidden">
                        <div class="bg-[white] rounded-b-md">
                            <div class="p-9">
                                <p class="font-bold text-lg">WBS Income Report</p>
                                <p>Barangay Consolacion, Panabo City, Davao del Norte</p>
                            </div>

                            <div class="flex mx-2">
                                <div class="ml-auto">
                                    <p class="font-bold text-sm">Total Income:</p>
                                    <p class="font-bold text-3xl text-primary-blue">₱ {{Number(amount).toLocaleString()}}.00</p>
                                </div>
                                <div class="ml-4">
                                    <p class="font-bold text-sm">Total Consumed Cu M:</p>
                                    <p class="font-bold text-3xl text-primary-blue text-right">{{consumed}}</p>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-slate-500">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-normal text-slate-700 sm:pl-6 md:pl-0">
                                            Transaction #
                                        </th>
                                        <th scope="col" class="py-3.5 pl-3 pr-4 text-center text-sm font-normal text-slate-700 sm:pr-6 md:pr-0">
                                            Client
                                        </th>
                                        <th scope="col" class="py-3.5 pl-3 pr-4 text-center text-sm font-normal text-slate-700 sm:pr-6 md:pr-0">
                                            Cu M
                                        </th>
                                        <th scope="col" class="py-3.5 pl-3 pr-4 text-center text-sm font-normal text-slate-700 sm:pr-6 md:pr-0">
                                            Amount Paid
                                        </th>
                                        <th scope="col" class="py-3.5 pl-3 pr-4 text-center text-sm font-normal text-slate-700 sm:pr-6 md:pr-0">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="transaction in transactions">
                                        <td>{{ transaction.id }}</td>
                                        <td class="text-center">{{ transaction.client?.name }}</td>
                                        <td class="text-center">{{ transaction.client.account.current_reading - transaction.client.account.prev_reading }}</td>
                                        <td class="text-center">₱ {{ Number(transaction.amount).toLocaleString() }}.00</td>
                                        <td class="text-center">{{ transaction.date_paid }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </article>
                </div>
            </section>
        </RecieptModal>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight w-full">
                Income Reports
            </h2>
        </template>

        <div class="py-12">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex mb-8">
                    <div class="w-3/4">
                        <ReportsChart :datas="income_data"/>
                    </div>
                    <div class="ml-auto">
                        <p class="font-bold text-sm">Total Income:</p>
                        <p class="font-bold text-3xl text-primary-blue">₱ {{Number(amount).toLocaleString()}}.00</p>
                    </div>
                </div>
                <div class="sm:flex sm:items-center w-full">
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="role" value="Search By:" />
                        <select v-model="searchBy" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">None</option>
                            <option value="date">Date</option>
                            <option value="between">Date Between</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                    <div v-if="searchBy === 'date'" class="mr-4">
                        <InputLabel class="font-bold" for="date" value="Date" />
                        <TextInput id="search" type="date" class="mt-1 block w-full" v-model="searchDate"/>
                    </div>
                    <div v-if="searchBy === 'between'" class="mr-4">
                        <InputLabel class="font-bold" for="date" value="Date From" />
                        <TextInput id="search" type="date" class="mt-1 block w-full" v-model="searchFrom"/>
                    </div>
                    <div v-if="searchBy === 'between'" class="mr-4">
                        <InputLabel class="font-bold" for="date" value="Date To" />
                        <TextInput id="search" type="date" class="mt-1 block w-full" v-model="searchTo"/>
                    </div>
                    <div v-if="searchBy === 'year'" class="mr-4">
                        <InputLabel class="font-bold" for="date" value="Year" />
                        <TextInput id="search" type="number" min="1980" max="2030" class="mt-1 block w-full" v-model="searchYear"/>
                    </div>
                    <div class="ml-auto">
                        <button @click="showPrint = true" class="mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Print Report</button>
                    </div>
                </div>
                <div class="-mx-4 mt-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:-mx-6 md:mx-0 md:rounded-lg bg-white">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-primary-blue">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Transaction #</th>
                                <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-white lg:table-cell">Client</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Consumed Cu M</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Amount Paid</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="transaction in transactions">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ transaction.id }}</td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">
                                    <a :href="route('client.profile', transaction.client.id)" class="font-bold text-primary-blue hover:opacity-70 cursor-pointer">{{ transaction.client.first_name+' '+transaction.client.last_name }}</a>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">{{ transaction.client.account.current_reading - transaction.client.account.prev_reading }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">₱ {{ Number(transaction.amount).toLocaleString() }}.00</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">{{ transaction.date_paid }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
