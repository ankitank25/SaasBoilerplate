<template>
  <span v-if="position === 'before-label'">
    <Icon v-if="icon" class="mr-1" :name="icon" size="24" />
    {{ label }}
  </span>
  <span v-else-if="position === 'after-label'">
    {{ label }}
    <Icon v-if="icon" class="ml-1" :name="icon" size="24" />
  </span>
  <span v-else-if="position === 'only-icon'">
    <Icon v-if="icon" :name="icon" size="24" />
  </span>
  <span v-else>
    {{ label }}
  </span>
</template>
<script setup lang="ts">
defineProps({
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    default: "",
  },
  position: {
    type: String,
    default: "before-label",
  },
});
</script>
