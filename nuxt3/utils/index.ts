import { Setting } from "@/types";
import { RouteLocationNormalized } from "~/.nuxt/vue-router";
import { useUiStore } from "~/stores/ui";
import { useUserStore } from "~/stores/user";

export const formateValidationError = (
  errorMessage: string | undefined,
): string | undefined => {
  return errorMessage
    ? errorMessage.charAt(0).toUpperCase() +
        errorMessage.slice(1).replace("_", " ")
    : "";
};

export const getInitials = (name: string) => {
  const parts = name.split(" ");
  let initials = "";
  for (let i = 0; i < parts.length; i++) {
    if (parts[i].length > 0 && parts[i] !== "") {
      initials += parts[i][0];
    }
  }
  return initials;
};

export const getSiteConfig = (name: string): Setting | null => {
  const { siteConfig } = useUiStore();
  return (
    (siteConfig &&
      siteConfig.config.find((setting: Setting) => setting.name === name)) ||
    null
  );
};

export const getSiteResource = (name: string): any => {
  const { siteConfig } = useUiStore();

  if (siteConfig && siteConfig.resources) {
    type ObjectKey = keyof typeof siteConfig.resources;

    if (Object.prototype.hasOwnProperty.call(siteConfig.resources, name)) {
      return siteConfig.resources[name as ObjectKey];
    }
  }

  return null;
};

export const getMenu = (menuId: string) => {
  const menuResource = getSiteResource(menuId);
  const menuItems = JSON.parse(menuResource);
  if (Array.isArray(menuItems) && menuItems.length > 0) {
    return menuItems.filter((menuItem) => menuItem.status === true);
  }
};

export const getUserMeta = (name: string): string | null | undefined => {
  const userStore = useUserStore();
  const user = userStore.getUser;

  if (name) {
    return user?.meta[name];
  }
};

export const setReferral = (_to: RouteLocationNormalized) => {
  return { referral: _to.fullPath };
};

export const getReferral = (): string | undefined => {
  return useRoute().query.referral?.toString();
};
