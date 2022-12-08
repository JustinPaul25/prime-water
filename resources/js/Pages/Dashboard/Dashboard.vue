<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IncomeChart from '@/Components/IncomeChart.vue';
import UsageChart from '@/Components/UsageChart.vue';
import { Head } from '@inertiajs/inertia-vue3';
import regression from 'regression'
import { ref, onMounted } from 'vue'

const props = defineProps({
    income: Object,
    reading: Object
});

const datas = ref([])

function getResult() {
    return regression.linear(datas.value)
}

function populateData() {
    

    props.income.forEach(elem => {
        props.reading.forEach(read => {
            if(elem.month === read.month && elem.year === read.year) {
                const toPush = [read.readings , elem.amount]
                datas.value.push(toPush)
            }
        })
    });
}

onMounted(() => populateData())
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
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">1968</dd>
                                </div>
                                <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">No. of Puroks</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">5</dd>
                                </div>
                                <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">Households</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">463</dd>
                                </div>
                                <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">Availed Households</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">272</dd>
                                </div>
                                <div class="flex flex-col border-t border-gray-100 p-6 text-center sm:border-0 sm:border-l">
                                    <dt class="order-2 mt-2 text-sm font-medium leading-6 text-gray-500">In-Active Households</dt>
                                    <dd class="order-1 text-4xl font-bold tracking-tight text-primary-blue">191</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white">
                <div class="grid overflow-hidden w-full px-8 lg:px-40">
                    <div class="box row-span-2 p-5">
                        <p class="order-1 text-4xl font-bold tracking-tight text-primary-blue">Forecast Chart</p>
                        <income-chart class="h-60" v-if="datas.length != 0" :raw-data="datas" :result="getResult()"></income-chart>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
