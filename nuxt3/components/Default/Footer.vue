<template>
  <footer>
    <div class="mx-auto max-w-7xl overflow-hidden px-6 py-20 sm:py-24 lg:px-8">
      <nav
        class="-mb-6 columns-2 sm:flex sm:justify-center sm:space-x-12"
        aria-label="Footer"
      >
        <div v-for="item in getFooterMenu" :key="item.id" class="pb-6">
          <NuxtLink
            :to="item.url"
            :title="item.title"
            :target="item.target"
            :rel="item.rel"
            class="text-sm leading-6 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          >
            <BaseMenuLabel
              :label="item.label"
              :icon="item.icon"
              :position="item.icon_placement"
            />
          </NuxtLink>
        </div>
      </nav>
      <div class="mt-10 flex justify-center space-x-10">
        <a
          v-for="item in getSocialMenu"
          :key="item.id"
          :href="item.url"
          :title="item.title"
          :target="item.target"
          :rel="item.rel"
          class="text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-100"
        >
          <BaseMenuLabel
            :label="item.label"
            :icon="item.icon"
            :position="item.icon_placement"
          />
        </a>
      </div>
      <p class="mt-10 text-center text-xs leading-5 text-gray-500">
        {{ getSiteConfig("footer_copyright")?.value }}
      </p>
    </div>
  </footer>
</template>

<script setup lang="ts">
const getFooterMenu = getMenu("footer_menu");
const getSocialMenu = getMenu("social_menu");
</script>
