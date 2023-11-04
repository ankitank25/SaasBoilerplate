<template>
  <div>Account Dashboard</div>
</template>
<script setup lang="ts">
definePageMeta({
  middleware: "user",
  layout: "account",
  title: "Dashboard",
});
</script>
