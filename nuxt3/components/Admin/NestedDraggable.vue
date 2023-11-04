<script setup lang="ts">
import draggable from "vuedraggable";
import { PencilIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { PropType } from "vue";
import { MenuItem } from "@/types";

const props = defineProps({
  list: {
    type: Array as PropType<MenuItem[]>,
    required: true,
  },
});

const emit = defineEmits(["change", "edit", "delete"]);

const checkUpdate = () => {
  emit("change", props.list);
};

const editItem = (element: MenuItem) => {
  emit("edit", element);
};

const deleteItem = (element: MenuItem) => {
  emit("delete", element);
};
</script>
<template>
  <draggable
    :list="list"
    class="dragArea mt-4 ml-8"
    animation="600"
    :group="{ name: 'g1' }"
    item-key="title"
    @change="checkUpdate"
  >
    <template #item="{ element }">
      <div
        class="paper-item group"
        :class="!element.status ? 'opacity-50' : ''"
      >
        <div class="relative border border-gray-400 dark:border-gray-600 p-4">
          <span class="block font-medium">
            <a
              :href="element.url"
              target="_blank"
              :title="element.title"
              class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-300"
            >
              <BaseMenuLabel
                :label="element.label"
                :icon="element.icon"
                :position="element.icon_placement"
                class="inline-flex items-center"
              />
            </a>
            <span class="block text-xs text-gray-500 dark:text-gray-400"
              >{{ element.target ? `target: ${element.target} |` : "" }}
              {{ element.rel ? `rel: ${element.rel}` : "" }}</span
            >
            <span class="block text-xs text-gray-500 dark:text-gray-500"
              >URL: {{ element.url }}</span
            >
          </span>
          <XMarkIcon
            class="absolute rounded border first-line:border-red-600 text-red-600 cursor-pointer group-hover:block w-6 right-4 top-6"
            @click="deleteItem(element)"
          />
          <PencilIcon
            class="absolute p-0.5 rounded border text-sm first-line:border-red-600 text-red-600 cursor-pointer group-hover:block w-6 right-12 top-6"
            @click="editItem(element)"
          />
        </div>
        <AdminNestedDraggable
          :list="element.items"
          @change="checkUpdate"
          @delete="deleteItem"
        />
      </div>
    </template>
  </draggable>
</template>
<style scoped>
.dragArea {
  min-height: 10px;
}
</style>
