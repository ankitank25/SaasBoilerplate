<template>
  <div class="sm:items-start">
    <div class="mt-3 text-left">
      <div class="mt-2 mb-5">
        <p class="text-sm text-gray-500" v-html="message"></p>
      </div>
    </div>
    <form class="space-y-6" action="#" method="POST" @submit.prevent="onSubmit">
      <BaseInput
        id="name"
        v-model="name"
        label="Name"
        type="name"
        class="base-input"
        :validation-error="validationErrors.name"
      />
      <BaseTextarea
        id="description"
        v-model="description"
        label="Description"
        :validation-error="validationErrors.description"
      />
      <div class="">
        <Disclosure
          v-for="(resources, index) in spaceRoleResources"
          :key="index"
          v-slot="{ open }"
        >
          <DisclosureButton
            class="flex w-full justify-between rounded-sm bg-gray-500 dark:bg-gray-900 px-4 py-2 text-left text-sm font-medium text-white dark:text-white"
          >
            <span>{{ index }}</span>
            <ChevronUpIcon
              :class="open ? 'rotate-180 transform' : ''"
              class="h-5 w-5 text-purple-500"
            />
          </DisclosureButton>
          <DisclosurePanel
            class="px-4 pt-4 pb-2 text-sm text-gray-400 dark:bg-gray-700"
            static
          >
            <form class="flex">
              <div
                v-for="(resourceSection, sectionIndex) in resources"
                :key="sectionIndex"
                class="mx-2 my-2 px-4 py-2 min-w-32 border border-gray-500"
              >
                {{ sectionIndex }}
                <ul class="mt-2">
                  <li
                    v-for="(item, itemIndex) in resourceSection"
                    :key="itemIndex"
                    class="flex align-middle"
                  >
                    <input
                      :id="`${index}-${sectionIndex}-${item}`"
                      v-model="abilities"
                      type="checkbox"
                      :value="`${index}-${sectionIndex}-${item}`"
                    />
                    <label
                      :for="`${index}-${sectionIndex}-${item}`"
                      class="ml-1"
                      >{{ item }}</label
                    >
                  </li>
                </ul>
              </div>
            </form>
          </DisclosurePanel>
        </Disclosure>
        <span v-if="validationErrors.name">{{
          validationErrors.abilities
        }}</span>
      </div>
      <BaseButton
        id="inviteSubmit"
        class="w-full"
        :type="buttonTypes.SUBMIT"
        :processing="form.pending"
        >Invite</BaseButton
      >
    </form>
  </div>
</template>

<script setup lang="ts">
import { ChevronUpIcon } from "@heroicons/vue/20/solid";
import { useForm, useField, useFieldArray } from "vee-validate";
import { string, object } from "yup";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { useSpaceStore } from "@/stores/space";
import { useToastStore } from "@/stores/toast";
import { apiResponse, Space, buttonTypes } from "@/types";

const { getCurrentSpaceId } = useSpaceStore();
const toast = useToastStore();
const config = useRuntimeConfig();

const spaceRoleResources = ref<object>({});

const form = ref({
  pending: false,
});

/* const setPermission = (area: string, permission: string) => {
  type AccessObjectKey = keyof typeof form.value.data.abilities;
  // form.value.data.abilities[area as AccessObjectKey].push(permission);
}; */

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  disposable: {
    type: Boolean,
    default: true,
  },
  title: {
    type: String,
    default: "Invite a member",
  },
  message: {
    type: String,
    default: "",
  },
});

const emit = defineEmits(["cancel", "added"]);

const schema = object({
  name: string().required(),
  description: string(),
});

const { handleSubmit, errors: validationErrors } = useForm({
  validationSchema: schema,
});

const { value: name } = useField<string | undefined>("name");
const { value: description } = useField<string | undefined>("description");
const { fields: abilities } = useFieldArray<[] | undefined>("abilities");

const onSubmit = handleSubmit(async (values) => {
  values.space_id = getCurrentSpaceId;
  values.abilities = JSON.stringify(abilities.value);
  form.value.pending = true;
  try {
    /* let abilities = {};
    console.log(form.value.data.role);
    if (form.value.data.role === "custom") {
      abilities = { access: form.value.data.role };
    } else {
      abilities = { access: form.value.data.role };
    }

    form.value.data.abilities = abilities; */

    const response = await useFetchApi<apiResponse<Space>>(
      config.public.apiRoutes.spaceRole.replace("SPACEID", getCurrentSpaceId),
      {
        method: "POST",
        body: values,
      },
    );

    if (response && response.success) {
      toast.addSuccess(response.message);

      /* user.value.spaces?.push(response.data); */

      emit("added");
    }
  } catch (error: any) {
    toast.addSuccess(error?.data?.message);
  } finally {
    form.value.pending = false;
  }
});

const { data: roleResources, error: roleResourcesError } = await useAsyncData(
  "space-role-resources",
  () =>
    useFetchApi<apiResponse<object>>(
      config.public.apiRoutes.spaceRoleResources,
    ),
);

if (roleResources.value) {
  spaceRoleResources.value = roleResources.value.data;
}

if (roleResourcesError.value?.message) {
  toast.addError(
    roleResourcesError.value.message || "Error processing the request",
  );
}
</script>
