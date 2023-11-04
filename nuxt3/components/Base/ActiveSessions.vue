<template>
  <div class="mt-12">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1
          class="text-base font-semibold leading-6 text-gray-700 dark:text-gray-300"
        >
          Active sessions
        </h1>
        <p class="mt-2 text-sm dark:bg-gray-800 text-gray-400">
          A list of all active session of your account.
        </p>
      </div>
    </div>
    <BaseList
      :columns="columns"
      :data-source="sessions"
      :actions="actions"
      :delete-prompt="deletePrompt"
      @delete="deleteSession"
    />
  </div>
</template>
<script setup lang="ts">
import {
  apiResponse,
  userSessions,
  listColumns,
  listActions,
  listDeletePrompt,
} from "@/types";
import { useToastStore } from "@/stores/toast";

const columns: listColumns = {
  ip: {
    label: "IP",
    class: "w-32",
  },
  location: {
    label: "Location",
  },
  device: {
    label: "Device",
  },
  platform: {
    label: "Platform",
  },
  browser: {
    label: "Browser",
  },
  created_at: {
    label: "Created At",
    type: "date",
  },
};

const actions: listActions = [{ action: "delete", label: "Delete" }];

const deletePrompt: listDeletePrompt = {
  title: "Session delete confirmation",
  message: "Are you sure you want to delete the session?",
};

const config = useRuntimeConfig();
const toast = useToastStore();

const sessions = ref<userSessions>([]);
const { data, error } = await useAsyncData("user-sessions", () =>
  useFetchApi<apiResponse<userSessions>>(config.public.apiRoutes.userSessions),
);

if (data.value) {
  sessions.value = data.value.data;
}

if (error.value?.message) {
  toast.addError(error.value.message || "Error processing the request");
}

const deleteSession = async (item: any) => {
  const { success, message, data } = await useFetchApi<
    apiResponse<userSessions>
  >(
    config.public.apiRoutes.userSessionDelete.replace(
      "SESSIONID",
      item.id.toString(),
    ),
    { method: "DELETE" },
  );

  if (success && message) {
    toast.addSuccess(message);

    if (data) {
      sessions.value = data;
    }
  }
};
</script>
