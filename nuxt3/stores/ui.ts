import { defineStore } from "pinia";
import { Settings } from "@/types";

export const useUiStore = defineStore("uiStore", () => {
  const sidebar = ref<boolean>(false);

  const siteConfig = ref<Settings>();

  const closeSidebar = () => {
    sidebar.value = false;
  };

  const openSidebar = () => {
    sidebar.value = true;
  };

  const setSiteConfig = (config: Settings) => {
    siteConfig.value = config;
  };

  return {
    sidebar,
    closeSidebar,
    openSidebar,
    siteConfig,
    setSiteConfig,
  };
});
