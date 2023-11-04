<template>
  <div class="mb-6">
    <label class="text-base font-semibold text-gray-950 dark:text-gray-400">{{
      label
    }}</label>
    <div
      class="sm:flex"
      :class="[
        inline
          ? 'sm:items-center sm:space-x-4'
          : 'sm:flex-col sm:items-start sm:space-y-2',
      ]"
    >
      <div
        v-for="option in options"
        :key="option.value"
        class="flex items-center"
      >
        <input
          :id="`${id}-${option.value}`"
          :name="id"
          type="radio"
          class="h-4 w-4 border-primary-field-border text-indigo-600 focus:ring-indigo-600"
          :checked="option.value === modelValue"
          :aria-invalid="validationError ? true : false"
          :aria-describedby="validationError ? `${id}-error` : undefined"
          @click="emit('update:modelValue', option.value)"
        />
        <label
          :for="`${id}-${option.value}`"
          class="ml-2 block text-sm font-medium leading-6 text-gray-950 dark:text-gray-400"
          >{{ option.label }}</label
        >
      </div>
      <div
        v-if="validationError"
        :id="`${id}-error`"
        class="text-xs text-red-400 mt-1"
      >
        {{ formateValidationError(validationError) }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { PropType } from "vue";
import { radioOptions } from "@/types";

defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: "",
  },
  validationError: {
    type: String,
    default: "",
  },
  modelValue: {
    type: [String, Number, Boolean],
    default: "",
  },
  options: {
    type: Object as PropType<radioOptions>,
    default() {},
  },
  inline: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);
</script>
