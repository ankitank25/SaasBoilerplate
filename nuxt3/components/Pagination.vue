<template>
  <div
    v-if="pagination"
    class="flex items-center justify-between border-t border-gray-200 dark:border-gray-600 px-0 pt-6"
  >
    <div v-if="showPageLinks" class="flex flex-1 justify-between sm:hidden">
      <a
        href="#"
        class="relative inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        @click.prevent="updateValue(pagination?.prev_page_url)"
        >Previous</a
      >
      <a
        href="#"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        @click.prevent="updateValue(pagination?.next_page_url)"
        >Next</a
      >
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Showing
          {{ " " }}
          <span class="font-medium">{{ pagination.from }}</span>
          {{ " " }}
          to
          {{ " " }}
          <span class="font-medium">{{ pagination.to }}</span>
          {{ " " }}
          of
          {{ " " }}
          <span class="font-medium">{{ pagination.total }}</span>
          {{ " " }}
          results
        </p>
      </div>
      <div v-if="showPageLinks">
        <nav
          class="isolate inline-flex -space-x-px rounded-md shadow-sm"
          aria-label="Pagination"
        >
          <a
            v-for="(link, index) in pagination.links"
            :key="index"
            href="#"
            class="relative inline-flex items-center border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-700 focus:z-20"
            :class="[
              index == 0 ? 'rounded-l-md' : '',
              index == pagination.links.length - 1 ? 'rounded-r-md' : '',
              link.active == true
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200'
                : '',
            ]"
            @click.prevent="updateValue(link.url)"
          >
            <span v-html="link.label"></span>
          </a>
        </nav>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from "vue";
import { paginationData } from "@/types";

const props = defineProps({
  pagination: {
    type: Object as PropType<paginationData>,
    required: true,
  },
});

const showPageLinks = computed(() => {
  return !!(props.pagination.total > props.pagination.per_page);
});

const emit = defineEmits(["paginationClick"]);

const updateValue = (url: string | undefined) => {
  if (url) {
    return emit("paginationClick", url);
  }
};
</script>
