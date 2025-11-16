<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

import Pagination from '@/Components/Pagination.vue';
import Modal2 from '@/Components/Modal2.vue';
import debounce from 'lodash.debounce';
import axios from 'axios';
import { FwbButton, FwbModal } from 'flowbite-vue'




let props = defineProps({
    email_template_data: Object,
    filters: Object,
    per_page: Number,
    sort_by: String,
    sort_dir: String,
    can: Object,
    legal_message_1: String,
    legal_message_2: String,
    legal_message_3: String,
    action: Boolean,
    phone_action_button_email: String,
    post_action_button_email: String,
    legal_action_button_email: String,
});

let search = ref(props.filters.search);
let per_page = ref(props.per_page);
let sort_by = ref(props.sort_by);
const invoiceData = ref([]);
let sort_dir = ref(props.sort_dir);
let selectFilter = ref(1);
var hidePaid = ref(false);
var isLoading = ref(false);
let legal_message_1 = ref(props.legal_message_1);
let legal_message_2 = ref(props.legal_message_2);
let legal_message_3 = ref(props.legal_message_3);
var isShowModal = ref(false);
var dataLength = ref(0);

function closeModal () {
    isLoading.value = false;
    isShowModal.value = false;
}

async function showModal (invoice_id :number) {
    let id = invoice_id;

    isLoading.value = true;
    isShowModal.value = true;
    try {
        const response = await axios.get('/get-invoice-history/'+id);
        invoiceData.value = response.data;
        dataLength.value = response.data.length;
        console.log(dataLength.value);

    } catch (error) {
        console.error('Error:', error);
        // Handle errors gracefully
    } finally {
        isLoading.value = false; // Hide loading indicator after request
    }
}

const toggleSort = function(col:string){
    if(sort_by.value == col){
        sort_dir.value = sort_dir.value == 'desc'?'asc':'desc';
    }else{
        sort_by.value = col;
        sort_dir.value == 'asc'
    }
}

const form = useForm({
    c_id: '',
});

watch([search, per_page, sort_by, sort_dir, selectFilter], debounce(function ( value:string ) {
    router.get('/collection_invoice', {
        search: value[0],
        per_page: value[1],
        sort_by: value[2],
        sort_dir: value[3],
        selectFilter: value[4],
    }, {
        preserveState: true,
        replace: true
    });
}, 300));

function formatDate(da: any){
    let strArray=['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    let d1 = Date.parse(da);
    let date = new Date(d1);
    let d = date.getDate();
    let m = strArray[date.getMonth()];
    let y = date.getFullYear();
    return '' + (d <= 9 ? '0' + d : d) + ' ' + m + ' ' + y;
}

function dateDifferenceInDays(da1: any) {
    let d1 = Date.parse(da1);
    let date1 = new Date(d1);
    let date2 = new Date();

    const msInADay = 1000 * 60 * 60 * 24; // milliseconds in a day
    const differenceInMs = date2.getTime() - date1.getTime(); // time difference in milliseconds
    return Math.abs(Math.floor(differenceInMs / msInADay)); // convert milliseconds to days
}

function callLegalAction(contact_id: string, legal_message_key: string, email: string){
    let conf = confirm("Are you sure?");
    if( conf ){
        // form.get(`send-legal-action/${contact_id}`);
        form.transform((data) => ({
            ...data,
            contact_id: contact_id,
            legal_message_key: legal_message_key,
            email: email,
        })).post(route('collection_invoice.post.legal.action'));
    }

}

</script>

<template>
    <Head title="Collection Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight font-bold">Collection Invoices</h2>
        </template>

        <div class="py-12">


            <!-- <Modal2 /> -->

            <!-- <fwb-button @click="showModal">
              Open modal
            </fwb-button> -->

            <fwb-modal v-if="isShowModal" @close="closeModal">
                <template #header>
                    <div class="flex items-center text-lg">
                    History
                    </div>
                </template>
                <template #body>

                    <p v-for="invoice in invoiceData" class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        {{invoice.activity_message.replace(/{template_name}/, invoice.name)}} : {{  formatDate(invoice.created_at)  }}
                    </p>

                </template>

            </fwb-modal>



            <div v-for="template in email_template_data" class="max-w-7xl m-8 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 font-bold">{{template.get_templates.name}}</div>

                    <div v-if="props.action && template.get_templates.name == 'Further Action'" class="p-4 m-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                        <span class="font-medium">Success!</span> Action taken Successfully.
                    </div>



                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4 p-5">



                            <div>
                                <select v-model="per_page" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option disabled value="">per page</option>
                                    <option value="10">10</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
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
                                <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                                </div>
                                <input v-model="search" type="text" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
                            </div>
                        </div>
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4">
                                        <div class="flex items-center">
                                            <input id="checkbox-all-search" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Contact Name
                                            <button v-on:click="toggleSort('contact_name')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Contact Email
                                            <button v-on:click="toggleSort('contact_email')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Mobile Number
                                            <button v-on:click="toggleSort('mobile_number')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Invoice Number
                                            <button v-on:click="toggleSort('invoice_number')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>

                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Amount Due
                                            <button v-on:click="toggleSort('amount_due')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th v-if="hidePaid" scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Amount Paid
                                            <button v-on:click="toggleSort('amount_paid')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Invoice Date
                                            <button v-on:click="toggleSort('invoice_date')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex items-center">
                                            Due Date
                                            <button v-on:click="toggleSort('due_date')">
                                                <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>

                                    <th scope="col" class="px-6 py-3">
                                        Days over Due
                                        <button v-on:click="toggleSort('DueDate')">
                                            <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                            </svg>
                                        </button>
                                    </th>

                                    <th v-if="template.get_templates.name == 'Further Action'" scope="col" class="px-6 py-3">
                                        Legal Action
                                    </th>

                                    <th scope="col" class="px-6 py-3">
                                        History
                                    </th>



                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td colspan="9" class="w-4 p-4"><div class="flex items-center">Loading...</div></td>
                                </tr>

                                <tr v-if="template.invoice_data.data.length<1">
                                    <td scope="row" colspan="9">
                                        No records found.
                                    </td>
                                </tr>
                                <tr v-for="ci in template.invoice_data.data" :key="ci.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input id="checkbox-table-search-1" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                        </div>
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ ci.contact_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ ci.contact_email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ ci.mobile_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ ci.invoice_number }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ ci.amount_due }}
                                    </td>
                                    <td v-if="hidePaid" class="px-6 py-4">
                                        {{ ci.amount_paid }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ formatDate(ci.invoice_date) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ formatDate(ci.due_date) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ dateDifferenceInDays(ci.due_date) }}
                                    </td>
                                    <td v-if="template.get_templates.name == 'Further Action'" class="px-6 py-4">
                                        <button @click="callLegalAction(ci.contact_id, legal_message_1, props.phone_action_button_email)" :data-tooltip-target="'tooltip-default1-'+ci.id" >
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M5 4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4Zm12 12V5H7v11h10Zm-5 1a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H12Z" clip-rule="evenodd"/>
                                            </svg>

                                        </button>
                                        <div :id="'tooltip-default1-'+ci.id" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                            {{ props.legal_message_1 }}
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                        <button @click="callLegalAction(ci.contact_id, legal_message_2, props.post_action_button_email)" :data-tooltip-target="'tooltip-default2-'+ci.id" >
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M2.038 5.61A2.01 2.01 0 0 0 2 6v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6c0-.12-.01-.238-.03-.352l-.866.65-7.89 6.032a2 2 0 0 1-2.429 0L2.884 6.288l-.846-.677Z"/>
                                                <path d="M20.677 4.117A1.996 1.996 0 0 0 20 4H4c-.225 0-.44.037-.642.105l.758.607L12 10.742 19.9 4.7l.777-.583Z"/>
                                            </svg>

                                        </button>
                                        <div :id="'tooltip-default2-'+ci.id" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                            {{ props.legal_message_2 }}
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                        <button @click="callLegalAction(ci.contact_id, legal_message_3, props.legal_action_button_email)" :data-tooltip-target="'tooltip-default3-'+ci.id" >
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M12 4a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm-2.952.462c-.483.19-.868.432-1.19.71-.363.315-.638.677-.831.93l-.106.14c-.21.268-.36.418-.574.527C6.125 6.883 5.74 7 5 7a1 1 0 0 0 0 2c.364 0 .696-.022 1-.067v.41l-1.864 4.2a1.774 1.774 0 0 0 .821 2.255c.255.133.538.202.825.202h2.436a1.786 1.786 0 0 0 1.768-1.558 1.774 1.774 0 0 0-.122-.899L8 9.343V8.028c.2-.188.36-.38.495-.553.062-.079.118-.15.168-.217.185-.24.311-.406.503-.571a1.89 1.89 0 0 1 .24-.177A3.01 3.01 0 0 0 11 7.829V20H5.5a1 1 0 1 0 0 2h13a1 1 0 1 0 0-2H13V7.83a3.01 3.01 0 0 0 1.63-1.387c.206.091.373.19.514.29.31.219.532.465.811.78l.025.027.02.023v1.78l-1.864 4.2a1.774 1.774 0 0 0 .821 2.255c.255.133.538.202.825.202h2.436a1.785 1.785 0 0 0 1.768-1.558 1.773 1.773 0 0 0-.122-.899L18 9.343v-.452c.302.072.633.109 1 .109a1 1 0 1 0 0-2c-.48 0-.731-.098-.899-.2-.2-.12-.363-.293-.651-.617l-.024-.026c-.267-.3-.622-.7-1.127-1.057a5.152 5.152 0 0 0-1.355-.678 3.001 3.001 0 0 0-5.896.04Z" clip-rule="evenodd"/>
                                            </svg>

                                        </button>
                                        <div :id="'tooltip-default3-'+ci.id" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                            {{ props.legal_message_3 }}
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                    </td>
                                    <td class="px-6 py-4">

                                        <button @click="showModal(ci.id)" :data-tooltip-target="'tooltip-default4-'+ci.id" >

                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                            </svg>

                                        </button>
                                        <div :id="'tooltip-default4-'+ci.id" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                            History
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :links="template.invoice_data.links" class="m-6" />
                </div>
            </div>




        </div>
    </AuthenticatedLayout>
</template>
