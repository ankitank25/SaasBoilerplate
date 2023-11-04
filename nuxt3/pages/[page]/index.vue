<template>
  <div>
    <Head>
      <Title>{{ page?.title }}</Title>
    </Head>
    <div v-if="page?.show_title" class="text-2xl font-semibold">
      {{ page?.title }}
    </div>
    <div v-html="page?.content"></div>
  </div>
</template>
<script setup lang="ts">
import { apiResponse, Page } from "@/types";

definePageMeta({
  middleware: "site-config",
});

const page = ref<Page>();

const pageSlug = useRoute().params.page.toString();

const { data } = await useAsyncData(
  `cms-page-${pageSlug}`,
  async () => await useFetchApi<apiResponse<Page>>(`/page/${pageSlug}`),
);

if (data.value?.data) {
  page.value = data.value.data;
}

if (!page.value) {
  throw createError({ statusCode: 404, statusMessage: "Page Not Found" });
}
</script>
