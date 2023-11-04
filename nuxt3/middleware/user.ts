import { useAuthenticationStore } from "~/stores/authentication";
import { useSpaceStore } from "~/stores/space";

export default defineNuxtRouteMiddleware(async (_to, _from) => {
  const config = useRuntimeConfig();
  const authenticationStore = useAuthenticationStore();

  if (!authenticationStore.getToken) {
    return navigateTo({
      path: config.public.routes.login,
      query: setReferral(_to),
    });
  }

  try {
    const { data } = await authenticationStore.isAuthenticated();
    if (data.user.role === "admin") {
      return navigateTo(config.public.routes.admin);
    }
    const spaceStore = useSpaceStore();
    await spaceStore.loadSpaces();
  } catch (error: any) {
    if (error.statusCode === 401) {
      authenticationStore.clearSession();
      return navigateTo(config.public.routes.login);
    } else if (error.statusCode === 403) {
      return navigateTo(config.public.routes.emailVerify);
    } else {
      return abortNavigation(useErrorFormatter(error));
    }
  }
});
