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
    import { watchDebounced } from '@vueuse/core'

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
    const search = ref('')

    watch(role, async (newRole, oldRole) => {
        getStaffs()
    })
    
    watchDebounced(search, () => {
        getStaffs()
    }, {debounce: 500})

    const form = useForm({
        id: '',
        name: '',
        username: '',
        role: ''
    });

    function showModal(type, staff) {
        if(type == 'create') {
            isEdit.value = false
            form.id = ''
            form.name = ''
            form.username = ''
            form.role = '' 
        } else {
            isEdit.value = true
            form.id = staff.id
            form.name = staff.name
            form.username = staff.username
            form.role = staff.role.replace(/^./, staff.role[0].toUpperCase())
        }
        
        show.value = true
    }

    function cancel(close) {
        show.value = false
    }

    function submitSuccess() {
        show.value = false
        store.dispatch('staffs/getStaffs')
        swal.fire({
            icon: 'success',
            title: isEdit.value ? 'Staff Updated!' : 'Staff Saved!',
            text: isEdit.value ? '' : `The user password is default "PW-Staff"`,
            confirmButtonColor: '#23408E'
        })
    }

    function deleteSuccess() {
        store.dispatch('staffs/getStaffs')
        swal.fire({
            title: 'Deleted!',
            text: 'Staff has been deleted.',
            icon: 'success',
            confirmButtonColor: '#23408E'
        })
    }

    function deleteStaff(id) {
        swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#23408E',
            cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Inertia.delete(route("staff.delete", id), {
                    onSuccess: () => deleteSuccess()
                })
            }
        })
    }

    function getStaffs(page) {
        store.dispatch('staffs/getStaffs', {
            params: {
                page: page,
                role: role.value,
                search: search.value
            }
        })
    }

    const submit = (id) => {
        if(isEdit.value) {
            form.put(`/staff/${form.id}`, {
                onSuccess: () => submitSuccess()
            });
        } else {
            form.post(route('staff.create'), {
                onSuccess: () => submitSuccess()
            });
        }
    };

    onMounted(() => store.dispatch('staffs/getStaffs'))
</script>
    
<template>
    <Head title="Staff"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Staff
            </h2>
            <p class="mt-2 text-sm text-white">A list of all the staffs including their name, email and role.</p>
        </template>

        <div class="py-12">
            <div>
                <v-tailwind-modal v-model="show" @cancel="cancel()">
                    <template v-slot:title>{{isEdit ? 'Update Staff' : 'Create Staff'}}</template>
                    <div>
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="username" value="Username" />
                                <TextInput id="username" type="text" class="mt-1 block w-full" v-model="form.username" required autocomplete="username" />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="role" value="Role" />
                                <select v-model="form.role" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="Meterman">Meterman</option>
                                    <option value="Cashier">Cashier</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{isEdit ? 'Update Staff' : 'Save Staff'}}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </v-tailwind-modal>
            </div>
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center w-full">
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="search" value="Search" />
                        <TextInput id="search" type="text" class="mt-1 block w-full" v-model="search"/>
                    </div>
                    <div class="mr-4">
                        <InputLabel class="font-bold" for="role" value="Role" />
                        <select v-model="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">All Staff</option>
                            <option value="Meterman">Meterman</option>
                            <option value="Cashier">Cashier</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.role" />
                    </div>
                    <div class="ml-auto">
                        <button  @click="showModal('create', null)" type="button" class="mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-75 focus:outline-none focus:ring-2 focus:opacity-75 focus:ring-offset-2 sm:w-auto">Add Staff</button>
                    </div>
                </div>
                <div class="-mx-4 mt-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:-mx-6 md:mx-0 md:rounded-lg bg-white">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-primary-blue">
                            <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white font-bold sm:pl-6">Name</th>
                            <th scope="col" class="hidden px-3 py-3.5 text-left text-sm font-semibold text-white font-bold lg:table-cell">Username</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white font-bold">Role</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="staff in staffs.data">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ staff.name }}</td>
                                <td class="hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell">{{ staff.username }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">{{ staff.role }}</td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a @click="deleteStaff(staff.id)" href="#" class="text-red-400 hover:opacity-75 mr-2">Delete<span class="sr-only">, Lindsay Walton</span></a>
                                    <a @click="showModal('update', staff)" href="#" class="text-primary-blue hover:opacity-75">Edit<span class="sr-only">, Lindsay Walton</span></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                    <pagination class="mt-6" 
                            :pagination="pagination"
                            @paginate="getStaffs"
                            :offset="4" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
    