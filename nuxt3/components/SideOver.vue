<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-40" @close="onClose">
      <div class="fixed inset-0" />
      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div
            class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16"
          >
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-500 sm:duration-500"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leave-from="translate-x-0"
              leave-to="translate-x-full"
            >
              <DialogPanel class="pointer-events-auto w-screen max-w-7xl">
                <div
                  class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-gray-800 border-l border-gray-300 dark:border-gray-600 shadow-xl"
                >
                  <div class="flex-1">
                    <!-- Header -->
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-4 sm:px-6">
                      <div class="flex items-start justify-between space-x-3">
                        <div class="space-y-1">
                          <DialogTitle
                            class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-300"
                            ><slot name="title"
                          /></DialogTitle>
                          <slot name="message" />
                        </div>
                        <div class="flex h-7 items-center">
                          <button
                            type="button"
                            class="text-gray-400 hover:text-gray-500"
                            @click="onClose"
                          >
                            <span class="sr-only">Close panel</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="px-6 py-4">
                      <slot />
                    </div>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
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
import { XMarkIcon } from "@heroicons/vue/24/outline";

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close"]);

const onClose = () => {
  emit("close");
};
</script>
