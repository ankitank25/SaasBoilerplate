<template>
  <div
    class="flex min-h-full flex-1 flex-col justify-center py-12 sm:px-6 lg:px-8"
  >
    <Head>
      <Title>Verify email</Title>
    </Head> 
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <NuxtLink to="/">
        <BaseLogo />
      </NuxtLink>
      <h2
        class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-950 dark:text-gray-400"
      >
        Verify your email address
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
      <div
        class="bg-white dark:bg-slate-800 px-6 py-12 shadow sm:rounded-lg sm:px-12"
      >
        <p class="mb-4">
          You must recieved an email with verification link. Check verification
          email in your mailbox for further instructions to verify your email
          address.
        </p>
        <p class="mb-4">
          You can re-send verification email by clicking 'Resend Verification
          Email' button bellow.
        </p>
        <BaseButton
          id="re-send-email-btn"
          class="w-full mb-5"
          :type="buttonTypes.BUTTON"
          :processing="form.pending"
          @click="resendEmail"
          >Re-Send verification email</BaseButton
        >
        <BaseButton
          id="logout-btn"
          class="w-full"
          :type="buttonTypes.BUTTON"
          @click="authenticationStore.logout()"
          >Logout</BaseButton
        >
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { apiResponse, buttonTypes } from "@/types";
import { useAuthenticationStore } from "@/stores/authentication";
import { useToastStore } from "@/stores/toast";

const authenticationStore = useAuthenticationStore();
const toastStore = useToastStore();

definePageMeta({
  middleware: ["site-config", "email-verify"],
});

const form = ref({
  pending: false,
});

const resendEmail = async () => {
  form.value.pending = true;
  try {
    const { success, message } = await useFetchApi<apiResponse>(
      "/auth/email/resend-verification",
      {
        method: "GET",
      },
    );
    if (success && message) {
      toastStore.addSuccess(message);
    }
  } catch (error: any) {
    toastStore.addError(error?.data?.message || "Error processing request");
  } finally {
    form.value.pending = false;
  }
};
</script>
