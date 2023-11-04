<template>
  <div class="text-gray-400 dark:text-gray-400">
    <ComputerDesktopIcon
      v-show="$colorMode.preference == 'system'"
      class="w-6 cursor-pointer"
      title="Switch to dark color mode"
      @click.prevent="$colorMode.preference = 'light'"
    />
    <SunIcon
      v-show="$colorMode.preference == 'light'"
      class="w-6 cursor-pointer"
      title="Switch to system color preference"
      @click.prevent="$colorMode.preference = 'dark'"
    />
    <MoonIcon
      v-show="$colorMode.preference == 'dark'"
      class="w-6 cursor-pointer"
      title="Switch to light color mode"
      @click.prevent="$colorMode.preference = 'system'"
    />
  </div>
</template>
<script setup>
import {
  SunIcon,
  MoonIcon,
  ComputerDesktopIcon,
} from "@heroicons/vue/24/outline";
</script>
