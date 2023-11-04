<template>
  <div>
    <ul v-if="notifications?.data" role="list" class="divide-y divide-gray-100">
      <li
        v-for="notification in notifications.data"
        :key="notification.id"
        class="flex items-center justify-between gap-x-6 py-5"
      >
        <div class="flex gap-x-4">
          <div
            class="mt-1 ring-1 ring-black flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-white"
          >
            <component
              :is="getNotificationIcon(notification.type)"
              class="h-6 w-6 text-gray-600 group-hover:text-indigo-600"
              aria-hidden="true"
            />
          </div>
          <div class="min-w-0 flex-auto">
            <p
              class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300"
            >
              {{ notification.message }}
            </p>
            <p
              class="mt-1 truncate text-xs leading-5 text-gray-500 dark:text-gray-300"
            >
              {{ notification.updated_at }}
            </p>
          </div>
        </div>
        <a
          href="#"
          class="rounded-full bg-white dark:bg-gray-700 px-2.5 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
          >View</a
        >
      </li>
    </ul>
    <Pagination
      v-if="notifications"
      :pagination="notifications"
      @pagination-click="handlePagination"
    />
  </div>
</template>

<script setup lang="ts">
import { ArrowPathIcon, ShieldCheckIcon } from "@heroicons/vue/24/outline";
import { apiResponse, paginatedResponse, userNotifications } from "@/types";
import { useToastStore } from "@/stores/toast";

const toast = useToastStore();
const config = useRuntimeConfig();

const getNotificationIcon = (type: string) => {
  if (
    [
      "profile_twofa_disabled",
      "profile_twofa_email_enabled",
      "profile_password_update",
      "profile_email_updated",
    ].includes(type)
  ) {
    return ShieldCheckIcon;
  } else {
    return ArrowPathIcon;
  }
};

const notifications = ref<paginatedResponse<userNotifications>>();

onMounted(async () => {
  const { data } = await useFetchApi<
    apiResponse<paginatedResponse<userNotifications>>
  >(config.public.apiRoutes.userNotification);

  if (data) {
    notifications.value = data;
  }
});

const handlePagination = async (url: string) => {
  try {
    const { data, success } = await useFetchApi<
      apiResponse<paginatedResponse<userNotifications>>
    >(url);
    if (success) {
      notifications.value = data;
    }
  } catch (error: any) {
    toast.addError(error?.data?.message || "Error processing the request.");
  }
};
</script>
