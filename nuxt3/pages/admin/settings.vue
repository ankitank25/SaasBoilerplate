<template>
  <div class="space-y-12">
    <form action="#" method="post" @submit.prevent="onSubmit">
      <div
        class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-4"
      >
        <div>
          <h2
            class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-300"
          >
            General
          </h2>
          <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
            This information will be displayed publicly so be careful what you
            share.
          </p>
        </div>
        <div
          class="mb-12 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-3"
        >
          <div class="col-span-3">
            <BaseInput
              id="site_title"
              v-model="form.data.site_title"
              label="Site title"
            />
          </div>
          <div class="col-span-3">
            <BaseSelect
              id="homepage"
              v-model="form.data.homepage"
              :options="pageOptions"
              label="Select Homepage"
            />
          </div>
        </div>
        <div>
          <h2
            class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-300"
          >
            Header
          </h2>
          <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
            This information will be displayed publicly so be careful what you
            share.
          </p>
        </div>
        <div
          class="mb-12 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-3"
        >
          <div class="col-span-3">
            <BaseSelect
              id="header_menu"
              v-model="form.data.header_menu_id"
              :options="menuOptions"
              label="Header menu"
            />
          </div>
        </div>
        <div>
          <h2
            class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-300"
          >
            Footer
          </h2>
          <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
            This information will be displayed publicly so be careful what you
            share.
          </p>
        </div>
        <div
          class="mb-12 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-3"
        >
          <div class="col-span-3">
            <BaseSelect
              id="footer_menu"
              v-model="form.data.footer_menu_id"
              :options="menuOptions"
              label="Footer menu"
            />
          </div>
          <div class="col-span-3">
            <BaseSelect
              id="social_menu"
              v-model="form.data.social_menu_id"
              :options="menuOptions"
              label="Social menu"
            />
          </div>
          <div class="col-span-3">
            <BaseInput
              id="footer_copyright"
              v-model="form.data.footer_copyright"
              label="Footer copyright"
            />
          </div>
        </div>
        <div>
          <h2
            class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-300"
          >
            Miscellaneous
          </h2>
          <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
            This information will be displayed publicly so be careful what you
            share.
          </p>
        </div>
        <div
          class="mb-12 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-3"
        >
          <div class="col-span-6">
            <BaseTextarea
              id="head_script"
              v-model="form.data.head_script"
              label="Head script"
            />
          </div>
          <div class="col-span-6">
            <BaseTextarea
              id="head_css"
              v-model="form.data.head_css"
              label="Head CSS"
            />
          </div>
          <div class="col-span-6">
            <BaseTextarea
              id="after_body_start"
              v-model="form.data.after_body_start"
              label="After body start content"
            />
          </div>
          <div class="col-span-6">
            <BaseTextarea
              id="before_body_end"
              v-model="form.data.before_body_end"
              label="Before body end content"
            />
          </div>
        </div>
        <div>
          <h2
            class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-300"
          >
            Schema
          </h2>
          <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
            This information will be displayed publicly so be careful what you
            share.
          </p>
        </div>
        <div
          class="mb-12 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-3"
        >
          <div class="col-span-6">
            <BaseTextarea
              id="schema"
              v-model="form.data.schema"
              label="Schema content"
            />
          </div>
        </div>
        <BaseButton
          id="register-btn"
          class="w-32"
          :type="buttonTypes.SUBMIT"
          :processing="form.pending"
          >Save Config</BaseButton
        >
      </div>
    </form>
  </div>
</template>
<script setup lang="ts">
import { useToastStore } from "@/stores/toast";
import {
  buttonTypes,
  apiResponse,
  Settings,
  paginatedResponse,
  Menus,
  Pages,
  selectOptions,
} from "@/types";

const toastStore = useToastStore();

definePageMeta({
  title: "Settings",
  middleware: ["admin", "site-config"],
  layout: "admin",
});

const form = ref({
  data: {
    site_title: getSiteConfig("site_title")?.value,
    homepage: getSiteConfig("homepage")?.value,
    footer_copyright: getSiteConfig("footer_copyright")?.value,
    header_menu_id: getSiteConfig("header_menu_id")?.value,
    footer_menu_id: getSiteConfig("footer_menu_id")?.value,
    social_menu_id: getSiteConfig("social_menu_id")?.value,
    head_script: getSiteConfig("head_script")?.value,
    head_css: getSiteConfig("head_css")?.value,
    after_body_start: getSiteConfig("after_body_start")?.value,
    before_body_end: getSiteConfig("before_body_end")?.value,
    schema: getSiteConfig("schema")?.value,
  },
  pending: false,
});

const onSubmit = async () => {
  form.value.pending = true;
  try {
    const { message } = await useFetchApi<apiResponse<Settings>>(
      "/admin/settings/save",
      {
        method: "POST",
        body: { config: form.value.data },
      },
    );
    toastStore.addSuccess(message);
  } catch (error: any) {
    let message = error?.data?.message || "Error processing the request";
    if (error.status === 422 && error.data.data) {
      message = Object.values(error.data.data).join("<br/>");
    }
    toastStore.addError(message);
  } finally {
    form.value.pending = false;
  }
};

const menuOptions = ref<selectOptions>([]);

const { data: cmsMenus } = await useAsyncData("menus", () =>
  useFetchApi<apiResponse<paginatedResponse<Menus>>>(`/admin/menu/list`),
);

if (cmsMenus) {
  cmsMenus.value?.data.data.map((menu) => {
    menuOptions.value.push({
      value: menu.id,
      label: menu.title,
    });
  });
}

const pageOptions = ref<selectOptions>([]);

const { data: cmsPages } = await useAsyncData("pages", () =>
  useFetchApi<apiResponse<paginatedResponse<Pages>>>(`/admin/page/list`),
);

if (cmsPages) {
  cmsPages.value?.data.data.map((page) => {
    pageOptions.value.push({
      value: page.url_key,
      label: page.title,
    });
  });
}
</script>
