export default () => {
  const config = useRuntimeConfig();
  return "/" + config.public.apiBaseUrlPath + "/";
};
