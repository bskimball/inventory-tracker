<template>
  <div class="part-number-metric">
    <div class="dropdown">
      <form @submit.prevent="handleSubmit">
        <label for="partNumberFilter" class="form-label">Part Number</label>
        <div class="input-group">
          <div class="dropdown flex-grow-1">
            <input
              v-model="state.input"
              @input="querySearch"
              ref="inputElement"
              type="text"
              id="partNumberFilter"
              :class="[
                'form-control',
                v$.input.$dirty && v$.input.$error && 'is-invalid',
              ]"
              autocomplete="off"
            />
            <div :class="['dropdown-menu', state.input && 'show']">
              <ul v-if="state.suggestions.length > 0" class="list-unstyled">
                <li>
                  <h6 class="dropdown-header">Suggestions</h6>
                </li>
                <li v-for="suggestion in state.suggestions">
                  <a
                    href="#"
                    class="dropdown-item"
                    @click="() => handleSelect(suggestion)"
                  >
                    {{ suggestion }}
                  </a>
                </li>
              </ul>
              <div v-else class="px-3">
                <p class="text-muted">No suggestions</p>
              </div>
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, reactive, ref, watch } from "vue";
import debounce from "lodash/debounce.js";
import Dropdown from "bootstrap/js/dist/dropdown.js";
import http from "../utils/http.js";
import { required } from "@vuelidate/validators";
import { useVuelidate } from "@vuelidate/core";
import { useRoute } from "vue-router";

const route = useRoute();
const props = defineProps({ modelValue: String });
const emits = defineEmits(["submit", "update:modelValue"]);

const inputElement = ref(null);
const dropdown = ref(null);

const state = reactive({
  input: "",
  suggestions: [],
});

const rules = computed(() => ({
  input: { required },
}));
const v$ = useVuelidate(rules, state);
const products = inject("products");

const querySearch = debounce(async () => {
  const hasFilters = Object.keys(route.query).some((x) =>
    x.startsWith("filter")
  );
  if (!hasFilters) {
    const { data } = await http.get(`/api/search`, {
      params: { [`filter[part_number]`]: state.input },
    });
    state.suggestions = [...data];
  } else {
    state.suggestions = products.value.data
      .filter(
        (x) =>
          x.part_number &&
          x.part_number.toLowerCase().startsWith(state.input.toLowerCase())
      )
      .map((y) => y.part_number);
  }
}, 200);

function handleSubmit() {
  v$.value.$validate().then((valid) => {
    if (valid) {
      emits("submit", state.input);
    }
  });
}

function handleSelect(suggestion) {
  state.input = suggestion;
  setTimeout(() => {
    handleSubmit();
  }, 150);
}

watch(
  () => state.input,
  (next) => emits("update:modelValue", next)
);

watch(
  () => props.modelValue,
  (next) => Object.assign(state.input, next),
  { immediate: true }
);

onMounted(() => {
  inputElement.value.focus();
  dropdown.value = new Dropdown(inputElement.value);
});
</script>
