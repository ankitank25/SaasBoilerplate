<template>
  <div>
    Admin Dashboard

    <BaseButton id="add-toast" :type="buttonTypes.BUTTON" @click="addToast"
      >Add Toast</BaseButton
    >
  </div>
</template>
<script setup lang="ts">
import { buttonTypes } from "@/types";
import { useToastStore } from "@/stores/toast";

definePageMeta({
  title: "Admin dashboard",
  middleware: "admin",
  layout: "admin",
});

const toast = useToastStore();

const addToast = () => {
  toast.addSuccess("Success");
  toast.addWarning("Warning");
  toast.addError("Error");
};
</script>
