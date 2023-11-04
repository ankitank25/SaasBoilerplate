import { useAuthenticationStore } from "~/stores/authentication";

export default defineNuxtRouteMiddleware(async (_to, _from) => {
  const config = useRuntimeConfig();
  const authenticationStore = useAuthenticationStore();

  if (!authenticationStore.getToken) {
    return navigateTo({
      path: config.public.routes.login,
      query: setReferral(_to),
    });
  }
  console.log(_to.query);
  try {
    if (_to.query.verify_url) {
      const { success } = await authenticationStore.verifyEmail(
        _to.query.verify_url.toString(),
      );

      if (success) {
        return navigateTo(config.public.routes.account);
      }
    } else {
      await authenticationStore.isAuthenticated();
      return navigateTo(config.public.routes.account);
    }
  } catch (error: any) {
    if (error.statusCode === 401) {
      authenticationStore.clearSession();
      return navigateTo(config.public.routes.login);
    } else if (error.statusCode !== 403) {
      return abortNavigation(useErrorFormatter(error));
    }
  }
});
