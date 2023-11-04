<template>
  <div
    aria-live="assertive"
    class="fixed flex w-full max-w-lg items-end px-4 py-6 sm:items-start sm:p-6 bottom-0 right-0 z-50"
  >
    <div class="w-full">
      <TransitionGroup name="list" tag="div" mode="out-in">
        <div
          v-for="toast in toastStore.getToasts"
          :key="toast.message"
          class="mb-2"
        >
          <div
            class="border pointer-events-auto w-full overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
          >
            <div class="p-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <CheckCircleIcon
                    v-if="toast.type == toastTypes.SUCCESS"
                    class="h-6 w-6"
                    :class="colorVariants[toast.type as colorVariantTypes].icon"
                    aria-hidden="true"
                  />
                  <XCircleIcon
                    v-else-if="toast.type == toastTypes.ERROR"
                    class="h-6 w-6"
                    :class="colorVariants[toast.type as colorVariantTypes].icon"
                    aria-hidden="true"
                  />
                  <ExclamationTriangleIcon
                    v-else-if="toast.type == toastTypes.WARNING"
                    class="h-6 w-6"
                    :class="colorVariants[toast.type as colorVariantTypes].icon"
                    aria-hidden="true"
                  />
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                  <p
                    class="text-sm font-medium text-gray-900 break-words"
                    v-html="toast.message"
                  ></p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                  <button
                    type="button"
                    class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    @click.prevent="toastStore.removeToast(toast)"
                  >
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  ExclamationTriangleIcon,
  XCircleIcon,
  CheckCircleIcon,
  XMarkIcon,
} from "@heroicons/vue/20/solid";
import { toastTypes } from "@/types";
import { useToastStore } from "@/stores/toast";

const colorVariants = {
  green: {
    background: "bg-green-50 border-green-500",
    icon: "text-green-400",
    text: "text-green-700",
  },
  red: {
    background: "bg-red-50 border-red-500",
    icon: "text-red-400",
    text: "text-red-700",
  },
  yellow: {
    background: "bg-yellow-50 border-yellow-500",
    icon: "text-yellow-400",
    text: "text-yellow-700",
  },
};

type colorVariantTypes = keyof typeof colorVariants;

const toastStore = useToastStore();
</script>
<style>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
