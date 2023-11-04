<template>
  <div class="mb-6">
    <div class="flex items-center justify-between mb-3">
      <h2
        class="block text-base font-medium leading-6 text-gray-950 dark:text-gray-400"
      >
        {{ label }}
      </h2>
    </div>
    <RadioGroup
      :model-value="modelValue"
      class="mt-2"
      @update:model-value="(value) => emit('update:modelValue', value)"
    >
      <RadioGroupLabel class="sr-only">{{ label }}</RadioGroupLabel>
      <div
        class="grid gap-2"
        :class="[inline ? 'grid-flow-col auto-cols-max' : '']"
      >
        <RadioGroupOption
          v-for="option in options"
          :key="option.value"
          v-slot="{ active, checked }"
          as="template"
          :value="option.value"
          :disabled="option.disabled"
        >
          <div
            :class="[
              !option.disabled
                ? 'cursor-pointer focus:outline-none'
                : 'cursor-not-allowed opacity-25',
              active ? 'ring-2 ring-indigo-600 ring-offset-2' : '',
              checked
                ? 'bg-indigo-600 text-white hover:bg-indigo-500'
                : 'ring-1 ring-inset ring-gray-300 bg-white text-gray-950 hover:bg-gray-50',
              inline ? 'justify-center' : 'justify-start',
              'flex items-center rounded-md py-3 px-3 text-sm font-semibold uppercase sm:flex-1',
            ]"
          >
            <RadioGroupLabel>
              {{ option.label }}
              <RadioGroupDescription
                v-if="style == radioStyles.CARDS && option.description"
                as="span"
                class="mt-1 flex items-center text-sm"
                :class="[active ? 'text-gray-200' : 'text-gray-900']"
                >{{ option.description }}</RadioGroupDescription
              >
            </RadioGroupLabel>
          </div>
        </RadioGroupOption>
      </div>
    </RadioGroup>
  </div>
</template>

<script setup lang="ts">
import { PropType } from "vue";
import {
  RadioGroup,
  RadioGroupLabel,
  RadioGroupOption,
  RadioGroupDescription,
} from "@headlessui/vue";
import { radioStyles, radioOptions } from "@/types";

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
  style: {
    type: String as PropType<radioStyles>,
    default: "small-cards",
  },
});

const emit = defineEmits(["update:modelValue"]);
</script>
