import { defineEventHandler, sendRedirect } from "h3";
import { apiResponse } from "~/types";
export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig();

  const requestPath = getRequestPath(event).toString();
  if (!getCookie(event, config.public.tokenCookieName)) {
    const loginUrl = `${config.public.routes.login}?referral=${requestPath}`;
    await sendRedirect(event, loginUrl, 301);
  }

  const queryString = getQuery(event);
  if (queryString.key) {
    const invitationKey: string = queryString.key?.toString();

    const headers: {
      Authorization?: string;
      Accept: string;
      "Content-Type": string;
      "X-Platform": string;
    } = {
      Authorization: `Bearer ${getCookie(
        event,
        config.public.tokenCookieName,
      )}`,
      Accept: "application/json",
      "Content-Type": "application/json",
      "X-Platform": config.public.deviceName,
    };
    const baseURL =
      config.public.apiBaseUrlHost + "/" + config.public.apiBaseUrlPath;
    const url = config.public.apiRoutes.spaceInvitationReject.replace(
      "INVITATIONID",
      invitationKey,
    );
    const method = "GET";

    const { success } = await $fetch<apiResponse>(url, {
      headers,
      baseURL,
      method,
    });

    if (success) {
      await sendRedirect(event, config.public.routes.account);
    }
  }
});
