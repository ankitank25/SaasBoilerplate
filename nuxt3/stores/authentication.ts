import { defineStore } from "pinia";
import { useToastStore } from "./toast";
import { useUserStore } from "./user";
import {
  apiResponse,
  registerParams,
  loginParams,
  userTokenResponse,
  userResponse,
  resetPasswordParams,
} from "@/types";

export const useAuthenticationStore = defineStore("authentication", () => {
  const config = useRuntimeConfig();
  const router = useRouter();
  const token = ref(useCookie(config.public.tokenCookieName));
  const userStore = useUserStore();
  const toast = useToastStore();

  const register = async (
    params: registerParams,
  ): Promise<apiResponse<userTokenResponse>> => {
    const response = await $fetch<apiResponse<userTokenResponse>>(
      config.public.apiRoutes.register,
      {
        method: "POST",
        baseURL: useApiBaseUrl(),
        headers: {
          "X-Platform": config.public.deviceName,
        },
        body: params,
      },
    );

    if (response.success && response.data) {
      token.value = response.data.token;
      userStore.setUser(response.data.user);
    }

    return response;
  };

  const verifyEmail = async (url: string): Promise<apiResponse> => {
    return await useFetchApi<apiResponse>(url);
  };

  const login = async (
    params: loginParams,
  ): Promise<apiResponse<userTokenResponse>> => {
    const response = await $fetch<apiResponse<userTokenResponse>>(
      config.public.apiRoutes.login,
      {
        method: "POST",
        baseURL: useApiBaseUrl(),
        headers: {
          "X-Platform": config.public.deviceName,
        },
        body: params,
      },
    );

    if (response.success && response.data) {
      token.value = response.data.token;
      userStore.setUser(response.data.user);
    }

    return response;
  };

  const reAuthenticate = async (
    params: loginParams,
  ): Promise<apiResponse<userResponse>> => {
    const response = await $fetch<apiResponse<userResponse>>(
      config.public.apiRoutes.reAuthenticate,
      {
        method: "POST",
        baseURL: useApiBaseUrl(),
        headers: {
          "X-Platform": config.public.deviceName,
        },
        body: params,
      },
    );

    return response;
  };

  const isAuthenticated = async (): Promise<apiResponse<userResponse>> => {
    const response = await useFetchApi<apiResponse<userResponse>>(
      config.public.apiRoutes.me,
    );

    if (response.success && response.data) {
      userStore.setUser(response.data.user);
    }

    return response;
  };

  const logout = async (): Promise<void> => {
    try {
      const { success, message } = await useFetchApi<
        apiResponse<userTokenResponse>
      >(config.public.apiRoutes.logout);

      if (success !== true) {
        throw new Error(message || "Error processing request.");
      }

      toast.addSuccess(message);

      clearSession();
      router.push(config.public.routes.login);
    } catch (error: any) {}
  };

  const logoutAll = async (): Promise<void> => {
    try {
      const { success, message } = await useFetchApi<
        apiResponse<userTokenResponse>
      >(config.public.apiRoutes.logoutAll);

      if (success !== true) {
        throw new Error(message || "Error processing request.");
      }

      toast.addSuccess(message);
    } catch (error: any) {}
  };

  const forgotPassword = async (params: {
    email: string;
  }): Promise<apiResponse> => {
    return await useFetchApi<apiResponse>(
      config.public.apiRoutes.forgotPassword,
      {
        method: "POST",
        body: params,
      },
    );
  };

  const resetPassword = async (
    params: resetPasswordParams,
  ): Promise<apiResponse> => {
    return await useFetchApi<apiResponse>(
      config.public.apiRoutes.resetPassword,
      {
        method: "POST",
        body: params,
      },
    );
  };

  const clearSession = () => {
    token.value = null;
  };

  const getToken = computed(() => token.value);
  const getUser = computed(() => userStore.getUser);

  return {
    register,
    verifyEmail,
    login,
    reAuthenticate,
    isAuthenticated,
    logout,
    logoutAll,
    forgotPassword,
    resetPassword,
    clearSession,
    getToken,
    getUser,
  };
});
