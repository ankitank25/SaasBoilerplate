<template>
  <div class="sm:items-start">
    <div class="mt-3 text-left">
      <div class="mt-2 mb-5">
        <p class="text-sm text-gray-500">{{ message }}</p>
      </div>
    </div>
    <form class="space-y-6" action="#" method="POST" @submit.prevent="onSubmit">
      <BaseInput
        id="email"
        v-model="email"
        label="Email"
        type="email"
        class="base-input"
        :validation-error="validationErrors.email"
      />
      <div>
        <BaseRadio
          id="role"
          v-model="role"
          :options="roleOptions"
          label="Role"
          :inline="true"
          :validation-error="validationErrors.role"
        />
      </div>
      <BaseButton
        id="invite-submit-btn"
        class="w-full"
        :type="buttonTypes.SUBMIT"
        :processing="form.pending"
        >Invite</BaseButton
      >
    </form>
  </div>
</template>

<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { string, object, number } from "yup";
import { useSpaceStore } from "@/stores/space";
import { useToastStore } from "@/stores/toast";
import {
  apiResponse,
  Space,
  buttonTypes,
  radioOptions,
  radioOption,
  SpaceRole,
} from "@/types";

const { getCurrentSpaceId } = useSpaceStore();
const toast = useToastStore();

const config = useRuntimeConfig();

const form = ref({
  pending: false,
});

const roleOptions = ref<radioOptions>([]);

const schema = object({
  email: string().email().required(),
  role: string().required(),
});

const { handleSubmit, errors: validationErrors } = useForm({
  validationSchema: schema,
});

const { value: email } = useField<string | undefined>("email");
const { value: role } = useField<string | undefined>("role");

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  disposable: {
    type: Boolean,
    default: true,
  },
  title: {
    type: String,
    default: "Invite a member",
  },
  message: {
    type: String,
    default: "",
  },
});

const emit = defineEmits(["cancel", "invited"]);

const { data, error } = await useAsyncData("user-spaceRoles", () =>
  useFetchApi<apiResponse<SpaceRole[]>>(
    config.public.apiRoutes.spaceRole.replace("SPACEID", getCurrentSpaceId),
  ),
);

watchEffect(() => {
  if (data.value) {
    roleOptions.value = data.value.data.map((role): radioOption => {
      return {
        value: role.id,
        label: role.name,
        description: role.description,
      };
    });
  }
});

if (error.value?.message) {
  toast.addError(error.value.message || "Error processing the request");
}

const onSubmit = handleSubmit(async (values) => {
  form.value.pending = true;
  try {
    const response = await useFetchApi<apiResponse<Space>>(
      config.public.apiRoutes.spaceSendInvitation.replace(
        "SPACEID",
        getCurrentSpaceId,
      ),
      {
        method: "POST",
        body: values,
      },
    );

    if (response && response.success) {
      toast.addSuccess(response.message);

      emit("invited");
    }
  } catch (error: any) {
    toast.addSuccess(error?.data?.message);
  } finally {
    form.value.pending = false;
  }
});
</script>
