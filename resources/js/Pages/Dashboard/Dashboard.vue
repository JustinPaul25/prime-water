<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IncomeChart from '@/Components/IncomeChart.vue';
import UsageChart from '@/Components/UsageChart.vue';
import { Head } from '@inertiajs/inertia-vue3';
import regression from 'regression'

function getResult() {
    return regression.linear([
        [10, 2.1],
        [23, 2.4],
        [38, 2.6],
        [46, 2.8],
        [59, 3]
    ])
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
            <div class="mt-10 bg-white pb-12 sm:pb-16">
                <div class="relative">
                    <div class="absolute inset-0 h-1/2 bg-gray-100"></div>
                    <div class="relative mx-auto max-w-7xl px-2 sm:px-4 lg:px-6">
                        <div class="mx-auto max-w-4xl">
                            <dl class="rounded-lg bg-white shadow-lg sm:grid sm:grid-cols-5">
                                <div class="flex flex-col border-b border-gray-100 p-6 text-center sm:border-0 sm:border-r">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">Barangay Population</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">1509</dd>
                                </div>
                                <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">Screened Individuals</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">1509</dd>
                                </div>
                                <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">Number of Households</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">272</dd>
                                </div>
                                <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">Availed Household</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">272</dd>
                                </div>
                                <div class="flex flex-col border-t border-gray-100 p-6 text-center sm:border-0 sm:border-l">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">Adjusted Individuals</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">50</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white">
                <div class="grid overflow-hidden sm:grid sm:grid-cols-2 gap-1">
                    <div class="box row-span-2 p-5">
                        <p class="order-1 text-4xl font-bold tracking-tight text-primary-blue">2022 Income Chart</p>
                        <income-chart :raw-data="[
                    [10, 2.1],
                    [23, 2.4],
                    [38, 2.6],
                    [46, 2.8],
                    [59, 3]
                ]"
                
                :result="getResult()"></income-chart>
                    </div>
                    <div class="box row-span-2 p-5">
                        <p class="order-1 text-4xl font-bold tracking-tight text-primary-blue">2022 Consumed Water by:cu m</p>
                        <usage-chart></usage-chart>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
