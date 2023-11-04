<template>
  <div>
    <form @submit.prevent="createSpace">
      <BaseInput
        id="name"
        v-model="name"
        label="Space name"
        :validation-error="validationErrors.name"
      />
      <BaseButton id="new-space-btn" :type="buttonTypes.SUBMIT"
        >Create Space</BaseButton
      >
    </form>
  </div>
</template>
<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { string, object } from "yup";
import { buttonTypes, apiResponse, Space } from "@/types";
import { useToastStore } from "@/stores/toast";
import { useSpaceStore } from "@/stores/space";

const toastStore = useToastStore();
const spaceStore = useSpaceStore();
const config = useRuntimeConfig();

const form = ref({
  pending: false,
});

const schema = object({
  name: string().required(),
});

const { handleSubmit, errors: validationErrors } = useForm({
  validationSchema: schema,
});

const { value: name } = useField<string | undefined>("name");

const createSpace = handleSubmit(async (values) => {
  form.value.pending = true;
  try {
    const { success, message, data } = await useFetchApi<apiResponse<Space>>(
      config.public.apiRoutes.spaces,
      {
        method: "POST",
        body: values,
      },
    );

    if (success && message) {
      toastStore.addSuccess(message);
    }
    spaceStore.setCurrentSpace(data);
    spaceStore.updateSpace(data);
  } catch (error: any) {
    toastStore.addError(error?.data?.message || "Error processing request");
  } finally {
    form.value.pending = false;
  }
});
</script>
