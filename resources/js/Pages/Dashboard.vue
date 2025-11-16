<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Tabs from '@/Components/Tabs.vue';

const props = defineProps<{
    data: Array;
    amount_due: Number;
    amount_paid: Number;
    xero_overdue_invoice: Number;
    xero_amount_due: Number;
    email_template_data: Array;
}>();

const options = {
  colors: ["#1A56DB", "#FDBA8C"],
  series: props.data,
//   xaxis: {
//     type: "date"
//   },
  chart: {
    type: "bar",
    height: "320px",
    fontFamily: "Inter, sans-serif",
    toolbar: {
      show: false,
    },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "70%",
      borderRadiusApplication: "end",
      borderRadius: 8,
    },
  },
  tooltip: {
    shared: true,
    intersect: false,
    style: {
      fontFamily: "Inter, sans-serif",
    },
  },
  states: {
    hover: {
      filter: {
        type: "darken",
        value: 1,
      },
    },
  },
  stroke: {
    show: true,
    width: 0,
    colors: ["transparent"],
  },
  grid: {
    show: false,
    strokeDashArray: 4,
    padding: {
      left: 2,
      right: 2,
      top: -14
    },
  },
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: false,
  },
  xaxis: {
    // type: "datetime",
    floating: false,
    labels: {
      show: true,
      style: {
        fontFamily: "Inter, sans-serif",
        cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
      }
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  fill: {
    opacity: 1,
  },
}

const pieChart = {
    series: [props.amount_due, props.amount_paid],
    chartOptions: {
        chart: {
            width: 400,
            type: 'pie',
        },
        labels: ['Total Amount Due', 'Total Recovery Amount after collection process started'],
        // dataLabels: {
        //     enabled: true,
        //     formatter: function (val) {
        //         return '$' + val; // Add the currency sign
        //     }
        // },
        responsive: [{
            breakpoint: 480,
            options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
            }
        }]
    }
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 v-if="$page.props.auth.user.xero_organization_name" class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $page.props.auth.user.xero_organization_name }}</h2>
            <h2 v-else class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>

        </template>

        <div class="py-12">
            <!-- <div class="max-w-7xl m-8 sm:px-6 lg:px-8">
                <Link
                        :href="route('xero.invoices')"
                        method="get"
                        as="button"
                        >
                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Start Collection</button>
                </Link>
            </div> -->
            <!-- {{  props.email_template_data  }} -->
            <div class="max-w-7xl m-8 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    <section class="bg-white dark:bg-gray-900">
                        <div class="max-w-screen-xl px-4 py-8 mx-auto text-center lg:py-16 lg:px-6">
                            <dl class="grid max-w-screen-l gap-8 mx-auto text-gray-900 sm:grid-cols-4 dark:text-white">
                                <div class="flex flex-col items-center justify-center">

                                    <dd class="text-1xl md:text-2xl font-extrabold">Xero Invoices awaiting collections</dd>
                                </div>
                                <div class="flex flex-col items-center justify-center">
                                    <dt class="mb-2 text-3xl md:text-4xl font-extrabold">{{ $props.xero_overdue_invoice }}</dt>
                                    <dd class="font-light text-gray-500 dark:text-gray-400">Total Invoices</dd>
                                </div>
                                <div class="flex flex-col items-center justify-center">
                                    <dt class="mb-2 text-3xl md:text-4xl font-extrabold">${{ $props.xero_amount_due }}</dt>
                                    <dd class="font-light text-gray-500 dark:text-gray-400">Total Amount</dd>
                                </div>

                                <div class="flex flex-col items-center justify-center">
                                    <Link class="ml-5 mb-2" :href="route('xero.invoices')" method="get"
                                        as="button" >
                                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Start Collection</button>
                                    </Link>
                                </div>

                            </dl>
                        </div>
                    </section>

                </div>
            </div>


            <div class="max-w-7xl m-8 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">

                    <Tabs :email_template_data="email_template_data" />

                </div>
            </div>

            <div class="max-w-7xl m-8 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="flex flex-row justify-center items-center p-6 text-gray-900 dark:text-gray-400 font-bold">Total Collections</div>
                        <div class="flex flex-row justify-center items-center">
                            <apexchart type="pie" width="600" :options="pieChart.chartOptions" :series="pieChart.series"></apexchart>
                        </div>
                    </div>


                </div>
            </div>

            <div class="max-w-7xl m-8 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    <!-- <div class="flex items-center p-4 m-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">Danger alert!</span> Change a few things up and try submitting again.
                        </div>
                    </div> -->

                    <div class="flex flex-row justify-center items-center p-6 text-gray-900 dark:text-gray-400 font-bold">Amount Due/Paid Chart</div>
                    <div class="flex flex-row justify-center items-center">
                        <apexchart width="1000" height="500" type="bar" :options="options" :series="options.series"></apexchart>
                    </div>
                </div>




            </div>

            <!-- <div class="max-w-7xl m-8 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-row justify-center items-center p-6 text-gray-900 dark:text-gray-400 font-bold">Pie Chart</div>
                    <div class="flex flex-row justify-center items-center">
                        <apexchart type="pie" width="380" :options="pieChart.chartOptions" :series="pieChart.series"></apexchart>
                    </div>
                </div>
            </div> -->


            <!-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">Welcome</div>
                    <div class="flex justify-center p-4">
                        <button id="button" data-modal-toggle="modal" data-modal-target="modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Show modal</button>
                    </div>
                </div>
            </div> -->


        </div>

        <div id="modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-2xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 lg:text-2xl dark:text-white">
                            Terms of Service
                        </h3>
                        <button id="closeButton" data-modal-hide="modal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-6 space-y-6">
                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                            With less than a month to go before the European Union enacts new consumer privacy laws for its citizens, companies around the world are updating their terms of service agreements to comply.
                        </p>
                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                            The European Union’s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25 and is meant to ensure a common set of data rights in the European Union. It requires organizations to notify users as soon as possible of high-risk data breaches that could personally affect them.
                        </p>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
                        <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Decline</button>
                    </div>
                </div>
            </div>

    </div>

    </AuthenticatedLayout>
</template>
