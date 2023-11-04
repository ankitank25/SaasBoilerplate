<template>
  <Listbox
    :id="id"
    as="div"
    :model-value="modelValue"
    @update:model-value="(value) => emit('update:modelValue', value)"
  >
    <ListboxLabel class="sr-only">Change published status</ListboxLabel>
    <div class="relative">
      <div class="inline-flex divide-x divide-indigo-700 rounded-md shadow-sm">
        <div
          class="inline-flex items-center gap-x-1.5 rounded-l-md bg-indigo-600 px-3 py-2 text-white shadow-sm"
        >
          <CheckIcon class="-ml-0.5 h-5 w-5" aria-hidden="true" />
          <p class="text-sm font-semibold">{{ selectedOptionLabel }}</p>
        </div>
        <ListboxButton
          class="inline-flex items-center rounded-l-none rounded-r-md bg-indigo-600 p-2 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-gray-50"
        >
          <span class="sr-only">Change published status</span>
          <ChevronDownIcon class="h-5 w-5 text-white" aria-hidden="true" />
        </ListboxButton>
      </div>

      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <ListboxOptions
          class="absolute right-0 z-10 mt-2 w-72 origin-top-right divide-y divide-gray-200 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
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
                active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                'cursor-default select-none p-4 text-sm',
              ]"
            >
              <div class="flex flex-col">
                <div class="flex justify-between">
                  <p :class="selected ? 'font-semibold' : 'font-normal'">
                    {{ option.label }}
                  </p>
                  <span
                    v-if="selected"
                    :class="active ? 'text-white' : 'text-indigo-600'"
                  >
                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                  </span>
                </div>
              </div>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
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
import { CheckIcon, ChevronDownIcon } from "@heroicons/vue/20/solid";
import { PropType } from "vue";
import { selectOptions } from "@/types";

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  options: {
    type: Array as PropType<selectOptions>,
    required: true,
  },
  modelValue: {
    type: [String, Number],
    default: "",
  },
});
const emit = defineEmits(["update:modelValue"]);

const selectedOptionLabel = computed(() => {
  return props.options
    .filter((option) => {
      return props.modelValue === option.value;
    })
    .map((option) => option.label)
    .join(", ");
});
</script>
