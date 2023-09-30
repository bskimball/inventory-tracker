<template>
  <form ref="form" @submit.prevent="handleSubmit">
    <div class="mb-3">
      <label for="partNumber" class="form-label">Part Number</label>
      <input
        type="text"
        :class="[
          'form-control',
          v$.part_number.$dirty && v$.part_number.$error && 'is-invalid',
        ]"
        id="partNumber"
        name="part_number"
        v-model="input.part_number"
      />
    </div>
    <div class="mb-3">
      <label for="lotNumber" class="form-label">Lot Number</label>
      <input
        type="text"
        :class="[
          'form-control',
          v$.lot_number.$dirty && v$.lot_number.$error && 'is-invalid',
        ]"
        id="lotNumber"
        name="lot_number"
        v-model="input.lot_number"
      />
    </div>
    <div class="mb-3">
      <label for="quantity" class="form-label">Quantity</label>
      <input
        type="text"
        :class="[
          'form-control',
          v$.quantity.$dirty && v$.quantity.$error && 'is-invalid',
        ]"
        id="quantity"
        name="quantity"
        v-model.number="input.quantity"
      />
    </div>
    <div class="mb-3">
      <button type="submit" class="btn btn-primary" :disabled="state.submitting">Submit</button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, watch, inject } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minValue } from "@vuelidate/validators";
import http from "../utils/http";
import sendToast from "../utils/sendToast";
import { addProduct, releaseProduct } from "../repository/products";
import { useRoute } from "vue-router";

const route = useRoute();
const emit = defineEmits(["submit", "update:modelValue"]);
const props = defineProps({
  modelValue: Object,
  action: {
    type: String,
    default: "Capture",
    validator(value) {
      return ["Capture", "Release"].includes(value);
    },
  },
});
const scannerHasFocus = inject("scannerHasFocus");

// main form
const form = ref(null);

// general component state
const state = reactive({
  submitting: false,
});

// form stuff
const input = reactive({
  part_number: "",
  lot_number: "",
  quantity: 0,
});
const rules = {
  part_number: { required },
  lot_number: { required },
  quantity: { required, minValue: minValue(1) },
};
const v$ = useVuelidate(rules, input);

async function sendInput() {
  await emit("submit", input);
  const { lot_number, part_number, quantity } = input;
  const { data } = await http.get(
    `/api/products?filter[part_number]=${part_number}&filter[lot_number]=${lot_number}&filter[quantity]=${quantity}`
  );

  if (route.query.action && route.query.action.toLowerCase() === "release") {
    if (data.length < 1) {
      return sendToast({
        title: "Not in the system",
        body: `A product with lot number ${lot_number} and part number ${part_number} is not registered in the system.`,
      });
    }
    await releaseProduct(data[0]);
  } else {
    if (data.length > 0) {
      return sendToast({
        title: "Already exists",
        body: `A product with lot number ${lot_number} and part number ${part_number} already exists in the system.`,
      });
    }
    await addProduct({ lot_number, part_number, quantity });
  }

  input.part_number = "";
  input.lot_number = "";
  input.quantity = 0;
  v$.value.$reset();
}

// main form submit action
async function handleSubmit() {
  // is form valid?
  const valid = await v$.value.$validate();

  // if not don't submit
  if (!valid) return;

  // full send
  state.submitting = true;
  await sendInput();
  state.submitting = false;
}

watch(
  input,
  (next) => {
    emit("update:modelValue", next);

    // pluck serial_number from object
    const { serial_number, ...rest } = next;

    // if input is empty skip validation
    if (Object.keys(rest).every((key) => !rest[key])) return;

    // if we're not using the scanner, we don't want to auto-submit
    if (!scannerHasFocus.value) return;

    // validate form on input change, send if valid
    v$.value.$validate().then((valid) => {
      if (valid) {
        setTimeout(() => {
          sendInput();
        }, 200);
      } else {
        return true;
      }
    });
  },
  { deep: true }
);

watch(
  () => props.modelValue,
  (next) => {
    Object.assign(input, next);
  },
  { deep: true, immediate: true }
);
</script>
