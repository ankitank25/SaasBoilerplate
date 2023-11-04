<template>
  <div class="text-gray-700 dark:text-gray-300">
    <AdminSidebar />
    <div class="lg:pl-72">
      <AdminHeader :title="route.meta.title" />
      <main class="py-10 bg-gray-50 dark:bg-gray-800 min-h-screen">
        <div class="px-4 sm:px-6 lg:px-8">
          <slot></slot>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute();

const config = useRuntimeConfig();

useHead({
  titleTemplate: () => {
    return route.meta.title
      ? `${route.meta.title} - ${config.public.appName}`
      : config.public.appName;
  },
});
</script>
