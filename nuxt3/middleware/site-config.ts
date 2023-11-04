import { useUiStore } from "~/stores/ui";
import { Settings, apiResponse } from "~/types";

export default defineNuxtRouteMiddleware(async (_to, _from) => {
  const { data: response } = await useAsyncData(
    "site-config",
    async () => await useFetchApi<apiResponse<Settings>>("/site/config"),
  );
  const siteConfig = useUiStore();
  if (response.value?.data) {
    siteConfig.setSiteConfig(response.value?.data);
  }
});
