import { defineStore } from "pinia";
import { toastTypes, Toast } from "@/types";

export const useToastStore = defineStore("toast", () => {
  const toasts = ref<Toast[]>([]);

  const addToast = (
    message: string,
    type: toastTypes,
    append: boolean,
  ): void => {
    if (!append) {
      toasts.value = [
        {
          message,
          type,
        },
      ];
    } else {
      toasts.value.push({
        message,
        type,
      });
    }
  };

  const addSuccess = (message: string, append = true): void => {
    addToast(message, toastTypes.SUCCESS, append);
  };

  const addWarning = (message: string, append = true): void => {
    addToast(message, toastTypes.WARNING, append);
  };

  const addError = (message: string, append = true): void => {
    addToast(message, toastTypes.ERROR, append);
  };

  const removeToast = (toast: Toast): void => {
    const id = toasts.value.indexOf(toast);
    if (id > -1) {
      toasts.value.splice(id, 1);
    }
  };

  const getToasts = computed((): Toast[] => {
    return toasts.value;
  });

  return {
    addSuccess,
    addWarning,
    addError,
    getToasts,
    removeToast,
  };
});
