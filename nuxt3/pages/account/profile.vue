<template>
  <Profile />
</template>
<script setup lang="ts">
definePageMeta({
  middleware: "user",
  layout: "account",
  title: "Profile",
});
</script>
