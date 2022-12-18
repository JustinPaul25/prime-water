<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, usePage } from '@inertiajs/inertia-vue3';
    import RecieptModal from '@/Modals/RecieptModal.vue';
    import { ref, onMounted } from 'vue'
    import moment from 'moment';

    const transactions = ref([])
    const month = ref(["January","February","March","April","May","June","July","August","September","October","November","December"])
    const showBill = ref(false)
    const props = ref(usePage().props.value.auth.user);

    const getTransaction = () => {
        axios.get(`/client-transactions/${props.value.id}`)
        .then(response => {
            transactions.value = response.data
        });
    }

    onMounted(() => getTransaction())

    const printBill = () => {
        showBill.value = true
    }

    function cancel(close) {
        showBill.value = false
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
</script>

    <template>
        <Head title="Dashboard" />

        <AuthenticatedLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Dashboard
                </h2>
            </template>
            <div class="py-12">
                <reciept-modal v-model="showBill" @cancel="cancel()">
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
                                <p class="font-bold mx-auto">{{props.first_name}} {{props.last_name}}</p>
                            </div>
                            <div class="flex text-xs">
                                <p>Address: </p>
                                <p class="mx-auto">{{props.address}}</p>
                            </div>
                            <div class="flex text-xs">
                                <p>Previous Reading: </p>
                                <p class="mx-auto">{{props.account.prev_reading}}</p>
                            </div>
                            <div class="flex text-xs">
                                <p>Current Reading: </p>
                                <p class="mx-auto">{{props.account.current_reading}}</p>
                            </div>
                            <div class="flex text-xs">
                                <p class="font-bold">Consumed Cu. M: </p>
                                <p class="mx-auto font-bold">{{props.account.current_reading-props.account.prev_reading}}</p>
                            </div>

                            <div class="flex text-xs mt-4">
                                <p>Previous Balance - {{getPrevMonth()}} {{getCurrentYear()}}: </p>
                                <p class="mx-auto">₱ {{Number(props.account.prev_balance).toLocaleString()}}.00</p>
                            </div>
                            <div class="flex text-xs">
                                <p>Current Bill - {{getMonth()}} {{getCurrentYear()}}: </p>
                                <p class="mx-auto">₱ {{Number(props.account.current_charges).toLocaleString()}}.00</p>
                            </div>
                            <div class="flex text-sm font-bold mt-2">
                                <p>Total Bill: </p>
                                <p class="ml-auto">₱ {{Number(props.account.current_charges).toLocaleString()}}.00</p>
                            </div>

                            <div v-if="transactions.length" class="flex text-xs mt-8">
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
                <div class="mx-auto max-w-3xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
                    <div class="flex items-center space-x-5">
                        <div class="flex-shrink-0">
                        <div class="relative">
                            <img class="h-16 w-16 rounded-full" src="https://images.unsplash.com/photo-1463453091185-61582044d556?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80" alt="">
                            <span class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></span>
                        </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $page.props.auth.user.name }}</h1>
                            <p class="text-sm font-medium text-gray-500">Address: <span class="text-gray-900">{{ $page.props.auth.user.address }}</span></p>
                            <p class="text-sm font-medium text-gray-500">Contact No.: <span class="text-gray-900">{{ $page.props.auth.user.contact_no }}</span></p>
                        </div>
                    </div>
                    <div class="justify-stretch mt-6 flex flex-col-reverse space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
                        <button disabled type="button" class="inline-flex items-center justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-100" :class="$page.props.auth.user.status === 0 ? 'text-red-600 bg-red-200' : 'text-green-600 bg-green-200'">
                            {{ $page.props.auth.user.status === 0 ? 'In-Active' : 'Active' }}
                        </button>
                    </div>
                </div>

                <div class="mx-auto mt-8 grid max-w-3xl grid-cols-1 gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
                    <section aria-labelledby="timeline-title" class="lg:col-span-1 lg:col-start-1">
                        <div class="bg-white px-4 py-5 shadow sm:rounded-lg sm:px-6">
                            <h2 id="timeline-title" class="text-xl font-semibold text-gray-900">Water Bill</h2>

                            <div>
                                <dl class="mt-2 divide-y divide-gray-200 border-t border-b border-gray-200">
                                    <div class="flex justify-between py-3 text-sm font-medium">
                                        <dt class="text-gray-500">Previous Reading</dt>
                                        <dd class="whitespace-nowrap text-gray-900">{{ $page.props.auth.user.account.prev_reading }}</dd>
                                    </div>

                                    <div class="flex justify-between py-3 text-sm font-medium">
                                        <dt class="text-gray-500">Current Reading</dt>
                                        <dd class="whitespace-nowrap text-gray-900">{{ $page.props.auth.user.account.current_reading }}</dd>
                                    </div>

                                    <div class="flex justify-between py-3 text-sm font-medium">
                                        <dt class="text-gray-500">Consumed Cu. M</dt>
                                        <dd class="whitespace-nowrap text-gray-900">{{ $page.props.auth.user.account.current_reading - $page.props.auth.user.account.prev_reading }}</dd>
                                    </div>

                                    <div class="flex justify-between py-3 text-sm font-medium">
                                        <dt class="text-gray-500">Previous Balance</dt>
                                        <dd class="whitespace-nowrap text-gray-900 font-bold">P{{ $page.props.auth.user.account.prev_balance }}</dd>
                                    </div>

                                    <div class="flex justify-between py-3 text-sm font-medium">
                                        <dt class="text-gray-500">Current Bill</dt>
                                        <dd class="whitespace-nowrap text-gray-900 font-bold">P{{ $page.props.auth.user.account.current_reading }}</dd>
                                    </div>

                                    <div class="flex justify-between py-3 text-sm font-medium">
                                        <dt class="text-gray-500">Total Bill</dt>
                                        <dd class="whitespace-nowrap text-gray-900 font-bold text-xl">P{{ parseInt($page.props.auth.user.account.current_charges) + parseInt($page.props.auth.user.account.prev_balance) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="justify-stretch mt-6 flex flex-col">
                                <p class="text-xs text-gray-500 mb-2">Note: Due date is 3 months before the last payment</p>
                                <button disabled type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Due Date: {{$page.props.auth.user.account.due_date}}</button>
                            </div>
                        </div>
                    </section>
                    <div class="space-y-6 lg:col-span-2 lg:col-start-2 bg-white shadow sm:rounded-lg">
                        <section aria-labelledby="client-transacion-report">
                            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                                <div class="sm:flex sm:items-center">
                                    <div class="sm:flex-auto">
                                        <h1 class="text-xl font-semibold text-gray-900">Payment Transactions</h1>
                                    </div>
                                </div>
                                <div class="-mx-4 mt-10 ring-1 ring-gray-300 sm:-mx-6 md:mx-0 md:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Transaction No.</th>
                                                <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">Amount</th>
                                                <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="transaction in transactions">
                                                <td class="relative py-4 pl-4 sm:pl-6 pr-3 text-sm">
                                                    <div class="font-medium text-gray-900">{{ transaction.id }}</div>
                                                    <div class="mt-1 flex flex-col text-gray-500 sm:block lg:hidden">
                                                    <span>₱ {{ transaction.amount }} / {{ transaction.created_at }}</span>
                                                    </div>
                                                </td>
                                                <td class="hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell">₱ {{ Number(transaction.amount).toLocaleString() }}</td>
                                                <td class="hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell">{{ transaction.created_at }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
