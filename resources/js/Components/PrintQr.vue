<script setup>
import VTailwindModal from '@/Modals/VTailwindModal.vue'
import { ref } from 'vue'

let show = ref(false)

function cancel(close) {
    show.value = false
}

function showModal() {
    show.value = true
}

function print() {
    this.$htmlToPaper("printMe");
}
</script>

<template>
    <v-tailwind-modal v-model="show" @cancel="cancel()">
        <template v-slot:title>Print QR Code</template>
        <button @click="print" class="mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">print</button>
        <div id="printMe">
            asdasdasdsa
        </div>
    </v-tailwind-modal>
    <button  @click="showModal()" type="button" class="mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Print All Qr</button>
</template>