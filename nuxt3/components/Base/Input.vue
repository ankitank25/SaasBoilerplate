<template>
  <label
    v-if="label"
    :for="id"
    class="block text-base font-medium leading-6 text-gray-950 dark:text-gray-400"
    >{{ label }}</label
  >
  <div class="mb-6">
    <input
      :id="id"
      :name="id"
      :type="type"
      :value="modelValue"
      v-bind="$attrs"
      :required="required"
      :placeholder="placeholder"
      class="block w-full rounded-md border-0 my-2 py-2 px-4 text-base text-gray-900 dark:text-gray-300 dark:bg-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-400 outline-0 sm:leading-10 leading-10"
      :aria-invalid="validationError ? true : false"
      :aria-describedby="validationError ? `${id}-error` : undefined"
      @input="updateValue"
    />
    <div
      v-if="validationError"
      :id="`${id}-error`"
      class="text-xs text-red-400 mt-1"
    >
      {{ formateValidationError(validationError) }}
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  id: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: "text",
  },
  label: {
    type: String,
    default: "",
  },
  placeholder: {
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
    type: [String, Number],
    default: "",
  },
});
const emit = defineEmits(["update:modelValue"]);

const updateValue = (e: Event) => {
  emit("update:modelValue", (e.target as HTMLInputElement).value);
};
</script>
