import {
  defineEventHandler,
  getCookie,
  getMethod,
  getQuery,
  getHeaders,
  readBody,
} from "h3";
const config = useRuntimeConfig();
const baseURL = config.public.apiBaseUrlHost;
export default defineEventHandler(async (event) => {
  const method = getMethod(event);
  const params = getQuery(event);

  const eventHeaders = getHeaders(event);
  const authorization =
    eventHeaders.authorization || getCookie(event, "XSRF-Token");

  const headers: {
    Authorization?: string;
    Accept: string;
    "Content-Type": string;
    "X-Platform": string;
    "User-Agent": string;
    "X-Forwarded-For": string;
    "X-Forwarded-Port": string;
    "X-Forwarded-Proto": string;
  } = {
    Authorization: authorization,
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-Platform": eventHeaders["x-platform"] as string,
    "User-Agent": eventHeaders["user-agent"] as string,
    "X-Forwarded-For": eventHeaders["x-forwarded-for"] as string,
    "X-Forwarded-Port": eventHeaders["x-forwarded-port"] as string,
    "X-Forwarded-Proto": eventHeaders["x-forwarded-proto"] as string,
  };

  const url = event.req.url as string;

  const body = method === "GET" ? undefined : await readBody(event);

  console.log(baseURL + url);

  return await $fetch(url, {
    headers,
    baseURL,
    method,
    params,
    body,
  });
});
