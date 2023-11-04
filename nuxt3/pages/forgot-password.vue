<template>
  <div
    class="flex min-h-full flex-1 flex-col justify-center py-12 sm:px-6 lg:px-8"
  >
    <Head>
      <Title>Forgot password</Title>
    </Head>  
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <NuxtLink to="/">
        <BaseLogo />
      </NuxtLink>
      <h2
        class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-950 dark:text-gray-400"
      >
        Forgot password
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
      <div
        class="bg-white dark:bg-slate-800 px-6 py-12 shadow sm:rounded-lg sm:px-12"
      >
        <form action="#" method="POST" @submit.prevent="onSubmit">
          <BaseInput
            id="email"
            v-model="email"
            type="email"
            label="Email"
            placeholder="Email"
            :validation-error="validationErrors.email"
          />

          <BaseButton
            id="submit-btn"
            class="w-full"
            :type="buttonTypes.SUBMIT"
            :processing="form.pending"
            >Submit</BaseButton
          >
        </form>
      </div>

      <p class="mt-10 text-center text-sm text-gray-500">
        Back to
        {{ " " }}
        <NuxtLink
          to="/login"
          class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
          >Login</NuxtLink
        >
      </p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { string, object } from "yup";
import { buttonTypes } from "@/types";
import { useAuthenticationStore } from "@/stores/authentication";
import { useToastStore } from "@/stores/toast";

definePageMeta({
  middleware: ["site-config", "guest"],
});

const authenticationStore = useAuthenticationStore();
const toast = useToastStore();
const config = useRuntimeConfig();

const form = ref({
  pending: false,
});

const schema = object({
  email: string().required().email(),
});

const { handleSubmit, errors: validationErrors } = useForm({
  validationSchema: schema,
});

const { value: email } = useField<string | undefined>("email");

const onSubmit = handleSubmit(async (values) => {
  form.value.pending = true;
  try {
    const { success, message } = await authenticationStore.forgotPassword(
      values as { email: string },
    );
    if (success && message) {
      toast.addSuccess(message);
    }
    return navigateTo(config.public.routes.login);
  } catch (error: any) {
    toast.addError(error?.data?.message || "Error processing request");
  } finally {
    form.value.pending = false;
  }
});
</script>
