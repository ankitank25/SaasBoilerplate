<template>
  <div class="mt-12">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1
          class="text-base font-semibold leading-6 text-gray-700 dark:text-gray-300"
        >
          Manage roles
        </h1>
        <p class="mt-2 text-sm dark:bg-gray-800 text-gray-400">
          A list of all role of current space.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <BaseButton
          id="add-role-btn"
          :type="buttonTypes.BUTTON"
          class="block rounded-md bg-indigo-600 px-3 py-1.5 text-center text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          @click="addRoleModal = true"
        >
          Add new role
        </BaseButton>
      </div>
    </div>
    <BaseList
      :columns="columns"
      :data-source="spaceRoles"
      :actions="actions"
      :delete-prompt="deletePrompt"
      @delete="deleteRole"
    />
    <Modal
      :open="addRoleModal"
      title="Add new role"
      @close="addRoleModal = false"
    >
      <ModalSpaceRole
        @added="
          refresh();
          addRoleModal = false;
        "
      />
    </Modal>
  </div>
</template>
<script setup lang="ts">
import { useSpaceStore } from "@/stores/space";
import {
  listDeletePrompt,
  apiResponse,
  listColumns,
  listActions,
  buttonTypes,
  SpaceRole,
} from "@/types";
import { useToastStore } from "@/stores/toast";

const columns: listColumns = {
  name: {
    label: "Name",
  },
  abilities: {
    label: "Abilities",
  },
  created_at: {
    label: "Created At",
    type: "date",
  },
  updated_at: {
    label: "Updated At",
    type: "date",
  },
};

const actions: listActions = [
  { action: "edit", label: "Edit" },
  { action: "delete", label: "Delete" },
];

const deletePrompt: listDeletePrompt = {
  title: "Role delete confirmation",
  message: "Are you sure you want to delete the role?",
};

const config = useRuntimeConfig();
const toast = useToastStore();
const spaceStore = useSpaceStore();

const addRoleModal = ref(false);

const spaceRoles = ref<SpaceRole[]>([]);
const { data, error, refresh } = await useAsyncData("user-spaceRoles", () =>
  useFetchApi<apiResponse<SpaceRole[]>>(
    config.public.apiRoutes.spaceRole.replace(
      "SPACEID",
      spaceStore.getCurrentSpaceId,
    ),
  ),
);

watchEffect(() => {
  if (data.value) {
    spaceRoles.value = data.value.data;
  }
});

if (error.value?.message) {
  toast.addError(error.value.message || "Error processing the request");
}

const deleteRole = async (item: any) => {
  const { success, message } = await useFetchApi<apiResponse<SpaceRole[]>>(
    config.public.apiRoutes.spaceRoleDelete
      .replace("SESSIONID", spaceStore.getCurrentSpaceId)
      .replace("ROLEID", item.id),
    { method: "DELETE" },
  );

  if (success && message) {
    toast.addSuccess(message);

    refresh();
  }
};
</script>
