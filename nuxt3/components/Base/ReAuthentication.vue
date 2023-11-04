<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-10" @close="onCancel">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div
          class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
        >
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
            >
              <form @submit.prevent="onReAuthenticate()">
                <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                  <button
                    type="button"
                    class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    @click="onCancel"
                  >
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                  </button>
                </div>
                <div class="sm:items-start">
                  <div class="flex items-center mb-5">
                    <div
                      class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-transparent sm:mx-0 sm:h-10 sm:w-10"
                    >
                      <ShieldExclamationIcon
                        class="h-6 w-6 text-red-600"
                        aria-hidden="true"
                      />
                    </div>
                    <DialogTitle
                      as="h3"
                      class="text-base font-semibold leading-6 text-gray-700 dark:text-gray-300 ml-4"
                      >{{ title }}</DialogTitle
                    >
                  </div>
                  <div class="mt-3 text-left">
                    <div class="mt-2 mb-5">
                      <BaseInput
                        id="re-auth-password"
                        v-model="password"
                        type="password"
                        label="Password"
                        placeholder="Password"
                        :validation-error="validationErrors.password"
                        autofocus
                      />
                    </div>
                  </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex">
                  <BaseButton
                    id="re-auth-cancel-btn"
                    class="mr-2"
                    :type="buttonTypes.BUTTON"
                    @click="onCancel()"
                    >Cancel</BaseButton
                  >
                  <BaseButton id="re-auth-btn" :type="buttonTypes.SUBMIT"
                    >Re-Authenticate</BaseButton
                  >
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { ShieldExclamationIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { useForm, useField } from "vee-validate";
import { string, object } from "yup";
import { buttonTypes, loginParams } from "@/types";
import { useAuthenticationStore } from "@/stores/authentication";
import { useToastStore } from "@/stores/toast";

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: "",
  },
});

const authenticationStore = useAuthenticationStore();
const toastStore = useToastStore();

const user = authenticationStore.getUser;

const schema = object({
  email: string().required().email(),
  password: string().required().min(8),
});

const {
  handleSubmit,
  errors: validationErrors,
  setFieldValue,
  resetForm,
} = useForm({
  validationSchema: schema,
});

useField<string | undefined>("email");
const { value: password } = useField<string | undefined>("password");

setFieldValue("email", user?.email);

const onReAuthenticate = handleSubmit(async (values) => {
  try {
    const { success, message } = await authenticationStore.reAuthenticate(
      values as loginParams,
    );
    if (success === true) {
      toastStore.addSuccess(message);
      emit("cancel");
      emit("success", true);
      resetForm({
        values: {
          email: user?.email,
          password: "",
        },
      });
    }
  } catch (error: any) {
    toastStore.addError(error?.data?.message || "Error processing the request");
  }
});

const emit = defineEmits(["cancel", "success"]);

const onCancel = () => {
  emit("cancel");
};
</script>
