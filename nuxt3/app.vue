<template>
  <div class="bg-gray-100 dark:bg-gray-900 min-h-screen">
    <NuxtLayout>
      <NuxtLoadingIndicator />
      <NuxtPage />
    </NuxtLayout>
    <Teleport to="body">
      <BaseToast />
    </Teleport>
  </div>
</template>
