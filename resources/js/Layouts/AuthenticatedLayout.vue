<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LayoutFooter from '@/Components/LayoutFooter.vue';
import NavLink from '@/Components/NavLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import TextInput from '@/Components/TextInput.vue';
import VTailwindModal from '@/Modals/VTailwindModal.vue';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import { watchDebounced } from '@vueuse/core';
import axios from 'axios';
import { inject, ref } from 'vue';

const showingNavigationDropdown = ref(false);
const show = ref(false)
const showList = ref(false)
const showSettings = ref(false)
const fullName = ref(usePage().props.value.auth.user.name)
const swal = inject('$swal')

const form = useForm({
    amount: '',
    username: usePage().props.value.auth.user.username,
    first_name: usePage().props.value.auth.user.first_name,
    middle_name: usePage().props.value.auth.user.middle_name,
    last_name: usePage().props.value.auth.user.last_name,
    contact_no: usePage().props.value.auth.user.contact_no
});

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

function showPriceModal() {
    form.amount = 0
    show.value = true
}

function showPriceListModal() {
    showList.value = true
}

function showSettingsModal() {
    showSettings.value = true
}

function cancelSettings(close) {
    showSettings.value = false
}

function cancel(close) {
    show.value = false
}

function cancelList(close) {
    showList.value = false
}

const submit = () => {
    form.put(`/utilities-price/1`, {
        onSuccess: () => submitSuccess()
    });
}

function submitSuccess() {
    show.value = false
    swal.fire({
        icon: 'success',
        title: 'Price Changed Successfully',
        text: '',
        confirmButtonColor: '#23408E'
    })
}

const updateUser = () => {
    axios.post('/update-user', form)
    .then(response => {
        fullName.value = response.data
        cancelSettings()
        swal.fire({
            icon: 'success',
            title: 'Updated Successfully',
            text: '',
            confirmButtonColor: '#23408E'
        })
    })
    .catch(response => {
        console.log(response)
    })
}
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <div>
                <v-tailwind-modal v-model="showSettings" @cancel="cancelSettings()">
                    <div>
                        <div class="mt-10 px-4 sm:mt-16 sm:px-0 lg:mt-0">
                            <h1 class="text-3xl font-bold tracking-tight text-gray-900">User Settings</h1>

                            <div>
                                <div class="mt-4">
                                    <InputLabel for="first_name" value="First Name" />
                                    <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required autofocus autocomplete="first_name" />
                                </div>

                                <div class="mt-4">
                                    <InputLabel for="middle_name" value="Middle Name" />
                                    <TextInput id="middle_name" type="text" class="mt-1 block w-full" v-model="form.middle_name" autofocus autocomplete="middle_name" />
                                </div>

                                <div class="mt-4">
                                    <InputLabel for="last_name" value="Last Name" />
                                    <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" required autofocus autocomplete="last_name" />
                                </div>

                                <div class="mt-4">
                                    <InputLabel for="contact_no" value="Contact Number" />
                                    <TextInput maxlength="11" id="contact_no" type="text" class="mt-1 block w-full" v-model="form.contact_no" required autofocus autocomplete="contact_no" />
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button @click="updateUser()" class="inline-flex items-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Update</button>
                            </div>
                        </div>
                    </div>
                </v-tailwind-modal>
                <v-tailwind-modal v-model="show" @cancel="cancel()">
                    <div>
                        <div class="mt-10 px-4 sm:mt-16 sm:px-0 lg:mt-0">
                            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Current sdPrice/Cu m</h1>

                            <div class="mt-3">
                                <h2 class="sr-only">Product information</h2>
                                <p class="text-3xl tracking-tight text-gray-900">P {{ $page.props.auth.price.value }}</p>
                            </div>
                        </div>
                        <form @submit.prevent="submit">
                            <div class="mt-4">
                                <InputLabel for="amount" value="New Price" />
                                <TextInput id="amount" type="text" class="mt-1 block w-full" v-model="form.amount" placeholder="Input Amount" required autofocus autocomplete="amount" />
                                <InputError class="mt-2" :message="form.errors.amount" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <p  @click="showPriceListModal()" class="mr-auto text-lg font-bold hover:opacity-75 cursor-pointer">Show Price</p>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </v-tailwind-modal>
                <v-tailwind-modal v-model="showList" @cancel="cancelList()">
                    <div>
                        <div class="mt-10 px-4 sm:mt-16 sm:px-0 lg:mt-0">
                            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Price History</h1>

                            <div class="mt-3">
                                <table class="table-auto w-full">
                                    <thead>
                                        <tr>
                                        <th>Price</th>
                                        <th>Date Changes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="price in $page.props.auth.price_list">
                                            <td>₱ {{ price.value }}/CuM</td>
                                            <td class="text-center">{{ price.date_created }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </v-tailwind-modal>
            </div>
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <div class="flex items-end">
                                        <ApplicationLogo class="block h-9 w-auto" />
                                        <div>
                                            <span class="text-secondary-blue font-bold text-xl">WBS</span>
                                        </div>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('dashboard')" v-if="!$page.props.auth.cashier" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin" :href="route('staff')" :active="route().current('staff')">
                                    Admin/Staff
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin" :href="route('client')" :active="route().current('client')">
                                    Clients
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin || $page.props.auth.cashier" :href="route('payments')" :active="route().current('payments')">
                                    Payments
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin" :href="route('reports')" :active="route().current('reports')">
                                    Income Report
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin" :href="route('usage')" :active="route().current('usage')">
                                    Usage Report
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ fullName }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('change.password.form')">
                                            Change Password
                                        </DropdownLink>
                                        <button v-if="$page.props.auth.admin" @click="showPriceModal()" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                            Price/Cu M
                                        </button>
                                        <button v-if="$page.props.auth.admin" @click="showSettingsModal()" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                            User Settings
                                        </button>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin" :href="route('staff')" :active="route().current('staff')">
                            Admin/Staff
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin" :href="route('client')" :active="route().current('client')">
                            Clients
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin || $page.props.auth.cashier" :href="route('payments')" :active="route().current('payments')">
                            Payments
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin" :href="route('reports')" :active="route().current('reports')">
                            Income Report
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin" :href="route('usage')" :active="route().current('usage')">
                            Usage Report
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">{{ fullName }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('change.password.form')" class="w-full">
                                Change Password
                            </ResponsiveNavLink>
                            <button v-if="$page.props.auth.admin" @click="showPriceModal()" class="w-full block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                Price/Cu M
                            </button>
                            <button v-if="$page.props.auth.admin" @click="showSettingsModal()" class="w-full block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                User Settings
                            </button>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="w-full">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-primary-blue shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>

            <layout-footer></layout-footer>
        </div>
    </div>
</template>
