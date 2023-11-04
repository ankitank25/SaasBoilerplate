<template>
  <div>
    <AccountSpaceConfigurationForm />
    <AccountSpaceRoleList />
  </div>
</template>
<script setup lang="ts">
definePageMeta({
  middleware: "user",
  layout: "account",
  title: "Settings",
});
</script>
