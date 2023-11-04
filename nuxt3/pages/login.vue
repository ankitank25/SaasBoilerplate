<template>
  <div
    class="flex min-h-full flex-1 flex-col justify-center py-12 sm:px-6 lg:px-8"
  >
    <Head>
      <Title>Login</Title>
    </Head>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <NuxtLink to="/">
        <BaseLogo />
      </NuxtLink>
      <h2
        class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-950 dark:text-gray-400"
      >
        Sign in to your account
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

          <BaseInput
            id="password"
            v-model="password"
            type="password"
            label="Password"
            placeholder="Password"
            :validation-error="validationErrors.password"
          />

          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <BaseCheckbox
                id="remember_me"
                v-model="rememberMe"
                label="Remember me"
              />
            </div>

            <div class="text-sm leading-6">
              <NuxtLink
                to="/forgot-password"
                class="font-semibold text-indigo-600 hover:text-indigo-500"
                >Forgot password?</NuxtLink
              >
            </div>
          </div>

          <BaseButton
            id="register-btn"
            class="w-full"
            :type="buttonTypes.SUBMIT"
            :processing="form.pending"
            >Sign in</BaseButton
          >
        </form>
      </div>

      <p class="mt-10 text-center text-sm text-gray-500">
        Not a member?
        {{ " " }}
        <NuxtLink
          to="/register"
          class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
          >Sign up here</NuxtLink
        >
      </p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { string, object, boolean } from "yup";
import { buttonTypes, loginParams } from "@/types";
import { useAuthenticationStore } from "@/stores/authentication";
import { useToastStore } from "@/stores/toast";

const config = useRuntimeConfig();
const authenticationStore = useAuthenticationStore();
const toastStore = useToastStore();

definePageMeta({
  middleware: ["site-config", "guest"],
});

const form = ref({
  pending: false,
});

const schema = object({
  email: string().required().email(),
  password: string().required().min(8),
  remember_me: boolean().oneOf([true, false]).nullable(),
});

const {
  handleSubmit,
  setFieldValue,
  errors: validationErrors,
} = useForm({
  validationSchema: schema,
});

const { value: email } = useField<string | undefined>("email");
const { value: password } = useField<string | undefined>("password");
const { value: rememberMe } = useField<string | undefined>("remember_me");

// Set default value for terms_agreed checkbox to fix validation rule.
setFieldValue("remember_me", false);

const onSubmit = handleSubmit(async (values) => {
  form.value.pending = true;
  try {
    const { errorCode } = await authenticationStore.login(
      values as loginParams,
    );
    const referral = getReferral();
    if (referral) {
      return navigateTo(referral);
    }
    if (errorCode === "unverified") {
      return navigateTo(config.public.routes.emailVerify);
    }
    return navigateTo(config.public.routes.account);
  } catch (error: any) {
    toastStore.addError(error?.data?.message || "Error processing request");
  } finally {
    form.value.pending = false;
  }
});
</script>
