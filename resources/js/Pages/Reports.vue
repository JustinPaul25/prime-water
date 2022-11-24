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
    import _ from 'lodash'

    defineProps({
        status: String,
    });

    const store = useStore()
    const swal = inject('$swal')
    const staffs = computed(() => store.state.staffs.staffs)
    const pagination = computed(() => store.state.staffs.pagination)

    let show = ref(false)
    let isEdit = ref(false)
    const role = ref('')
    const searchYear = ref('2022')
    const searchDate = ref('')
    const searchFrom = ref('')
    const searchTo = ref('')
    const searchBy = ref('')
    const sortBy = ref('')
    const transactions = ref([])

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

    onMounted(() => getTransactions())
</script>
    
<template>
    <Head title="Staff"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Reports
            </h2>
        </template>

        <div class="py-12">
            <div class="px-4 sm:px-6 lg:px-8">
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
                        <button type="button" class="mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Print Report</button>
                    </div>
                </div>
                <div class="-mx-4 mt-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:-mx-6 md:mx-0 md:rounded-lg bg-white">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-primary-blue">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Transaction #</th>
                                <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-white lg:table-cell">Client</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Amount Paid</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="transaction in transactions">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ transaction.id }}</td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">{{ transaction.client?.name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">₱ {{ transaction.amount }}.00</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">{{ transaction.date_paid }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
    