<template>
  <div>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1
          class="text-base font-semibold leading-6 text-gray-700 dark:text-gray-300"
        >
          Users
        </h1>
        <p class="mt-2 text-sm dark:bg-gray-800 text-gray-400">
          A list of all the users assigned to current space.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <BaseButton id="user-invite" @click="inviteUserModal = true"
          >Invite a user</BaseButton
        >
      </div>
    </div>
    <div v-if="users?.length">
      <BaseList
        :columns="columns"
        :data-source="users"
        :actions="actions"
        :delete-prompt="deletePrompt"
      />
    </div>
    <div v-else class="my-8">
      No other users currently assigned to this space.
    </div>
    <div v-if="pendingInvitations?.length" class="mt-32">
      <div class="sm:flex-auto">
        <h1
          class="text-base font-semibold leading-6 text-gray-700 dark:text-gray-300"
        >
          Pending invitations
        </h1>
        <p class="mt-2 text-sm dark:bg-gray-800 text-gray-400">
          A list of all the users assigned to current space.
        </p>
      </div>
      <BaseList
        :columns="pendingInvitationsColumns"
        :data-source="pendingInvitations"
        :actions="pendingInvitationsActions"
        :delete-prompt="pendingInvitationsDeletePrompt"
        @delete="onDeleteInvitation"
      />
    </div>
    <Modal
      :open="inviteUserModal"
      title="Invite a new user"
      @close="inviteUserModal = false"
    >
      <ModalInviteUser />
    </Modal>
  </div>
</template>
<script setup lang="ts">
import { useToastStore } from "@/stores/toast";
import {
  listActions,
  listDeletePrompt,
  SpaceInvitation,
  apiResponse,
  listColumns,
} from "@/types";
import { useSpaceStore } from "@/stores/space";
import { useUserStore } from "@/stores/user";

definePageMeta({
  middleware: "user",
  layout: "account",
  title: "Users",
});

const config = useRuntimeConfig();
const inviteUserModal = ref(false);
const toastStore = useToastStore();
const userStore = useUserStore();
const spaceStore = useSpaceStore();
const currentSpace = computed(() => spaceStore.getCurrentSpace);
const users = computed(() => {
  return currentSpace.value?.users?.map((user) => {
    if (user.id === userStore.getUser?.id) {
      user.role = "Owner";
    }
    return user;
  });
});

const columns: listColumns = {
  first_name: {
    label: "First name",
  },
  last_name: {
    label: "Last name",
  },
  email: {
    label: "Email",
  },
  role: {
    label: "Role",
    noActionValue: "Owner",
  },
};

const actions: listActions = [{ action: "delete", label: "Revoke Access" }];

const deletePrompt: listDeletePrompt = {
  title: "User revoke confirmation",
  message: "Are you sure you want to revoke user access to this space?",
};

const pendingInvitations = computed(() => {
  return currentSpace.value?.invitations?.map((invitation) => {
    return {
      email: invitation.email,
      role: invitation.role.name,
      id: invitation.id,
      status: invitation.status,
    };
  });
});

const pendingInvitationsColumns = {
  email: {
    label: "Email",
  },
  status: {
    label: "Status",
    type: "options",
    options: [
      { value: 0, label: "Pending" },
      { value: 2, label: "Rejected" },
    ],
  },
  role: {
    label: "Role",
    class: "w-96",
  },
};

const pendingInvitationsActions: listActions = [
  { action: "delete", label: "Revoke Invitation" },
];

const pendingInvitationsDeletePrompt: listDeletePrompt = {
  title: "Revoke user invitation",
  message: "Are you sure you want to revoke user's invitation to this space?",
};

const onDeleteInvitation = async (invitation: SpaceInvitation) => {
  try {
    const { success, message } = await useFetchApi<apiResponse>(
      config.public.apiRoutes.spaceInvitationDelete.replace(
        "INVITATIONID",
        invitation.id.toString(),
      ),
      {
        method: "DELETE",
      },
    );

    if (success && message) {
      toastStore.addSuccess(message);
    }
  } catch (error: any) {
    toastStore.addError(error?.data?.message || "Error processing request");
  }
};
</script>
