<template>
  <div>
    <form @submit.prevent="saveSpace">
      <BaseInput
        id="name"
        v-model="name"
        label="Space name"
        :validation-error="validationErrors.name"
      />
      <BaseButton
        id="update-space-btn"
        :type="buttonTypes.SUBMIT"
        :processing="form.pending"
        >Update Space</BaseButton
      >
    </form>
  </div>
</template>
<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { string, object, boolean } from "yup";
import { buttonTypes, apiResponse, Space } from "@/types";
import { useToastStore } from "@/stores/toast";
import { useSpaceStore } from "@/stores/space";

const toastStore = useToastStore();
const spaceStore = useSpaceStore();
const config = useRuntimeConfig();
const currentSpace = computed(() => spaceStore.getCurrentSpace);

const form = ref({
  pending: false,
});

const schema = object({
  name: string().required(),
  status: boolean().oneOf([true, false]),
});

const formValues = computed(() => {
  return { name: currentSpace.value?.name, status: currentSpace.value?.status };
});

const { handleSubmit, errors: validationErrors } = useForm({
  validationSchema: schema,
  initialValues: formValues,
});

const { value: name } = useField<string | undefined>("name");
const { value: status } = useField<boolean | undefined>("status");

const saveSpace = handleSubmit(async (values) => {
  form.value.pending = true;
  try {
    const { success, message, data } = await useFetchApi<apiResponse<Space>>(
      config.public.apiRoutes.spaces.concat("/", spaceStore.getCurrentSpaceId),
      {
        method: "PATCH",
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
