<template>
  <div class="d-flex align-items-center mb-2">
    <button
      v-for="{ key, name } in tags"
      :key="key"
      class="btn btn-secondary me-2"
    >
      &nbsp;{{ name }}
      <span
        class="btn-close text-white ms-1"
        @click="() => handleClose(key)"
      ></span>
    </button>
    <div class="dropdown">
      <button
        ref="filterButton"
        class="btn btn-primary"
        id="filterMenuButton"
        :aria-expanded="false"
        data-bs-toggle="dropdown"
      >
        Add Filter <FontAwesomeIcon :icon="faFilter" fixed-width />
      </button>
      <ul class="dropdown-menu" aria-labelledby="filterMenuButton">
        <li v-for="option in filteredOptions" :key="option.label">
          <a
            href="#"
            class="dropdown-item"
            @click="() => handleMetrics(option.uid)"
          >
            {{ option.label }}
            <small class="text-muted">{{ option.uid }}</small>
          </a>
        </li>
      </ul>
    </div>
    <Modal v-model:open="showModal">
      <template v-if="input.select">
        <component
          :is="returnComponent"
          @submit="(item) => handleSelected(item)"
        />
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { reactive, ref, computed, watch, onMounted, inject } from "vue";
import { useRoute } from "vue-router";
import Dropdown from "bootstrap/js/dist/dropdown.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faFilter } from "@fortawesome/free-solid-svg-icons";
import Modal from "../components/Modal.vue";
import PartNumberMetric from "../components/PartNumberMetric.vue";
import LotNumberMetric from "../components/LotNumberMetric.vue";
import SerialNumberMetric from "../components/SerialNumberMetric.vue";
import CreatedBetweenMetric from "../components/CreatedBetweenMetric.vue";
import ReleasedBetweenMetric from "../components/ReleasedBetweenMetric.vue";

const route = useRoute();
const props = defineProps({ filters: Object });
const emit = defineEmits(["update", "update:filters"]);

// set our options for the select
const filterOptions = [
  { label: "Part Number", uid: "part_number", Component: PartNumberMetric },
  { label: "Lot Number", uid: "lot_number", Component: LotNumberMetric },
  {
    label: "Serial Number",
    uid: "serial_number",
    Component: SerialNumberMetric,
  },
  {
    label: "Date Scanned",
    uid: "created_between",
    Component: CreatedBetweenMetric,
  },
  {
    label: "Date Released",
    uid: "released_between",
    Component: ReleasedBetweenMetric,
  },
];
const returnComponent = computed(
  () => filterOptions.find((x) => x.uid === input.select).Component
);

// hold our form data
const input = reactive({
  select: "",
  data: "",
});

const suggestions = ref([]);

// set up ref for filters
const filters = ref({});

const filterButton = ref(null);
const showModal = ref(false);
const modal = ref(null);
const products = inject("products");

// convert filters to array for the tag display
const tags = computed(() => {
  return Object.keys(filters.value).map((key) => {
    const filter = filterOptions.find((x) => x.uid === key);
    const value = filters.value[key];
    if (key === "created_between") {
      return { key, name: `${filter.label}: from ${value[0]} to ${value[1]}` };
    }
    return { key, name: `${filter.label}: ${value}` };
  });
});

const filteredOptions = computed(() => {
  if (Object.keys(filters.value).length > 0) {
    return filterOptions.filter(
      (x) =>
        !filters.value.hasOwnProperty(x.uid) &&
        products.value &&
        products.value.data.length &&
        products.value.data[0].hasOwnProperty(x.uid) &&
        products.value.data[0][x.uid] != null
    );
  }
  return filterOptions;
});

function handleMetrics(value) {
  input.select = value;
  showModal.value = true;
}

// set filter based on selection
function handleSelected(item) {
  filters.value[input.select] = item;
  emit("update:filters", filters.value);
  suggestions.value = [];
  Object.assign(input, { select: "", data: "" });
  showModal.value = false;
}

// when tag is closed remove filter from object
function handleClose(key) {
  delete filters.value[key];
  emit("update:filters", filters.value);
}

watch(
  () => props.filters,
  (next) => Object.assign(filters.value, next),
  { deep: true, immediate: true }
);

onMounted(() => {
  new Dropdown(filterButton.value, { offset: [0, 8] });
});
</script>
