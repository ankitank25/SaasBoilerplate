import { defineStore } from "pinia";
import { useStorageAsync } from "@vueuse/core";
import { apiResponse, Space, SpaceRole, Spaces } from "@/types";
import { useToastStore } from "@/stores/toast";

export const useSpaceStore = defineStore("space", () => {
  const config = useRuntimeConfig();
  const toast = useToastStore();

  const spaces = ref<Spaces>([]);

  const currentSpace = useStorageAsync("current_space", {} as Space);

  const currentSpaceId = useCookie("current_space_id", { default: () => "" });
  const currentRole = useCookie("current_role", { default: () => "" });

  const setCurrentSpace = async (space: Space): Promise<Space | undefined> => {
    currentSpace.value = space;
    currentSpaceId.value = space.id;

    const { data, error } = await useAsyncData("user-spaces", () =>
      useFetchApi<apiResponse<SpaceRole>>(
        config.public.apiRoutes.userCurrentSpace.replace("SPACEID", space.id),
        { method: "PATCH" },
      ),
    );

    if (data.value) {
      currentRole.value = data.value.data.abilities;
    }

    if (error.value?.message) {
      toast.addError(error.value.message || "Error processing the request");
    }

    return space;
  };

  const updateSpace = (space: Space): void => {
    const spaceIndex = spaces.value.findIndex((s: Space) => s.id === space.id);
    if (spaceIndex === -1) {
      spaces.value = [...spaces.value, space];
    } else {
      spaces.value = [
        ...spaces.value.splice(0, spaceIndex),
        space,
        ...spaces.value.slice(spaceIndex + 1),
      ];
    }
  };

  const loadSpaces = async (): Promise<Spaces | undefined> => {
    try {
      const { data, error } = await useAsyncData("user-spaces", () =>
        useFetchApi<apiResponse<Spaces>>(config.public.apiRoutes.spaces),
      );

      if (data.value) {
        spaces.value = data.value.data;
      }

      if (error.value?.message) {
        toast.addError(error.value.message || "Error processing the request");
      }

      if (!currentSpaceId.value && spaces.value.length > 0) {
        setCurrentSpace(spaces.value[0]);
      }
      return spaces.value;
    } catch (error: any) {}
  };

  const getSpaces = spaces;

  const getCurrentSpace = computed(() => {
    if (currentSpaceId) {
      return spaces.value.find(
        (space: Space) => space.id === currentSpaceId.value,
      );
    }
  });

  const getCurrentSpaceId = currentSpaceId;

  return {
    loadSpaces,
    setCurrentSpace,
    updateSpace,
    getSpaces,
    getCurrentSpace,
    getCurrentSpaceId,
  };
});
