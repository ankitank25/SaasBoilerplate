<template>
  <div>
    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-6">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-6">
          <div class="relative">
            <div
              v-if="selectedItems.length > 0"
              class="absolute left-14 top-0 flex h-12 items-center space-x-3 sm:left-12"
            >
              <button
                type="button"
                class="inline-flex items-center rounded bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-white"
              >
                Bulk edit
              </button>
              <button
                type="button"
                class="inline-flex items-center rounded bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-white"
                @click="promptBulkDeleteAction"
              >
                Delete all
              </button>
            </div>
            <table
              class="min-w-full table-fixed divide-y divide-gray-300 dark:divide-gray-600"
            >
              <thead>
                <tr>
                  <th
                    v-if="checkboxField"
                    scope="col"
                    class="relative px-7 sm:w-12 sm:px-6"
                  >
                    <input
                      type="checkbox"
                      class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                      :checked="
                        indeterminate || selectedItems.length === data?.length
                      "
                      :indeterminate="indeterminate"
                      @change="selectAllItem"
                    />
                  </th>
                  <th
                    v-for="(column, index) in columns"
                    :key="index"
                    scope="col"
                    :class="[
                      column.class,
                      'py-3.5 pr-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300',
                    ]"
                  >
                    {{ column.label }}
                  </th>
                  <th
                    v-if="actions.length"
                    scope="col"
                    class="relative w-auto py-3.5 pl-3 pr-4 sm:pr-3 text-right"
                  >
                    <span>Action</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                <tr v-for="(item, itemIndex) in data" :key="itemIndex">
                  <td
                    v-if="checkboxField"
                    class="relative px-7 sm:w-12 sm:px-6"
                  >
                    <div
                      v-if="selectedItems.includes(item[checkboxField as keyof typeof item])"
                      class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600"
                    ></div>
                    <input
                      v-model="selectedItems"
                      type="checkbox"
                      class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                      :value="item[checkboxField as keyof typeof item]"
                    />
                  </td>
                  <td
                    v-for="(column, index) in columns"
                    :key="index"
                    :class="[
                      column.class,
                      'whitespace-nowrap py-4 pr-3 text-sm font-medium text-gray-700 dark:text-gray-300',
                    ]"
                    v-html="getColumnValue(item as object, column, index)"
                  ></td>
                  <td
                    v-if="actions.length"
                    class="whitespace-nowrap w-auto py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3"
                  >
                    <template
                      v-for="(actionItem, actionIndex) in actions"
                      :key="actionIndex"
                    >
                      <a
                        v-if="actionItem.action === 'edit'"
                        href="#"
                        class="mx-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-500"
                        @click.prevent="emitEditAction(item)"
                      >
                        {{ actionItem.label }}
                      </a>
                      <a
                        v-else-if="actionItem.action === 'delete'"
                        href="#"
                        class="ml-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-500"
                        @click.prevent="promptDeleteAction(item)"
                      >
                        {{ actionItem.label }}
                      </a>
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
            <Pagination
              v-if="pagination"
              :pagination="pagination"
              @pagination-click="handlePagination"
            />
            <BasePromt
              :open="itemDeletePrompt"
              :title="deletePrompt.title"
              @cancel="cancelDeleteAction"
              @confirm="emitDeleteAction(itemToDelete)"
              >{{ deletePrompt.message }}</BasePromt
            >

            <BasePromt
              :open="bulkDeletePrompt"
              title="Mass item delete confirmation"
              @cancel="cancelBulkDeleteAction"
              @confirm="emitBulkDeleteAction"
              >Are you sure you want to delete all selected items?</BasePromt
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { PropType } from "vue";
import { useTimeAgo, useDateFormat } from "@vueuse/core";
import {
  listColumns,
  listColumn,
  listActions,
  paginationData,
  listColumnOption,
  listDeletePrompt,
} from "@/types";

const itemDeletePrompt = ref(false);
const itemToDelete = ref<object>();

const bulkDeletePrompt = ref(false);

const props = defineProps({
  columns: {
    type: Object as PropType<listColumns>,
    default: () => {},
    required: true,
  },
  dataSource: {
    type: Array as PropType<object[]>,
    default: () => [{}],
  },
  pagination: {
    type: Object as PropType<paginationData>,
    default: () => {},
  },
  actions: {
    type: Array as PropType<listActions>,
    default: () => [],
  },
  checkboxField: {
    type: String,
    default: "",
  },
  deletePrompt: {
    type: Object as PropType<listDeletePrompt>,
    default: (): listDeletePrompt => {
      return {
        title: "Item delete confirmation",
        message: "Are you sure you want to delete this item?",
      };
    },
  },
});

const data = ref<Array<object>>();

watchEffect(() => {
  data.value = props.dataSource;
});

const getColumnValue = (data: object, column: listColumn, id: any) => {
  type objectKey = keyof typeof data;
  const rowValue = data[id as objectKey];
  if (column.type === "date" && rowValue) {
    return `<span title="${
      useDateFormat(rowValue, "YYYY-MM-DD HH:mm:ss").value
    }">${useTimeAgo(rowValue).value}</span>`;
  }

  if (column.type === "options" && column.options !== undefined) {
    const rowOptionValue = column.options.find(
      (option: listColumnOption) => option.value === rowValue,
    );

    return rowOptionValue?.label || rowValue;
  }
  return rowValue;
};

const emit = defineEmits([
  "edit",
  "delete",
  "selectAll",
  "unSelectAll",
  "bulkDelete",
  "paginate",
]);

const emitEditAction = (item: any) => {
  emit("edit", item);
};

/** Delete action **/
const promptDeleteAction = (item: any) => {
  itemDeletePrompt.value = true;
  itemToDelete.value = item;
};

const cancelDeleteAction = () => {
  itemDeletePrompt.value = false;
  itemToDelete.value = {};
};

const emitDeleteAction = (item: any) => {
  cancelDeleteAction();
  emit("delete", item);
};
/** Delete action **/

/** Bulk Delete action **/
const promptBulkDeleteAction = () => {
  bulkDeletePrompt.value = true;
};

const cancelBulkDeleteAction = () => {
  bulkDeletePrompt.value = false;
};

const emitBulkDeleteAction = () => {
  cancelBulkDeleteAction();
  if (selectedItems.value.length > 0) {
    emit("bulkDelete", selectedItems.value);
  }
};
/** Bulk Delete action **/

const selectedItems = ref<any[]>([]);
const indeterminate = computed(
  () =>
    data.value &&
    selectedItems.value.length > 0 &&
    selectedItems.value.length < data.value.length,
);

const selectAllItem = (event: Event) => {
  if (props.checkboxField) {
    const target = event.target as HTMLInputElement;
    type objectKey = keyof typeof data.value;

    if (target.checked && data.value) {
      selectedItems.value = data.value.map(
        (p: any) => p[props.checkboxField as objectKey],
      );

      emit("selectAll", selectedItems.value);
    } else {
      selectedItems.value = [];
      emit("unSelectAll", []);
    }
  }
};

const handlePagination = (url: string) => {
  emit("paginate", url);
};
</script>
