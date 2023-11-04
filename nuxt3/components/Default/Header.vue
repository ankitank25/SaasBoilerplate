<template>
  <header>
    <nav
      class="mx-auto flex max-w-7xl items-center justify-between gap-x-6 p-6 lg:px-8"
      aria-label="Global"
    >
      <div class="flex lg:flex-1">
        <NuxtLink to="/" class="-m-1.5 p-1.5">
          <BaseLogo />
        </NuxtLink>
      </div>
      <div class="hidden lg:flex lg:gap-x-12">
        <NuxtLink
          v-for="item in headerMenu"
          :key="item.id"
          :to="item.url"
          :title="item.title"
          :target="item.target"
          :rel="item.rel"
          class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          ><BaseMenuLabel
            :label="item.label"
            :icon="item.icon"
            :position="item.icon_placement"
        /></NuxtLink>
      </div>
      <div class="flex flex-1 items-center justify-end gap-x-6">
        <BaseColorMode />
        <template v-if="authenticaionStore.getToken">
          <NuxtLink
            to="#"
            class="hidden lg:block lg:text-sm lg:font-semibold lg:leading-6 lg:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
            @click.prevent="authenticaionStore.logout()"
            >Sign out</NuxtLink
          >
          <NuxtLink
            to="/account"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Dashboard</NuxtLink
          >
        </template>
        <template v-else>
          <NuxtLink
            to="/login"
            class="hidden lg:block lg:text-sm lg:font-semibold lg:leading-6 lg:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
            >Sign in</NuxtLink
          >
          <NuxtLink
            to="/register"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Sign up</NuxtLink
          >
        </template>
      </div>
      <div class="flex lg:hidden">
        <button
          type="button"
          class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
          @click="mobileMenuOpen = true"
        >
          <span class="sr-only">Open main menu</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>
      </div>
    </nav>
    <ClientOnly>
      <Dialog
        as="div"
        class="lg:hidden"
        :open="mobileMenuOpen"
        @close="mobileMenuOpen = false"
      >
        <div class="fixed inset-0 z-10" />
        <DialogPanel
          class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white dark:bg-gray-900 px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
        >
          <div class="flex items-center gap-x-6">
            <NuxtLink to="/" class="-m-1.5 p-1.5">
              <BaseLogo />
            </NuxtLink>
            <a
              href="#"
              class="ml-auto rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
              >Sign up</a
            >
            <button
              type="button"
              class="-m-2.5 rounded-md p-2.5 text-gray-700"
              @click="mobileMenuOpen = false"
            >
              <span class="sr-only">Close menu</span>
              <XMarkIcon class="h-6 w-6" aria-hidden="true" />
            </button>
          </div>
          <div class="mt-6 flow-root">
            <div class="-my-6 divide-y divide-gray-500/10">
              <div class="space-y-2 py-6">
                <NuxtLink
                  v-for="item in headerMenu"
                  :key="item.id"
                  :to="item.url"
                  :title="item.title"
                  :target="item.target"
                  :rel="item.rel"
                  class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-100"
                  ><BaseMenuLabel
                    :label="item.label"
                    :icon="item.icon"
                    :position="item.icon_placement"
                /></NuxtLink>
              </div>
              <div class="py-6">
                <template v-if="authenticaionStore.getToken">
                  <NuxtLink
                    to="#"
                    class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                    @click.prevent="authenticaionStore.logout()"
                    >Sign out</NuxtLink
                  >
                  <NuxtLink
                    :to="config.public.routes.account"
                    class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                    >Dashboard</NuxtLink
                  >
                </template>
                <template v-else>
                  <NuxtLink
                    :to="config.public.routes.login"
                    class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                    >Sign in</NuxtLink
                  >
                  <NuxtLink
                    :to="config.public.routes.register"
                    class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                    >Sign up</NuxtLink
                  >
                </template>
              </div>
            </div>
          </div>
        </DialogPanel>
      </Dialog>
    </ClientOnly>
  </header>
</template>

<script setup>
import { ref } from "vue";
import { Dialog, DialogPanel } from "@headlessui/vue";
import { Bars3Icon, XMarkIcon } from "@heroicons/vue/24/outline";
import { useAuthenticationStore } from "@/stores/authentication";

const config = useRuntimeConfig();
const authenticaionStore = useAuthenticationStore();

const headerMenu = getMenu("header_menu");

const mobileMenuOpen = ref(false);
</script>
