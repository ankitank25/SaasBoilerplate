<template>
  <div>
    <p class="text-sm text-gray-700 dark:text-gray-400">
      <CheckIcon
        v-if="password.length >= passwordLength"
        class="w-5 inline text-green-600"
      />
      <XMarkIcon v-else class="w-5 inline text-red-500" />
      Longer than {{ passwordLength }} characters
    </p>
    <p class="text-sm text-gray-700 dark:text-gray-400">
      <CheckIcon v-if="data.has_uppercase" class="w-5 inline text-green-600" />
      <XMarkIcon v-else class="w-5 inline text-red-500" />
      Has a capital letter
    </p>
    <p class="text-sm text-gray-700 dark:text-gray-400">
      <CheckIcon v-if="data.has_lowercase" class="w-5 inline text-green-600" />
      <XMarkIcon v-else class="w-5 inline text-red-500" />
      Has a lowercase letter
    </p>
    <p class="text-sm text-gray-700 dark:text-gray-400">
      <CheckIcon v-if="data.has_number" class="w-5 inline text-green-600" />
      <XMarkIcon v-else class="w-5 inline text-red-500" />
      Has a number
    </p>
    <p class="text-sm text-gray-700 dark:text-gray-400">
      <CheckIcon v-if="data.has_special" class="w-5 inline text-green-600" />
      <XMarkIcon v-else class="w-5 inline text-red-500" />
      Has a special character
    </p>
  </div>
</template>
<script setup lang="ts">
import { CheckIcon, XMarkIcon } from "@heroicons/vue/20/solid";

const passwordLength = parseInt(useRuntimeConfig().public.minPasswordLength);

const props = defineProps({
  password: {
    type: String,
    default: "",
  },
});

const data = ref({
  message: "",
  has_number: false,
  has_lowercase: false,
  has_uppercase: false,
  has_special: false,
});

watchEffect(() => {
  data.value.has_number = /\d/.test(props.password);
  data.value.has_lowercase = /[a-z]/.test(props.password);
  data.value.has_uppercase = /[A-Z]/.test(props.password);
  data.value.has_special = /[!@#\$%\^\&*\)\(+=._-]/.test(props.password);
});
</script>
