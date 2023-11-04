<template>
  <div>
    <Head>
      <Title>{{ page?.title }}</Title>
    </Head>
    <div v-if="page">
      <div v-if="page.show_title" class="text-2xl font-semibold">
        {{ page.title }}
      </div>
      <div v-html="page.content"></div>
    </div>
    <div
      v-else
      class="text-2xl text-center my-20 text-gray-700 dark:text-gray-400"
    >
      Homepage
    </div>
  </div>
</template>
<script setup lang="ts">
import { Page, apiResponse } from "@/types";

definePageMeta({
  middleware: "site-config",
});

const page = ref<Page>();

const pageSlug = getSiteConfig("homepage")?.value;

if (pageSlug) {
  const { data } = await useAsyncData(
    `cms-page-${pageSlug}`,
    async () => await useFetchApi<apiResponse<Page>>(`/page/${pageSlug}`),
  );

  if (data.value?.data) {
    page.value = data.value.data;
  }
}
</script>
