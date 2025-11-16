<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
// import Modal from '@/Components/Modal.vue';
// import Modal2 from '@/Components/Modal2.vue';
import { FwbButton, FwbModal } from 'flowbite-vue'

const uPage = usePage()
const invoiceData = ref([]);
var page = ref(1);
var selectFilter = ref(1);
const searchTerm = ref('');
const isLoading = ref(false);
const selectedInvoices = ref([]);
const selectAll = ref(false);
var dataFetch = ref(false);
var hidePaid = ref(false);
var isShowModal = ref(false);
var dataLength = ref(0);


var sort_by = ref('');
var sort_dir = ref('asc');

const toggleSort = function(col:string){

    sort_by.value = col;
    sort_dir.value = sort_dir.value == 'asc'?'desc':'asc';
}

function closeModal () {
    isShowModal.value = false;
    console.log(isShowModal);
}

function showModal (item) {
    isShowModal.value = true
    console.log(item.invoice_id);
}

const fetchData = async () => {

  isLoading.value = true; // Show loading indicator before request
  try {
    const response = await axios.get('/xero/invoice/data?page='+page.value+'&search_term='+searchTerm.value+'&filter_term='+selectFilter.value+'&sort_by='+sort_by.value+'&sort_dir='+sort_dir.value);
    invoiceData.value = response.data;
    dataLength.value = response.data.length;
    console.log(dataLength.value);

  } catch (error) {
    console.error('Error:', error);
    // Handle errors gracefully
  } finally {
    isLoading.value = false; // Hide loading indicator after request
  }
};
const checkAll = function() {
    invoiceData.value.forEach((item) => ( item.checked = selectAll.value ));
};

const hideSelected = function(){
    let selectedItemIds = invoiceData.value.filter((item) => item.checked);
    for(let i in selectedItemIds)
    {
        selectedItemIds[i].isActiveRow = !selectedItemIds[i].isActiveRow;
    }

    invoiceData.value.forEach((item) => ( item.checked = false ));
    // console.log(selectedItemIds);
}

const submitSelected = function() {
    const selectedItemIds = invoiceData.value.filter((item) => item.checked);
    dataFetch.value = false;
    axios.post('/collection_invoice',
        selectedItemIds,
    )
    .then((response) => {
        dataFetch.value = true;
        console.log("dataFetch => ", dataFetch);
        console.log('Selected items submitted:', response.data);
    })
    .catch((error) => {
        console.error('Error submitting data:', error);
    });
};
onMounted(() => {
  // component is now mounted.
  fetchData();
});

watch([page, selectFilter, sort_dir], fetchData);
</script>

<template>
    <Head title="Invoices" />

    <!-- <Modal :show="isModal" :maxWidth="isModalWidth" :closeable="isModalCloseable" /> -->

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight font-bold">Xero Invoices - Awaiting Collection</h2>
        </template>

        <fwb-modal v-if="isShowModal" @close="closeModal">
      <template #header>
        <div class="flex items-center text-lg">
            Sign in to our platform
        </div>
      </template>
      <template #body>
        <div class="flex justify-between">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->

                    <!-- Modal body -->
                        <form class="space-y-4" action="#">
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                                <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="name@company.com" required />
                            </div>
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your password</label>
                                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required />
                            </div>
                            <div class="flex justify-between">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" required />
                                    </div>
                                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Remember me</label>
                                </div>
                                <a href="#" class="text-sm text-blue-700 hover:underline dark:text-blue-500">Lost Password?</a>
                            </div>
                            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login to your account</button>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                                Not registered? <a href="#" class="text-blue-700 hover:underline dark:text-blue-500">Create account</a>
                            </div>
                        </form>


            </div>
        </div>

      </template>
      <template #footer>

      </template>
    </fwb-modal>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- <div class="p-6 text-gray-900 dark:text-gray-100">Invoices</div> -->

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div v-if="dataFetch" class="p-4 m-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                            <span class="font-medium">Success!</span> Data saved successfully.
                        </div>

                        <div class="flex items-center justify-between flex-column md:flex-row flex-wrap space-y-4 md:space-y-0 py-4 bg-white dark:bg-gray-900 p-5">

                                <div class="flex flex-col items-center justify-center">
                                        <Link class="ml-3" :href="route('xero.invoices')" method="get"
                                            as="button" >
                                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Refresh to Xero</button>
                                        </Link>
                                    </div>
                                <div>

                                <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                                    <span class="sr-only">Action button</span>
                                    Add From Xero
                                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <!-- Dropdown menu -->
                                <div id="dropdownAction" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownActionButton">
                                        <li>
                                            <button @click="submitSelected" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Add in Collections</button>
                                        </li>
                                        <li>
                                            <button @click="hideSelected" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Hide Collections</button>
                                        </li>
                                    </ul>
                                    <!-- <div class="py-1">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete User</a>
                                    </div> -->
                                </div>
                            </div>

                            <select v-model="selectFilter" class="mt-1 block" >
                                <option value="1">Over Due Invoices</option>
                                <option value="2">Incoming Due Invoices</option>
                                <option value="3">All Due Invoices</option>
                            </select>

                            <div class="flex items-center mb-4">
                                <input id="default-checkbox" type="checkbox" v-model="hidePaid" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="default-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Show Amount Paid</label>
                            </div>

                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input v-model="searchTerm" v-on:blur="fetchData()" type="text" id="table-search-users" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for Invoice">
                            </div>
                        </div>


                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4">
                                        <div class="flex items-center">
                                            <input v-model="selectAll" @change="checkAll()" id="checkbox-all-search" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Name
                                        <button v-on:click="toggleSort('Contact.Name')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Invoice Number
                                        <button v-on:click="toggleSort('InvoiceNumber')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>

                                    <th scope="col" class="px-6 py-3">
                                        Reference
                                        <button v-on:click="toggleSort('Reference')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Amount Due
                                        <button v-on:click="toggleSort('AmountDue')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th v-if="hidePaid" scope="col" class="px-6 py-3">
                                        Amount Paid
                                        <button v-on:click="toggleSort('AmountPaid')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Invoice Date
                                        <button v-on:click="toggleSort('Date')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Due Date
                                        <button v-on:click="toggleSort('DueDate')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>

                                    <th scope="col" class="px-6 py-3">
                                        Days over Due
                                        <button v-on:click="toggleSort('DueDate')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>

                                    <!-- <th scope="col" class="px-6 py-3">
                                        Action
                                    </th> -->

                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td colspan="9" class="w-4 p-4"><div class="flex items-center">Loading...</div></td>
                                </tr>
                                <tr v-else-if="!invoiceData || invoiceData.length < 1" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td colspan="9" class="w-4 p-4"><div class="flex items-center">Not found.</div></td>
                                </tr>
                                <tr v-for="(item, index) in invoiceData" :key="index" :class="{hidden: item.isActiveRow}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input v-model="item.checked" id="checkbox-table-search-1" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                        </div>
                                    </td>
                                    <td scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ item.contact_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <!-- <Link  target="_blank" href="{{route('contact')}}"> -->
                                            {{ item.invoice_number }}
                                        <!-- </Link> -->
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ item.reference }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ item.amount_due }}
                                    </td>
                                    <td v-if="hidePaid" class="px-6 py-4">
                                        {{ item.amount_paid }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ item.date }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ item.due_date }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ item.days_over_due }}
                                    </td>
<!--
                                    <td class="px-6 py-4 flex">

                                        <button v-on:click="showModal(item)" >
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                            </svg>
                                        </button>
                                    </td> -->

                                    <!-- <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> {{ item.status }}
                                        </div>
                                    </td> -->
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="dataLength > 99" class="flex m-6">
                        <!-- Previous Button -->
                        <button @click="page--" href="#" class="flex items-center justify-center px-4 h-10 me-3 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                            </svg>
                            Previous
                        </button>
                        <button @click="page++" href="#" class="flex items-center justify-center px-4 h-10 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            Next
                            <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
