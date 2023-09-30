<template>
  <div>
    <div class="alert alert-danger" v-if="error">
      <p>{{ error }}</p>
    </div>
    <div class="mb-3">
      <ProductFilters v-model:filters="state.filters" />
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <Pagination
        v-if="products?.meta?.last_page > 1"
        :page-size="30"
        :total="products?.meta?.total"
        v-model:current-page="state.current_page"
      />
    </div>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-borderless table-striped align-bottom">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Data</th>
                <th scope="col">&nbsp;</th>
                <th scope="col">&nbsp;</th>
                <th scope="col">&nbsp;</th>
                <th scope="col">Scanned to Inventory</th>
                <th scope="col">Released to Production</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-if="products && products.data"
                v-for="product in products.data"
              >
                <th scope="row">{{ product.id }}</th>
                <template v-if="product.serial_number">
                  <td colspan="4">
                    <small class="text-muted">serial number</small>
                    <div class="d-block">{{ product.serial_number }}</div>
                  </td>
                </template>
                <template v-else>
                  <td>
                    <small class="text-muted">part</small>
                    <div class="d-block">
                      {{ product.part_number }}
                    </div>
                  </td>
                  <td>
                    <small class="text-muted">lot</small>
                    <div class="d-block">
                      {{ product.lot_number }}
                    </div>
                  </td>
                  <td>
                    <small class="text-muted">quantity</small>
                    <div class="d-block">
                      {{ product.quantity }}
                    </div>
                  </td>
                  <td>
                    <small class="text-muted">manufactured</small>
                    <div class="d-block">
                      {{
                        dayjs(product.date_manufactured).format("MM/DD/YYYY")
                      }}
                    </div>
                  </td>
                </template>
                <td>
                  {{ dayjs(product.created_at).format("MM/DD/YYYY h:mm A") }}
                </td>
                <td>
                  <span v-if="product.date_released">
                    {{
                      dayjs(product.date_released).format("MM/DD/YYYY h:mm A")
                    }}
                  </span>
                  <span v-else> N/A </span>
                </td>
                <td>
                  <div class="d-flex justify-content-end">
                    <button
                      type="button"
                      :class="[
                        'btn btn-secondary me-2',
                        product.date_released && 'disabled',
                      ]"
                      @click="() => handleRelease(product)"
                      :disabled="product.date_released"
                    >
                      Release
                    </button>
                    <button
                      type="button"
                      class="btn btn-danger"
                      @click="() => handleRemove(product)"
                    >
                      Remove
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <Modal v-model:open="state.showModal">
      <p>
        Are you sure you want to delete product with id
        {{ state.selected?.id }}?
      </p>
      <div class="d-flex">
        <button
          type="button"
          class="btn btn-light"
          @click="
            () => {
              state.showModal = false;
              state.selected = {};
            }
          "
        >
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-danger"
          @click="
            () => {
              sendDelete(state.selected);
              state.selected = {};
              state.showModal = false;
            }
          "
        >
          Delete
        </button>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import {
  reactive,
  watch,
  onMounted,
  computed,
  watchEffect,
  provide,
} from "vue";
import { useRouter, useRoute } from "vue-router";
import useSWRV from "swrv";
import dayjs from "dayjs";
import http from "../utils/http";
import { releaseProduct } from "../repository/products";
import ProductFilters from "../components/ProductFilters.vue";
import Pagination from "../components/Pagination.vue";
import Modal from "../components/Modal.vue";
import sendToast from "../utils/sendToast";

const router = useRouter();
const route = useRoute();

const state = reactive({
  current_page: 1,
  filters: {},
  showModal: false,
  selected: {},
});

const url = computed(() => {
  const baseUrl = `/api/products`;
  const page = `page[number]=${state.current_page}`;
  const sort = `sort=-id`;
  const filters = Object.keys(state.filters)
    .map((key) => `filter[${key}]=${state.filters[key]}`)
    .join("&");
  return `${baseUrl}?${page}&${sort}&${filters}`;
});

const {
  data: products,
  error,
  mutate,
} = useSWRV(
  () => url.value,
  (url) => http.get(url).then(({ data }) => data)
);
provide("products", products);

async function sendDelete(product) {
  const { id } = product;

  // eagerly remove product from local data
  const i = products.value.data.map(({ id }) => id).indexOf(id);
  if (i > -1) products.value.data.splice(i, 1);

  // format delete message
  let deleteMessage = product.serial_number
    ? `serial number ${product.serial_number} was deleted`
    : `part ${product.part_number} from lot ${product.lot_number} was deleted`;

  // submit to server
  try {
    await http.delete(`/api/products/${id}`);
    sendToast({
      title: "Product Deleted",
      body: deleteMessage,
    });
  } catch (e) {
    sendToast({
      title: "Error",
      body: e.message,
    });
  } finally {
    await mutate();
  }
}

function handleRemove(product) {
  state.selected = product;
  state.showModal = true;
}

async function handleRelease(product) {
  await releaseProduct(product);
  await mutate();
}

onMounted(() => {
  state.current_page = parseInt(route.query["page[number]"]) || 1;
  if (route.query) {
    if (route.query["filter[part_number]"]) {
      state.filters.part_number = route.query["filter[part_number]"];
    }
    if (route.query["filter[lot_number]"]) {
      state.filters.lot_number = route.query["filter[lot_number]"];
    }
    if (route.query["filter[created_between]"]) {
      state.filters.created_between = route.query["filter[created_between]"];
    }
  }
  if (!route.query || !route.query["page[number]"]) {
    router.replace({ query: { ...route.query, "page[number]": 1 } });
  }
});

watchEffect(() => {
  const query = { "page[number]": state.current_page };
  Object.keys(state.filters).forEach((key) => {
    return (query[`filter[${key}]`] = state.filters[key]);
  });
  if (route.path === "/lookup") {
    router.replace({ query });
  }
});

watch(
  () => state.filters,
  () => (state.current_page = 1),
  { deep: true }
);
</script>
