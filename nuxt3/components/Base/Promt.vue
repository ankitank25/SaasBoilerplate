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
          class="fixed inset-0 z-40 bg-gray-500 bg-opacity-75 transition-opacity"
        />
      </TransitionChild>

      <div class="fixed inset-0 z-50 overflow-y-auto">
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
                    <p class="text-base text-gray-500">
                      <slot></slot>
                    </p>
                  </div>
                </div>
              </div>
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <BaseButton
                  id="promt-btn-yes"
                  class="w-16 bg-red-700 hover:bg-red-600"
                  :type="buttonTypes.BUTTON"
                  @click="onConfirm"
                  >Yes</BaseButton
                >
                <BaseButton
                  id="promt-btn-no"
                  class="w-16 bg-white hover:bg-gray-300 text-black border border-gray-600 mr-4"
                  :type="buttonTypes.BUTTON"
                  @click="onCancel"
                  >No</BaseButton
                >
              </div>
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
import { buttonTypes } from "@/types";

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: "Are you sure to perform this action?",
  },
});

const emit = defineEmits(["cancel", "confirm"]);

const onCancel = () => {
  emit("cancel");
};

const onConfirm = () => {
  emit("confirm");
};
</script>
