<template>
  <Listbox
    :id="id"
    as="div"
    :model-value="modelValue"
    :multiple="multiple"
    :aria-invalid="validationError ? true : false"
    :aria-describedby="validationError ? `${id}-error` : undefined"
    @update:model-value="(value) => emit('update:modelValue', value)"
  >
    <ListboxLabel
      v-if="label"
      class="block text-base font-medium leading-6 text-gray-950 dark:text-gray-400"
      >{{ label }}</ListboxLabel
    >
    <div class="relative mt-2">
      <ListboxButton
        class="relative w-full cursor-default rounded-md dark:bg-gray-900 py-4 pl-4 pr-10 text-left text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-base sm:leading-6"
      >
        <span v-if="selectedOptionLabel" class="block truncate">{{
          selectedOptionLabel
        }}</span>
        <span v-else class="block truncate text-gray-900 dark:text-gray-300">{{
          placeholder
        }}</span>
        <span
          class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
        >
          <ChevronUpDownIcon
            class="h-5 w-5 text-gray-900 dark:text-gray-300"
            aria-hidden="true"
          />
        </span>
      </ListboxButton>

      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-out"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <ListboxOptions
          class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-gray-900 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-base"
        >
          <ListboxOption
            v-for="option in options"
            :key="option.value"
            v-slot="{ active, selected }"
            as="template"
            :value="option.value"
            :disabled="option?.disabled"
          >
            <li
              :class="[
                active
                  ? 'bg-indigo-600 text-white'
                  : 'text-gray-900 dark:text-gray-300',
                'relative cursor-default select-none py-2 pl-3 pr-9',
              ]"
            >
              <span
                :class="[
                  selected ? 'font-semibold' : 'font-normal',
                  'block truncate',
                ]"
                >{{ option.label }}</span
              >
              <span
                v-if="selected"
                :class="[
                  active
                    ? 'text-gray-900 dark:text-gray-300'
                    : 'text-indigo-400',
                  'absolute inset-y-0 right-0 flex items-center pr-4',
                ]"
              >
                <CheckIcon class="h-5 w-5" aria-hidden="true" />
              </span>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
      <div
        v-if="validationError"
        :id="`${id}-error`"
        class="text-xs text-red-400 mt-1"
      >
        {{ formateValidationError(validationError) }}
      </div>
    </div>
  </Listbox>
</template>

<script setup lang="ts">
import {
  Listbox,
  ListboxButton,
  ListboxLabel,
  ListboxOption,
  ListboxOptions,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import { PropType } from "vue";
import { selectOptions } from "@/types";

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: "",
  },
  options: {
    type: Array as PropType<selectOptions>,
    required: true,
  },
  modelValue: {
    type: [String, Number, Array],
    default: "",
  },
  placeholder: {
    type: String,
    default: "Select option",
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  validationError: {
    type: String,
    default: "",
  },
});
const emit = defineEmits(["update:modelValue"]);

const selectedOptionLabel = computed(() => {
  return props.options
    .filter((option) => {
      if (Array.isArray(props.modelValue)) {
        return props.modelValue.includes(option.value);
      }

      return props.modelValue === option.value;
    })
    .map((option) => option.label)
    .join(", ");
});
</script>
