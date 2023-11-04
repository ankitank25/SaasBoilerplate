<template>
  <div>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1
          class="text-base font-semibold leading-6 text-gray-700 dark:text-gray-300"
        >
          Menus
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
          @click.prevent="addMenu"
        >
          Add menu
        </button>
      </div>
    </div>
    <BaseList
      :columns="columns"
      :data-source="menus?.data"
      :actions="actions"
      checkbox-field="id"
      :pagination="menus"
      @edit="onEdit"
      @delete="onDelete"
      @bulk-delete="deleteSelected"
      @paginate="handlePagination"
    />
    <SideOver :open="openSideOver" @close="openSideOver = false">
      <template #title>{{
        cmsMenu?.title ? `Edit "${cmsMenu.title}" menu` : "Add new menu"
      }}</template>
      <AdminMenuForm
        :cms-menu="cmsMenu"
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
  Menu,
  Menus,
  listColumns,
  listActions,
} from "@/types";
import { useToastStore } from "@/stores/toast";

const toastStore = useToastStore();

const openSideOver = ref<boolean>(false);

definePageMeta({
  title: "CMS Menu",
  middleware: "admin",
  layout: "admin",
});

const columns: listColumns = {
  id: {
    label: "ID",
    class: "w-96",
  },
  title: {
    label: "Title",
    class: "w-96",
  },
  status: {
    label: "Status",
    class: "w-32",
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

const newCmsMenu: Menu = {
  id: "",
  title: "",
  items: "",
  status: true,
  created_at: "",
  updated_at: "",
};

const cmsMenu = ref<Menu>(newCmsMenu);

definePageMeta({
  title: "Menus",
  middleware: "admin",
  layout: "admin",
});

const menus = ref<paginatedResponse<Menus>>();

const { data, error, refresh } = await useAsyncData("menus", () =>
  useFetchApi<apiResponse<paginatedResponse<Menus>>>(`/admin/menu/list`),
);

watchEffect(() => {
  if (data.value) {
    menus.value = data.value.data;
  }
});

if (error.value?.message) {
  toastStore.addSuccess(
    error.value?.message || "Error processing the request.",
  );
}

const addMenu = () => {
  cmsMenu.value = newCmsMenu;
  openSideOver.value = true;
};
const onEdit = (menu: Menu) => {
  cmsMenu.value = menu;
  openSideOver.value = true;
};
const onDelete = (page: Menu) => {
  deleteSelected([page.id]);
};

const deleteSelected = async (ids: number[] | string[]) => {
  const { success, message } = await useFetchApi<apiResponse>(
    "/admin/menu/delete",
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
      apiResponse<paginatedResponse<Menus>>
    >(url);
    if (success) {
      menus.value = data;
    }
  } catch (error: any) {
    toastStore.addSuccess(
      error?.data?.message || "Error processing the request.",
    );
  }
};
</script>
