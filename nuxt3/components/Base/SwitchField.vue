<template>
  <SwitchGroup as="div" class="flex items-start flex-col mb-6">
    <SwitchLabel
      as="label"
      class="mb-6 text-base text-gray-950 dark:text-gray-400"
    >
      {{ label }}
    </SwitchLabel>
    <Switch
      :id="id"
      :name="id"
      :model-value="modelValue"
      :class="[
        modelValue ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-950',
        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
      ]"
      @update:model-value="(value) => emit('update:modelValue', value)"
    >
      <span
        aria-hidden="true"
        :class="[
          modelValue ? 'translate-x-5' : 'translate-x-0',
          'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
        ]"
      />
    </Switch>
  </SwitchGroup>
</template>

<script setup lang="ts">
import { SwitchGroup, Switch, SwitchLabel } from "@headlessui/vue";

defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: "",
  },
  modelValue: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);
</script>
