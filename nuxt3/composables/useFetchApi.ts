export default <T>(url: string, options: any = {}) => {
  const config = useRuntimeConfig();
  const token = useCookie(config.public.tokenCookieName);

  options.baseURL = "/api";

  // request api with token
  return $fetch<T>(url, {
    ...options,
    headers: {
      ...options.headers,
      Authorization: `Bearer ${token.value}`,
      "X-Platform": config.public.deviceName,
      Accept: "application/json",
    },
  });
};
