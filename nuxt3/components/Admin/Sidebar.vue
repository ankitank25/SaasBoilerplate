<template>
  <TransitionRoot as="template" :show="uiStore.sidebar">
    <ClientOnly>
      <Dialog
        as="div"
        class="relative z-50 lg:hidden"
        @close="uiStore.closeSidebar()"
      >
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 dark:bg-gray-900/80" />
        </TransitionChild>

        <div class="fixed inset-0 flex">
          <TransitionChild
            as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
          >
            <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
              <TransitionChild
                as="template"
                enter="ease-in-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in-out duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0"
              >
                <div
                  class="absolute left-full top-0 flex w-16 justify-center pt-5"
                >
                  <button
                    type="button"
                    class="-m-2.5 p-2.5"
                    @click="uiStore.closeSidebar()"
                  >
                    <span class="sr-only">Close sidebar</span>
                    <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                  </button>
                </div>
              </TransitionChild>
              <!-- Sidebar component, swap this element with another sidebar if you like -->
              <div
                class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-50 dark:bg-gray-900 px-6 pb-4 ring-1 ring-white/10"
              >
                <div class="flex h-16 shrink-0 items-center justify-center">
                  <NuxtLink to="/admin" @click="uiStore.closeSidebar()">
                    <BaseLogo />
                  </NuxtLink>
                </div>
                <nav class="flex flex-1 flex-col">
                  <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                      <ul role="list" class="-mx-2 space-y-1">
                        <li v-for="item in navigation" :key="item.name">
                          <NuxtLink
                            :href="item.href"
                            active-class="bg-gray-800 text-white"
                            class="text-gray-400 hover:text-white hover:bg-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                            @click="uiStore.closeSidebar()"
                          >
                            <component
                              :is="item.icon"
                              class="h-6 w-6 shrink-0"
                              aria-hidden="true"
                            />
                            {{ item.name }}
                          </NuxtLink>
                        </li>
                      </ul>
                    </li>
                    <li class="mt-auto">
                      <NuxtLink
                        to="/admin/settings"
                        class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white"
                        @click="uiStore.closeSidebar()"
                      >
                        <Cog6ToothIcon
                          class="h-6 w-6 shrink-0"
                          aria-hidden="true"
                        />
                        Settings
                      </NuxtLink>
                    </li>
                  </ul>
                </nav>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </ClientOnly>
  </TransitionRoot>

  <!-- Static sidebar for desktop -->
  <div
    class="hidden border-r dark:border-gray-700 border-gray-200 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col"
  >
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div
      class="flex grow flex-col gap-y-5 overflow-y-auto dark:bg-gray-900 px-6 pb-4"
    >
      <div class="flex h-16 shrink-0 items-center justify-center">
        <NuxtLink to="/admin">
          <BaseLogo />
        </NuxtLink>
      </div>
      <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
          <li>
            <ul role="list" class="-mx-2 space-y-1">
              <li v-for="item in navigation" :key="item.name">
                <NuxtLink
                  :to="item.href"
                  active-class="bg-gray-700 dark:bg-gray-800 text-white"
                  class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                >
                  <component
                    :is="item.icon"
                    class="h-6 w-6 shrink-0"
                    aria-hidden="true"
                  />
                  {{ item.name }}
                </NuxtLink>
              </li>
            </ul>
          </li>
          <li class="mt-auto">
            <NuxtLink
              to="/admin/settings"
              active-class="bg-gray-700 dark:bg-gray-800 text-white"
              class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 dark:text-gray-400 hover:bg-gray-800 hover:text-white"
            >
              <Cog6ToothIcon class="h-6 w-6 shrink-0" aria-hidden="true" />
              Settings
            </NuxtLink>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script setup lng="ts">
import {
  Dialog,
  DialogPanel,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import {
  Cog6ToothIcon,
  HomeIcon,
  UsersIcon,
  XMarkIcon,
} from "@heroicons/vue/24/outline";
import { useUiStore } from "@/stores/ui";

const uiStore = useUiStore();

const navigation = [
  { name: "Dashboard", href: "/admin", icon: HomeIcon },
  { name: "Pages", href: "/admin/pages", icon: UsersIcon },
  { name: "Menus", href: "/admin/menu", icon: UsersIcon },
];
</script>
