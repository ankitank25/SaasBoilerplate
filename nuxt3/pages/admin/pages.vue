<template>
  <div>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1
          class="text-base font-semibold leading-6 text-gray-700 dark:text-gray-300"
        >
          Pages
        </h1>
        <p class="mt-2 text-sm dark:bg-gray-800 text-gray-400">
          A list of all the users in your account including their name, title,
          email and role.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
          type="button"
          class="block rounded-md bg-indigo-600 px-3 py-1.5 text-center text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          @click.prevent="addPage"
        >
          Add new page
        </button>
      </div>
    </div>
    <BaseList
      :columns="columns"
      :data-source="pages?.data"
      :actions="actions"
      checkbox-field="id"
      :pagination="pages"
      @edit="onEdit"
      @delete="onDelete"
      @bulk-delete="deleteSelected"
      @paginate="handlePagination"
    />
    <SideOver :open="openSideOver" @close="openSideOver = false">
      <template #title>{{
        cmsPage?.title ? `Edit "${cmsPage.title}" page` : "Add new page"
      }}</template>
      <AdminPageForm
        :cms-page="cmsPage"
        @success="
          refresh();
          openSideOver = false;
        "
      />
    </SideOver>
  </div>
</template>

<script setup lang="ts">
import {
  paginatedResponse,
  apiResponse,
  Page,
  Pages,
  listColumns,
  listActions,
} from "@/types";
import { useToastStore } from "@/stores/toast";

const toastStore = useToastStore();

const openSideOver = ref<boolean>(false);

const columns: listColumns = {
  title: {
    label: "Title",
    class: "w-96",
  },
  url_key: {
    label: "URL Key",
    class: "w-96",
  },
  layout: {
    label: "Layout",
    class: "min-w-32",
  },
  status: {
    label: "Status",
    type: "options",
    options: [
      { value: true, label: "Enabled" },
      { value: false, label: "Disabled" },
    ],
  },
  updated_at: {
    label: "Updated At",
    type: "date",
  },
  created_at: {
    label: "Created At",
    type: "date",
  },
};

const actions: listActions = [
  { action: "edit", label: "Edit" },
  { action: "delete", label: "Delete" },
];

const newCmsPage: Page = {
  id: "",
  title: "",
  show_title: true,
  url_key: "",
  content: "",
  layout: "default",
  meta_title: "",
  meta_description: "",
  status: true,
  created_at: "",
  updated_at: "",
};

const cmsPage = ref<Page>(newCmsPage);

definePageMeta({
  title: "Pages",
  middleware: "admin",
  layout: "admin",
});

const pages = ref<paginatedResponse<Pages>>();

const { data, error, refresh } = await useAsyncData("pages", () =>
  useFetchApi<apiResponse<paginatedResponse<Pages>>>(`/admin/page/list`),
);

watchEffect(() => {
  if (data.value) {
    pages.value = data.value.data;
  }
});

if (error.value?.message) {
  toastStore.addError(error.value.message || "Error processing the request.");
}

const addPage = () => {
  cmsPage.value = newCmsPage;
  openSideOver.value = true;
};
const onEdit = (page: Page) => {
  cmsPage.value = page;
  openSideOver.value = true;
};
const onDelete = (page: Page) => {
  deleteSelected([page.id]);
};

const deleteSelected = async (ids: number[] | string[]) => {
  const { success, message } = await useFetchApi<apiResponse>(
    "/admin/page/delete",
    {
      method: "DELETE",
      body: { ids },
    },
  );

  if (success && message) {
    toastStore.addSuccess(message);

    refresh();
  }
};

const handlePagination = async (url: string) => {
  try {
    const { data, success } = await useFetchApi<
      apiResponse<paginatedResponse<Pages>>
    >(url);
    if (success) {
      pages.value = data;
    }
  } catch (error: any) {
    toastStore.addError(error.value.message || "Error processing the request.");
  }
};
</script>
