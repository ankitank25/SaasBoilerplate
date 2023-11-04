<template>
  <div>
    <DefaultHeader />
    <main class="main">
      <slot />
    </main>
    <DefaultFooter />
  </div>
</template>
<script setup lang="ts">
useHead({
  script: [
    {
      children: getSiteConfig("head_script")?.value,
      type: "text/javascript",
    },
  ],
  style: [
    {
      children: getSiteConfig("head_css")?.value,
    },
  ],
});
</script>
