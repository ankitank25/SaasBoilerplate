<template>
  <div
    class="flex min-h-full flex-1 flex-col justify-center py-12 sm:px-6 lg:px-8"
  >
    <Head>
      <Title>Register</Title>
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
        <form @submit.prevent="onSubmit">
          <BaseInput
            id="first_name"
            v-model="firstName"
            label="First name"
            type="text"
            :validation-error="validationErrors.first_name"
          />
          <BaseInput
            id="last_name"
            v-model="lastName"
            label="Last name"
            type="text"
            :validation-error="validationErrors.last_name"
          />
          <BaseInput
            id="email"
            v-model="email"
            label="Email"
            type="text"
            :validation-error="validationErrors.email"
          />
          <BaseInput
            id="password"
            v-model="password"
            label="Password"
            type="password"
            :validation-error="validationErrors.password"
          />
          <BaseInput
            id="password_confirmation"
            v-model="passwordConfirmation"
            label="Confirm password"
            type="password"
            :validation-error="validationErrors.password_confirmation"
          />
          <BasePasswordStength :password="password" class="mb-6" />
          <BaseCheckbox
            id="terms_agreed"
            v-model="termsAgreed"
            label="I agree to the Terms and Conditions"
            class="mb-6"
            :validation-error="validationErrors.terms_agreed"
          />
          <BaseButton
            id="register-btn"
            class="w-full"
            :type="buttonTypes.SUBMIT"
            :processing="form.pending"
            >Sign up</BaseButton
          >
        </form>
      </div>

      <p class="mt-10 text-center text-sm text-gray-500">
        Already a member?
        {{ " " }}
        <NuxtLink
          to="/login"
          class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
          >Login here</NuxtLink
        >
      </p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { string, object, boolean, ref as yupRef } from "yup";
import { buttonTypes, registerParams } from "@/types";
import { useAuthenticationStore } from "@/stores/authentication";
import { useToastStore } from "@/stores/toast";

definePageMeta({
  middleware: ["site-config", "guest"],
});

const authenticationStore = useAuthenticationStore();
const config = useRuntimeConfig();
const toastStore = useToastStore();

const form = ref({
  pending: false,
});

const schema = object({
  first_name: string().required(),
  last_name: string().required(),
  email: string().required().email(),
  password: string().required().min(parseInt(config.public.minPasswordLength)),
  password_confirmation: string()
    .required()
    .oneOf(
      [yupRef("password")],
      "Confirm password should be same as password.",
    ),
  terms_agreed: boolean().oneOf([true], "This is required field."),
});

const {
  handleSubmit,
  errors: validationErrors,
  setFieldValue,
} = useForm({
  validationSchema: schema,
});

// Set default value for terms_agreed checkbox to fix validation rule.
setFieldValue("terms_agreed", false);

const { value: firstName } = useField<string>("first_name");
const { value: lastName } = useField<string>("last_name");
const { value: email } = useField<string>("email");
const { value: password } = useField<string>("password");
const { value: passwordConfirmation } = useField<string>(
  "password_confirmation",
);
const { value: termsAgreed } = useField<boolean>("terms_agreed");

const onSubmit = handleSubmit(async (values) => {
  form.value.pending = true;
  try {
    const { errorCode } = await authenticationStore.register(
      values as registerParams,
    );
    if (errorCode === "unverified") {
      return navigateTo(config.public.routes.emailVerify);
    }
  } catch (error: any) {
    toastStore.addError(error?.data?.message || "Error processing request");
  } finally {
    form.value.pending = false;
  }
});
</script>
