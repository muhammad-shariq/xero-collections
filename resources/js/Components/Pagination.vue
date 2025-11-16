<script>
  export default {
    props: {
      links: Array
    }
  };
  </script>
<template>
    <!-- <div>
      <Component
        :is="link.url ? 'Link' : 'span'"
        v-for="link in links"
        :href="link.url"
        v-html="link.label"
        class="px-1"
        :class="{ 'text-gray-500': ! link.url, 'font-bold' : link.active }"
      />
    </div> -->
    <nav aria-label="Page navigation example">
  <ul class="inline-flex -space-x-px text-base h-10">
    <!-- <li>
      <a href="#" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>
    </li> -->
    <li v-for="link in links">
      <a
      :href="link.url"
      v-bind:aria-current="link.active ? 'page' : null"
      :class="{ 'text-gray-500': ! link.url, 'dark:hover:bg-gray-700 dark:hover:text-white' : !link.active }"
      class="flex items-center justify-center px-4 h-10 leading-tight"
      v-bind:class="link.active ? 'text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400'"
      v-html="link.label"></a>
    </li>

  </ul>
</nav>
</template>
