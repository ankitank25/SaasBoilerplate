<template>
  <div
    class="flex min-h-full flex-1 flex-col justify-center py-12 sm:px-6 lg:px-8"
  >
    <Head>
      <Title>Reset password</Title>
    </Head>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <NuxtLink to="/">
        <BaseLogo />
      </NuxtLink>
      <h2
        class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-950 dark:text-gray-400"
      >
        Reset password
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
      <div
        class="bg-white dark:bg-slate-800 px-6 py-12 shadow sm:rounded-lg sm:px-12"
      >
        <form action="#" method="POST" @submit.prevent="onSubmit">
          <BaseInput
            id="token"
            v-model="token"
            type="hidden"
            readonly="true"
            aria-readonly="true"
            class="m-0"
            :validation-error="validationErrors.token"
          />

          <BaseInput
            id="email"
            v-model="email"
            type="email"
            label="Email"
            placeholder="Email"
            :validation-error="validationErrors.email"
          />

          <BaseInput
            id="password"
            v-model="password"
            label="Password"
            type="password"
            placeholder="Password"
            :validation-error="validationErrors.password"
          />
          <BaseInput
            id="password_confirmation"
            v-model="passwordConfirmation"
            label="Confirm password"
            type="password"
            placeholder="Confirm password"
            :validation-error="validationErrors.password_confirmation"
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
import { string, object, ref as yupRef } from "yup";
import { buttonTypes, resetPasswordParams } from "@/types";
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
  token: string().required(),
  email: string().required().email(),
  password: string().required().min(parseInt(config.public.minPasswordLength)),
  password_confirmation: string()
    .required()
    .oneOf(
      [yupRef("password")],
      "Confirm password should be same as password.",
    ),
});

const {
  handleSubmit,
  errors: validationErrors,
  setFieldValue,
} = useForm({
  validationSchema: schema,
});

const { value: token } = useField<string | undefined>("token");
const { value: email } = useField<string | undefined>("email");
const { value: password } = useField<string>("password");
const { value: passwordConfirmation } = useField<string>(
  "password_confirmation",
);

const route = useRoute();
if (route.query.token) {
  // Set default value for terms_agreed checkbox to fix validation rule.
  setFieldValue("token", route.query.token);
} else {
  toast.addError("Password reset token not provided.");
  navigateTo(config.public.routes.login);
}

const onSubmit = handleSubmit(async (values) => {
  form.value.pending = true;
  try {
    const { success, message } = await authenticationStore.resetPassword(
      values as resetPasswordParams,
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
