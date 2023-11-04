<template>
  <div
    class="sticky top-0 z-40 bg-gray-100 dark:bg-gray-900 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 dark:border-gray-600 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8"
  >
    <button
      type="button"
      class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 lg:hidden"
      @click="uiStore.sidebar = true"
    >
      <span class="sr-only">Open sidebar</span>
      <Bars3Icon class="h-6 w-6" aria-hidden="true" />
    </button>

    <!-- Separator -->
    <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true" />

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
      <div
        class="relative flex flex-1 items-center md:text-2xl sm:text-xl xs:text-base"
      >
        {{ title }}
      </div>
      <div class="flex items-center gap-x-4 lg:gap-x-6">
        <BaseColorMode />
        <button
          type="button"
          class="-m-2.5 p-2.5 text-gray-400 dark:text-gray-300 hover:text-gray-500"
          @click.prevent="openNotifications = true"
        >
          <span class="sr-only">View notifications</span>
          <BellIcon class="h-6 w-6" aria-hidden="true" />
        </button>
        <Modal :open="openNotifications" @close="openNotifications = false">
          <template #content>
            <BaseNotification />
          </template>
        </Modal>
        <!-- Separator -->
        <div
          class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-300 dark:lg:bg-gray-500"
          aria-hidden="true"
        />

        <!-- Profile dropdown -->
        <Menu v-slot="{ open }" as="div" class="relative">
          <MenuButton class="-m-1.5 flex items-center p-1.5">
            <span class="sr-only">Open user menu</span>
            <span
              class="px-2 py-1.5 w-9 ring-1 rounded-full"
              :class="
                open
                  ? 'ring-slate-700 text-white bg-slate-700'
                  : 'ring-slate-700 bg-white text-slate-700'
              "
              >{{ getInitials(`${user.first_name} ${user.last_name}`) }}</span
            >
            <span class="hidden lg:flex lg:items-center">
              <span
                class="ml-2 text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300"
                aria-hidden="true"
                >{{ user.first_name }} {{ user.last_name }}</span
              >
              <ChevronDownIcon
                class="ml-2 h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </span>
          </MenuButton>
          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <MenuItems
              class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
            >
              <MenuItem v-slot="{ active }">
                <NuxtLink
                  to="/admin/profile"
                  :class="[
                    active ? 'bg-gray-50' : '',
                    'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-700 bg-white dark:bg-gray-900',
                  ]"
                  >Profile</NuxtLink
                >
              </MenuItem>
              <MenuItem>
                <a
                  href="#"
                  class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-700 bg-white dark:bg-gray-900"
                  @click.prevent="authenticationStore.logout()"
                  >Logout</a
                >
              </MenuItem>
            </MenuItems>
          </transition>
        </Menu>
      </div>
    </div>
  </div>
</template>
<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { Bars3Icon, BellIcon } from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import { useAuthenticationStore } from "@/stores/authentication";
import { useUiStore } from "@/stores/ui";

defineProps({
  title: {
    type: String,
    default: "",
  },
});

const authenticationStore = useAuthenticationStore();
const uiStore = useUiStore();

const openNotifications = ref(false);

const user = authenticationStore.getUser;
</script>
