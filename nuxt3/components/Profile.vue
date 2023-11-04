<template>
  <form @submit.prevent="onSubmit">
    <div class="space-y-12">
      <div
        class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3"
      >
        <div>
          <h2
            class="text-base font-semibold leading-7 text-gray-700 dark:text-gray-300"
          >
            Profile information
          </h2>
          <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
            This information will be displayed publicly so be careful what you
            share.
          </p>
        </div>

        <div
          class="grid max-w-4xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2"
        >
          <div class="sm:col-span-3">
            <BaseInput
              id="first_name"
              v-model="firstName"
              label="First name"
              type="text"
              :validation-error="validationErrors.first_name"
            />
          </div>
          <div class="sm:col-span-3">
            <BaseInput
              id="last_name"
              v-model="lastName"
              label="Last name"
              type="text"
              :validation-error="validationErrors.last_name"
            />
          </div>
          <div class="sm:col-span-3">
            <BaseSelect
              id="gender"
              v-model="gender"
              :options="genderOptions"
              label="Gender"
              type="text"
              :validation-error="validationErrors.gender"
            />
          </div>
        </div>
      </div>
      <div
        class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3"
      >
        <div>
          <h2
            class="text-base font-semibold leading-7 text-gray-700 dark:text-gray-300"
          >
            Account information
          </h2>
          <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
            This information will be displayed publicly so be careful what you
            share.
          </p>
        </div>

        <div
          class="grid max-w-4xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2"
        >
          <div class="sm:col-span-6">
            <BaseInput
              id="email"
              v-model="email"
              label="Email"
              type="email"
              :validation-error="validationErrors.email"
            />
          </div>
          <div class="sm:col-span-3">
            <BaseInput
              id="password"
              v-model="password"
              label="Password"
              type="password"
              :validation-error="validationErrors.password"
            />
            <BasePasswordStength :password="password" />
          </div>
          <div class="sm:col-span-3">
            <BaseInput
              id="password_confirmation"
              v-model="passwordConfirmation"
              label="Confirm password"
              type="password"
              :validation-error="validationErrors.password_confirmation"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 flex items-center justify-between gap-x-6">
      <div>
        <BaseButton
          id="logout-btn"
          class="w-32 bg-red-700 hover:bg-red-600 mr-6 focus-visible:outline-red-600"
          :type="buttonTypes.BUTTON"
          @click="authenticationStore.logout"
          >Logout</BaseButton
        >
        <BaseButton
          id="logout-all-btn"
          class="w-auto bg-red-700 hover:bg-red-600 focus-visible:outline-red-600"
          :type="buttonTypes.BUTTON"
          @click="logoutAllPrompt = true"
          >Logout From All Devices</BaseButton
        >
        <BasePromt
          :open="logoutAllPrompt"
          title="Logout everywhere confirmation"
          @cancel="logoutAllPrompt = false"
          @confirm="logoutFromAllDevices"
          >Are you sure you want to logout from your all logged in
          devices?</BasePromt
        >
      </div>
      <BaseButton
        id="save-btn"
        class="w-32"
        :type="buttonTypes.SUBMIT"
        :processing="form.pending"
        :disabled="isFieldsModified"
        >Save</BaseButton
      >
      <BaseReAuthentication
        :open="reAuthenticateModal"
        title="Re-Authenticate your account"
        @cancel="reAuthenticateModal = false"
        @success="updateProfile()"
      />
    </div>
  </form>
  <BaseActiveSessions :key="sessionListKey" />
</template>

<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { string, object, ref as yupRef } from "yup";
import { apiResponse, userResponse, buttonTypes, selectOptions } from "@/types";

import { useAuthenticationStore } from "@/stores/authentication";
import { useToastStore } from "@/stores/toast";

const authenticationStore = useAuthenticationStore();
const config = useRuntimeConfig();
const toastStore = useToastStore();

const logoutAllPrompt = ref<boolean>(false);
const reAuthenticateModal = ref<boolean>(false);
const reAuthRequired = ref<boolean>(false);

const sessionListKey = ref(1);

let user = authenticationStore.getUser;

const form = ref({
  pending: false,
});

const genderOptions: selectOptions = [
  { value: "male", label: "Male" },
  { value: "female", label: "Female" },
];

const schema = object({
  first_name: string().required(),
  last_name: string().required(),
  email: string().required().email(),
  password: string().min(parseInt(config.public.minPasswordLength)),
  password_confirmation: string()
    .nullable()
    .oneOf(
      [yupRef("password")],
      "Confirm password should be same as password.",
    ),
  meta: object({
    gender: string(),
    test: string(),
  }),
});

const {
  handleSubmit,
  errors: validationErrors,
  setValues,
  resetForm,
  values,
} = useForm({
  validationSchema: schema,
});

// Set default value for terms_agreed checkbox to fix validation rule.
setValues({
  first_name: user?.first_name,
  last_name: user?.last_name,
  email: user?.email,
  meta: {
    gender: getUserMeta("gender"),
  },
});

const { value: firstName } = useField<string>("first_name");
const { value: lastName } = useField<string>("last_name");
const { value: email } = useField<string>("email");
const { value: password } = useField<string>("password");
const { value: passwordConfirmation } = useField<string>(
  "password_confirmation",
);
const { value: gender } = useField<string>("meta.gender");

const getModifiedFields = computed(() => {
  type UserObjectKey = keyof typeof user;
  const modifiedData: any = {};
  Object.keys(values).forEach((key) => {
    if (
      user &&
      values[key as UserObjectKey] &&
      typeof values[key as UserObjectKey] === "object"
    ) {
      Object.keys(values[key as UserObjectKey]).forEach((subKey) => {
        if (
          user &&
          values[key][subKey] &&
          values[key][subKey] !==
            user[key as UserObjectKey][subKey as UserObjectKey]
        ) {
          modifiedData[subKey] = values[key][subKey];
        }
      });
    } else if (user && values[key] !== user[key as UserObjectKey]) {
      modifiedData[key] = values[key];

      if (
        ["password", "password_confirmation"].includes(key) &&
        modifiedData[key] === ""
      ) {
        delete modifiedData[key];
      }
    }
  });

  reAuthRequired.value = false;

  if (modifiedData.email !== undefined || modifiedData.password !== undefined) {
    reAuthRequired.value = true;
  }

  return modifiedData;
});

const isFieldsModified = computed(() => {
  return !Object.keys(getModifiedFields.value).length;
});

const updateProfile = handleSubmit(async (values) => {
  form.value.pending = true;

  if (values.email === user?.email) {
    delete values.email;
  }

  try {
    if (user?.id) {
      const { success, message, data, errorCode } = await useFetchApi<
        apiResponse<userResponse>
      >(
        `${config.public.apiRoutes.accountUpdateDelete.replace(
          "USERID",
          user.id,
        )}`,
        {
          method: "PATCH",
          body: values,
        },
      );

      if (success && message) {
        toastStore.addSuccess(message);
        user = data.user;
        resetForm({
          values: data.user,
        });

        if (errorCode === "unverified") {
          return navigateTo(config.public.routes.emailVerify);
        }
      }
    }
  } catch (error: any) {
    toastStore.addError(error?.data?.message || "Error processing request");
  } finally {
    form.value.pending = false;
  }
});

const onSubmit = () => {
  reAuthenticateModal.value = reAuthRequired.value;

  if (!reAuthRequired.value) {
    updateProfile();
  }
};

const logoutFromAllDevices = async () => {
  await authenticationStore.logoutAll();
  sessionListKey.value++;
  logoutAllPrompt.value = false;
};
</script>
