<script setup>
import { ref, inject } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LayoutFooter from '@/Components/LayoutFooter.vue'
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import VTailwindModal from '@/Modals/VTailwindModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const showingNavigationDropdown = ref(false);
const show = ref(false)
const swal = inject('$swal')

const form = useForm({
    amount: ''
});

function showPriceModal() {
    form.amount = 0
    show.value = true
}

function cancel(close) {
    show.value = false
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
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <div>
                <v-tailwind-modal v-model="show" @cancel="cancel()">
                    <div>
                        <div class="mt-10 px-4 sm:mt-16 sm:px-0 lg:mt-0">
                            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Current Price/Cu m</h1>

                            <div class="mt-3">
                                <h2 class="sr-only">Product information</h2>
                                <p class="text-3xl tracking-tight text-gray-900">P {{ $page.props.auth.price.value }}</p>
                            </div>
                        </div>
                        <form @submit.prevent="submit">
                            <div class="mt-4">
                                <InputLabel for="amount" value="New Price" />
                                <TextInput id="amount" type="text" class="mt-1 block w-full" v-model="form.amount" required autofocus autocomplete="amount" />
                                <InputError class="mt-2" :message="form.errors.amount" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update
                                </PrimaryButton>
                            </div>
                        </form>
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
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin" :href="route('staff')" :active="route().current('staff')">
                                    Staff
                                </NavLink>
                                <NavLink v-if="$page.props.auth.cashier" :href="route('staff')" :active="route().current('staff')">
                                    Payments
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin" :href="route('client')" :active="route().current('client')">
                                    Clients
                                </NavLink>
                                <NavLink v-if="$page.props.auth.admin" :href="route('dashboard')" :active="route().current('dashboard')">
                                    Reports
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
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <button v-if="$page.props.auth.admin" @click="showPriceModal()" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                            Price/Cu M
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
                        <ResponsiveNavLink v-if="$page.props.auth.cashier" :href="route('staff')" :active="route().current('staff')">
                            Paymentsasdas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin" :href="route('staff')" :active="route().current('staff')">
                            Staff
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin" :href="route('client')" :active="route().current('client')">
                            Clients
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.admin" :href="route('dashboard')" :active="route().current('dashboard')">
                            Reports
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">{{ $page.props.auth.user.name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <button v-if="$page.props.auth.admin" @click="showPriceModal()" class="w-full block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                Price/Cu M
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
