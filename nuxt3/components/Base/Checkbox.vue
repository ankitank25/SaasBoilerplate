<template>
  <input
    :id="id"
    :name="id"
    type="checkbox"
    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
    :checked="modelValue"
    v-bind="$attrs"
    :required="required"
    :aria-invalid="validationError ? true : false"
    :aria-describedby="validationError ? `${id}-error` : undefined"
    @change="updateValue"
  />
  <label
    v-if="label"
    :for="id"
    class="inline text-base ml-2 font-medium leading-6 text-gray-950 dark:text-gray-400"
    >{{ label }}</label
  >
  <div
    v-if="validationError"
    :id="`${id}-error`"
    class="text-xs text-red-400 mt-1"
  >
    {{ formateValidationError(validationError) }}
  </div>
</template>

<script setup lang="ts">
defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: "",
  },
  required: {
    type: Boolean,
    default: false,
  },
  validationError: {
    type: String,
    default: "",
  },
  modelValue: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(["update:modelValue"]);

const updateValue = (e: Event) => {
  emit("update:modelValue", (e.target as HTMLInputElement).checked);
};
</script>
