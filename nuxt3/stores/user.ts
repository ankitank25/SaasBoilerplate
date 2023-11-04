import { defineStore } from "pinia";
import { user } from "@/types";

export const useUserStore = defineStore("user", () => {
  const user = ref<user>();

  const getUser = computed(() => user.value);
  const setUser = (newUser: user) => {
    user.value = newUser;
  };

  return {
    getUser,
    setUser,
  };
});
