<script>
    export default {
      name: 'VTailwindModal',
      inheritAttrs: false
    }
</script>

<template>
    <vue-final-modal
      v-slot="{ params, close }"
      v-bind="$attrs"
      classes="flex justify-center items-center"
      content-class="relative flex flex-col w-3/4 md:w-2/4 lg:w-2/4 xl:w-2/4 max-h-full mx-4 p-4 border dark:border-gray-800 rounded bg-white dark:bg-gray-900"
    >
      <span class="mr-8 text-2xl font-bold ">
        <slot name="title"></slot>
      </span>
      <div class="flex-grow overflow-y-auto max-h-screen">
        <slot v-bind:params="params"></slot>
      </div>
        <button class="absolute top-0 right-0 mt-2 mr-2" @click="$emit('cancel', close)">
          <svg class="text-red-400 w-6 h-6 hover:opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </button>
    </vue-final-modal>
</template>