<template>
  <button
    :id="id"
    :type="type"
    :disabled="processing || disabled"
    class="rounded-md bg-indigo-600 p-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:pointer-events-none"
    @click="onClick"
  >
    <slot v-if="!processing" />
    <Icon v-else name="svg-spinners:ring-resize" size="24" />
  </button>
</template>

<script setup lang="ts">
import { PropType } from "vue";
import { buttonTypes } from "@/types";

defineProps({
  id: {
    type: String,
    required: true,
  },
  type: {
    type: String as PropType<buttonTypes>,
    default: buttonTypes.BUTTON,
  },
  processing: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  style: {
    type: String,
    default: "indigo",
  },
});

const emit = defineEmits(["click"]);

const onClick = () => {
  emit("click");
};
</script>
