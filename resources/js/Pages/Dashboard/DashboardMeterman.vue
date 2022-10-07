<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref, watch, inject } from 'vue'
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import VTailwindModal from '@/Modals/VTailwindModal.vue';

    const scanned = ref('')
    const client = ref({})
    const hasScanned = ref(false)
    const show = ref(false)

    const swal = inject('$swal')

    const form = useForm({
        reading: '',
        client_id: ''
    });

    watch(scanned, async (newScanned, oldScanned) => {
        getData()
    })

    function onDecode(data) {
        scanned.value = data
    }

    function getData() {
        axios.get(`/client-data/${scanned.value}`)
        .then(response => {
            client.value = response.data
            hasScanned.value = true
            form.client_id = response.data.id
        })
    }

    function showModal() {
        form.reading = 0
        show.value = true
    }

    function cancel(close) {
        show.value = false
    }

    const submit = () => {
        form.post(route('reading.create'), {
            onSuccess: () => submitSuccess()
        });
    }

    function submitSuccess() {
        show.value = false
        swal.fire({
            icon: 'success',
            title: 'Reading Saved!',
            text: '',
            confirmButtonColor: '#23408E'
        })
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
                <div>
                    <v-tailwind-modal v-model="show" @cancel="cancel()">
                        <div>
                            <form @submit.prevent="submit">
                                <div class="mt-4">
                                    <InputLabel for="reading" value="Reading" />
                                    <TextInput id="reading" type="text" class="mt-1 block w-full" v-model="form.reading" required autofocus autocomplete="reading" />
                                    <InputError class="mt-2" :message="form.errors.reading" />
                                </div>
                                <div class="flex items-center justify-end mt-4">
                                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Save
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </v-tailwind-modal>
                </div>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="relative bg-white">
                            <div class="absolute inset-0">
                                <div class="absolute inset-y-0 left-0 w-1/2 bg-gray-50"></div>
                            </div>
                            <div class="relative mx-auto max-w-7xl lg:grid lg:grid-cols-5">
                                <div class="bg-gray-50 py-16 px-4 sm:px-6 lg:col-span-2 lg:px-8 lg:py-24 xl:pr-12">
                                    <div class="mx-auto max-w-lg">
                                        <qr-stream @decode="onDecode" class="mb">
                                            <div style="color: red;" class="frame"></div>
                                        </qr-stream>
                                    </div>
                                </div>
                                <div v-if="hasScanned" class="bg-white py-4 px-4 sm:px-6 lg:col-span-3 lg:py-24 lg:px-8 xl:pl-12">
                                    <div class="text-center">
                                        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Client Information</h2>
                                    </div>
                                    <div class="mt-12">
                                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                                            <div>
                                                <label for="first-name" class="block text-sm font-medium text-gray-700">First name</label>
                                                <div class="mt-1">
                                                    <input v-model="client.first_name" disabled autocomplete="given-name" class="block w-full rounded-md border-gray-300 py-3 px-4 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div>
                                                <label for="last-name" class="block text-sm font-medium text-gray-700">Last name</label>
                                                <div class="mt-1">
                                                    <input v-model="client.first_name" disabled autocomplete="family-name" class="block w-full rounded-md border-gray-300 py-3 px-4 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label for="company" class="block text-sm font-medium text-gray-700">Address</label>
                                                <div class="mt-1">
                                                    <input v-model="client.address" disabled autocomplete="organization" class="block w-full rounded-md border-gray-300 py-3 px-4 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label for="email" class="block text-sm font-medium text-gray-700">Contact Number</label>
                                                <div class="mt-1">
                                                    <input v-model="client.contact_no" disabled autocomplete="email" class="block w-full rounded-md border-gray-300 py-3 px-4 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <button @click="showModal()" class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Add new reading</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="p-6 bg-white border-b border-gray-200">
                            
                            {{ scanned }}
                        </div> -->
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
    