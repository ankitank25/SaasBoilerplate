import { useAuthenticationStore } from "~/stores/authentication";

export default defineNuxtRouteMiddleware((_to, _from) => {
  const config = useRuntimeConfig();
  const authenticationStore = useAuthenticationStore();

  if (authenticationStore.getToken) {
    return navigateTo(config.public.routes.account);
  }
});
