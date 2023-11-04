<template>
  <div class="grid grid-cols-6 gap-4">
    <div class="col-span-6">
      <div
        class="grid max-w-full grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6 md:col-span-2"
      >
        <div class="sm:col-span-3">
          <BaseInput
            id="menu-title"
            v-model="cmsMenu.title"
            label="Menu title"
            class="sm:col-span-3"
          />
        </div>
        <div class="sm:col-span-3">
          <BaseSwitchField
            id="menu-status"
            v-model="cmsMenu.status"
            label="Menu status"
            class="base-switch sm:col-span-3"
          />
        </div>
      </div>
    </div>
    <div
      class="col-span-6 grid grid-cols-6 gap-4 my-8 border border-gray-300 dark:border-gray-600 p-6"
    >
      <div class="sm:col-span-3">
        <form action="#" method="post" @submit.prevent="addMenuItem">
          <div
            class="grid max-w-full grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6 md:col-span-2"
          >
            <div class="sm:col-span-3">
              <BaseInput
                id="label"
                v-model="menuForm.label"
                label="Label"
                class="sm:col-span-3"
              />
            </div>
            <div class="sm:col-span-3">
              <BaseInput
                id="icon"
                v-model="menuForm.icon"
                label="Icon"
                class="sm:col-span-3"
              />
            </div>
            <div v-if="menuForm.icon" class="sm:col-span-6">
              <BaseRadio
                id="icon_placement"
                v-model="menuForm.icon_placement"
                label="Icon placement"
                :options="iconPlacementOptions"
                :inline="true"
                class="sm:col-span-6"
              />
            </div>
            <div class="sm:col-span-3">
              <BaseInput
                id="url"
                v-model="menuForm.url"
                label="URL"
                class="sm:col-span-3"
              />
            </div>
            <div class="sm:col-span-3">
              <BaseInput
                id="title"
                v-model="menuForm.title"
                label="Title"
                class="sm:col-span-3"
              />
            </div>
            <div class="sm:col-span-3">
              <BaseSelect
                id="target"
                v-model="menuForm.target"
                :options="targetOptions"
                label="Target"
                class="sm:col-span-3"
              />
            </div>
            <div class="sm:col-span-3">
              <BaseSelect
                id="rel"
                v-model="menuForm.rel"
                :options="relOptions"
                label="Rel"
                class="sm:col-span-3"
              />
            </div>
            <div class="sm:col-span-3">
              <BaseSwitchField
                id="status"
                v-model="menuForm.status"
                label="Status"
              />
            </div>
          </div>
          <BaseButton
            id="menu-item-add-btn"
            :type="buttonTypes.SUBMIT"
            class="w-48"
            >{{
              editingItem ? "Update menu item" : "Add menu item"
            }}</BaseButton
          >
        </form>
      </div>
      <div class="sm:col-span-3">
        <AdminNestedDraggable
          v-if="menuItems"
          :list="menuItems"
          @edit="editMenuItem"
          @delete="deleteMenuItem"
        />
      </div>
    </div>
    <div class="sm:col-span-3">
      <BaseButton
        id="submit-btn"
        class="w-32"
        :type="buttonTypes.BUTTON"
        :processing="form.pending"
        @click="onSubmit"
        >Save</BaseButton
      >
    </div>
  </div>
</template>
<script setup lang="ts">
import { PropType } from "vue";
import { useToastStore } from "@/stores/toast";
import {
  buttonTypes,
  MenuItem,
  apiResponse,
  Menu,
  radioOptions,
  selectOptions,
} from "@/types";

const toastStore = useToastStore();

const props = defineProps({
  cmsMenu: {
    type: Object as PropType<Menu>,
    required: true,
  },
});

const cmsMenu = ref<Menu>();
const menuItems = ref<MenuItem[]>();

watchEffect(() => {
  cmsMenu.value = props.cmsMenu;
  menuItems.value = cmsMenu.value.items ? JSON.parse(cmsMenu.value.items) : [];
});

/* const menuItems = ref<MenuItem[]>(
  props.cmsMenu.items ? JSON.parse(cmsMenu.value?.items) : [],
); */

const iconPlacementOptions: radioOptions = [
  { value: "only-icon", label: "Icon only" },
  { value: "before-label", label: "Before label" },
  { value: "after-label", label: "After label" },
];

const targetOptions: selectOptions = [
  { value: "", label: "Select target option" },
  { value: "_blank", label: "Blank" },
  { value: "_self", label: "Self" },
  { value: "_parent", label: "Parent" },
  { value: "_top", label: "Top" },
];

const relOptions: selectOptions = [
  { value: "", label: "Select rel option" },
  { value: "nofollow", label: "nofollow" },
  { value: "noopener", label: "noopener" },
  { value: "noreferrer", label: "noreferrer" },
  { value: "alternate", label: "alternate" },
  { value: "author", label: "author" },
  { value: "bookmark", label: "bookmark" },
  { value: "external", label: "external" },
  { value: "help", label: "help" },
  { value: "license", label: "license" },
  { value: "next", label: "next" },
  { value: "prev", label: "prev" },
  { value: "search", label: "search" },
  { value: "tag", label: "tag" },
];

const editingItem = ref(false);

const defaultMenuForm: MenuItem = {
  id: 1,
  title: "",
  url: "",
  label: "",
  icon: "",
  icon_placement: "",
  target: "",
  rel: "",
  status: false,
};

const menuForm = ref<MenuItem>(Object.assign({}, defaultMenuForm));

const addMenuItem = () => {
  if (editingItem.value === true) {
    editingItem.value = false;
  } else {
    menuForm.value.id = generateUniqueId();
    menuForm.value.items = [];
    menuItems.value?.push(Object.assign({}, menuForm.value));
  }

  menuForm.value = Object.assign({}, defaultMenuForm);
};

const editMenuItem = (menu: MenuItem) => {
  menu.items = toRaw(menu.items);
  if (menuItems.value) {
    const menuItem = findMenuItem(menuItems.value, menu.id);
    if (menuItem !== undefined) {
      menuForm.value = menuItem;
      editingItem.value = true;
    }
  }
};

const deleteMenuItem = (menu: MenuItem) => {
  if (menuItems.value) {
    menuItems.value = removeItem(menuItems.value, menu);
  }
};

const removeItem = (items: MenuItem[], item: MenuItem) => {
  const newItems: MenuItem[] = [];
  items.forEach((childItem) => {
    if (childItem.id !== item.id) {
      newItems.push(childItem);
    }

    if (childItem.items && childItem.items.length > 0) {
      childItem.items = removeItem(childItem.items, item);
    }
  });

  return newItems;
};

const findMenuItem = (
  menuItems: MenuItem[],
  itemId: number,
): MenuItem | undefined => {
  return menuItems.find((item) => item.id === itemId);
};

const generateUniqueId = (): number => {
  const id = Math.floor(Math.random() * 1000);
  if (menuItems.value && findMenuItem(menuItems.value, id) !== undefined) {
    return generateUniqueId();
  }

  return id;
};

const form = ref({
  pending: false,
});

const emit = defineEmits([
  "create",
  "update",
  "success",
  "close",
] as Array<string>);

const onSubmit = async () => {
  form.value.pending = true;
  try {
    let url = "/admin/menu/create";
    let method = "POST";
    let action = "create";
    if (props.cmsMenu.id) {
      url = `/admin/menu/update/${props.cmsMenu.id}`;
      method = "PUT";
      action = "update";
    }

    if (menuItems.value && cmsMenu.value) {
      cmsMenu.value.items = JSON.stringify(menuItems.value);
    }

    // eslint-disable-next-line camelcase, @typescript-eslint/no-unused-vars
    const { id, created_at, updated_at, ...body } = props.cmsMenu;

    const { success, message } = await useFetchApi<apiResponse<Menu>>(url, {
      method,
      body,
    });

    if (success && message) {
      toastStore.addSuccess(message);

      emit(action);
      emit("success");
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
