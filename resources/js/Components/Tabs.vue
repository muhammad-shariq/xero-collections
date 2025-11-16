<script setup lang="ts">
    import { ref } from 'vue';
    import { FwbTab, FwbTabs } from 'flowbite-vue';

   const props = defineProps<{
        email_template_data: Array;
    }>();
    // console.log(props.email_template_data[0].get_templates.name);
    let activeTab = "nothing";
    if( props.email_template_data.length )
    {
        activeTab = props.email_template_data[0].get_templates.name??"nothing";
    }

    activeTab = ref(activeTab);

    let totalInvoiceLength = 0;
    let totalInvoiceCollection = 0;

    function countInvoices()
    {
        let t = 0;
        let d = [0, 0.00];
        let a = 0;

        for(let i in props.email_template_data){
            // console.log(i);
            t += props.email_template_data[i].invoice_data.length;
            a += props.email_template_data[i].amount_paid - props.email_template_data[i].import_amount_paid;
        }

        d[0] = t;
        d[1] = a;
        return d;
    }
    let d = countInvoices();
    totalInvoiceLength = d[0];
    totalInvoiceCollection = d[1];


    const handlePaneClick = () => { console.log('Click!') }
</script>

<template>
   <!-- <li v-for="temp in email_template_data">{{ temp.id }}</li> -->

    <fwb-tabs @click:pane="handlePaneClick" v-model="activeTab" class="p-5 m-5">
      <fwb-tab v-for="temp in email_template_data"  :name="temp.get_templates.name" :title="temp.get_templates.name">
        <!-- {{ temp.invoice_data.length }} -->
            <section class="bg-white dark:bg-gray-900">
                <div class="max-w-screen-xl px-4 py-8 mx-auto text-center lg:py-16 lg:px-6">
                    <dl class="grid max-w-screen-l gap-8 mx-auto text-gray-900 sm:grid-cols-3 dark:text-white">
                        <div class="flex flex-col items-center justify-center">
                            <dd class="text-1xl md:text-2xl font-extrabold">Invoices in collections</dd>
                        </div>

                        <div class="flex flex-col items-center justify-center">
                            <dt class="mb-2 text-3xl md:text-4xl font-extrabold">{{ temp.invoice_data.length }}</dt>
                            <dd class="font-light text-gray-500 dark:text-gray-400">Total Number of Invoices</dd>
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <dt class="mb-2 text-3xl md:text-4xl font-extrabold">${{temp.amount_paid - temp.import_amount_paid}}</dt>
                            <dd class="font-light text-gray-500 dark:text-gray-400">Total Amount</dd>
                        </div>

                    </dl>
                </div>
            </section>
      </fwb-tab>

      <fwb-tab name="further action" title="Further Action">
        <section class="bg-white dark:bg-gray-900">
                <div class="max-w-screen-xl px-4 py-8 mx-auto text-center lg:py-16 lg:px-6">
                    <dl class="grid max-w-screen-l gap-8 mx-auto text-gray-900 sm:grid-cols-3 dark:text-white">
                        <div class="flex flex-col items-center justify-center">
                            <dd class="text-1xl md:text-2xl font-extrabold">Invoices in collections</dd>
                        </div>

                        <div class="flex flex-col items-center justify-center">
                            <dt class="mb-2 text-3xl md:text-4xl font-extrabold">{{ totalInvoiceLength }}</dt>
                            <dd class="font-light text-gray-500 dark:text-gray-400">Total Number of Invoices</dd>
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <dt class="mb-2 text-3xl md:text-4xl font-extrabold">${{ totalInvoiceCollection }}</dt>
                            <dd class="font-light text-gray-500 dark:text-gray-400">Total Amount</dd>
                        </div>

                    </dl>
                </div>
            </section>
      </fwb-tab>

    </fwb-tabs>
  </template>

