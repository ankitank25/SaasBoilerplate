<template>
  <div>
    <div class="register-form">
      <form
        class="space-y-6"
        action="#"
        method="POST"
        @submit.prevent="onSubmit"
      >
        <div
          class="grid max-w-full grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6 md:col-span-2"
        >
          <div class="sm:col-span-3">
            <BaseInput
              id="title"
              v-model="cmsPage.title"
              label="Page title"
              type="text"
            />
          </div>
          <div class="sm:col-span-3">
            <BaseInput
              id="url_key"
              v-model="cmsPage.url_key"
              label="URL Key"
              type="text"
            />
          </div>
          <div class="sm:col-span-3">
            <BaseSwitchField
              id="show_title"
              v-model="cmsPage.show_title"
              label="Show page title"
              class="base-switch sm:col-span-3"
            />
          </div>
          <div class="mb-16 sm:col-span-6">
            <ClientOnly>
              <Editor @update="updateContent" />
            </ClientOnly>
          </div>
          <div class="sm:col-span-3">
            <BaseSelect
              id="layout"
              v-model="cmsPage.layout"
              :options="layoutOptions"
              label="Page layout"
            />
          </div>
          <div class="sm:col-span-3">
            <BaseSwitchField
              id="status"
              v-model="cmsPage.status"
              label="Page status"
              class="base-switch sm:col-span-3"
            />
          </div>
          <BaseButton
            id="submit-btn"
            class="w-full"
            :type="buttonTypes.SUBMIT"
            :processing="form.pending"
            >Save</BaseButton
          >
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { PropType } from "vue";
import { useToastStore } from "@/stores/toast";
import { apiResponse, Page, buttonTypes, selectOptions } from "@/types";

const toastStore = useToastStore();

const layoutOptions: selectOptions = [
  { value: "default", label: "Default" },
  { value: "empty", label: "Empty" },
];

const form = ref({
  pending: false,
});

const props = defineProps({
  cmsPage: {
    type: Object as PropType<Page>,
    required: true,
  },
});

const updateContent = (html: string) => {
  if (cmsPage.value) {
    cmsPage.value.content = html;
  }
};

const cmsPage = ref<Page>();

const emit = defineEmits(["create", "update", "success", "close"]);

watchEffect(() => {
  cmsPage.value = props.cmsPage;
});

const onSubmit = async () => {
  form.value.pending = true;
  try {
    let url = "/admin/page/create";
    let method = "POST";
    let action = "create";
    if (cmsPage.value?.id) {
      url = `/admin/page/update/${props.cmsPage.id}`;
      method = "PUT";
      action = "update";
    }

    if (cmsPage.value) {
      // eslint-disable-next-line @typescript-eslint/no-unused-vars, camelcase
      const { id, created_at, updated_at, ...body } = cmsPage.value;

      const { success, message } = await useFetchApi<apiResponse<Page>>(url, {
        method,
        body,
      });

      if (success && message) {
        toastStore.addSuccess(message);

        if (action === "update") {
          emit("update");
        } else {
          emit("create");
        }
        emit("success");
      }
    }
  } catch (error: any) {
    let message = error?.data?.message || "Error processing the request.";
    if (error.status === 422 && error.data.data) {
      message = Object.values(error.data.data).join("<br/>");
    }
    toastStore.addError(message);
  } finally {
    form.value.pending = false;
  }
};
</script>
<style type="text/css">
.ql-container {
  margin: 0 !important;
}
.ql-container .ql-editor {
  min-height: 400px;
}
</style>
