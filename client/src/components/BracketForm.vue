<template>
  <form ref="form" @submit.prevent="handleSubmit">
    <div class="mb-3">
      <label for="serialNumber" class="form-label">Serial Number</label>
      <input
        type="text"
        :class="[
          'form-control',
          v$.serial_number.$dirty && v$.serial_number.$error && 'is-invalid',
        ]"
        id="serialNumber"
        name="serial_number"
        v-model="input.serial_number"
      />
    </div>
    <div class="mb-3">
      <button class="btn btn-primary" type="submit" :disabled="state.submitting">Submit</button>
    </div>
  </form>
</template>

<script setup>
import { inject, reactive, ref, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength } from "@vuelidate/validators";
import http from "../utils/http.js";
import sendToast from "../utils/sendToast.js";
import { addProduct, releaseProduct } from "../repository/products.js";
import { useRoute } from "vue-router";

const route = useRoute();

// props, injections and events
const props = defineProps({
  modelValue: Object,
});
const emits = defineEmits(["submit", "update:modelValue"]);
const scannerHasFocus = inject("scannerHasFocus");

// the form and state
const form = ref(null);
const state = reactive({
  submitting: false,
});

const input = reactive({ serial_number: "" });
const rules = {
  serial_number: { required, minLength: minLength(9), maxLength: maxLength(9) },
};
const v$ = useVuelidate(rules, input);

// actions
async function sendInput() {
  emits("submit", input);

  const { serial_number } = input;
  const { data } = await http.get(
    `/api/products?filter[serial_number]=${serial_number}`
  );

  if (route.query.action && route.query.action.toLowerCase() === "release") {
    if (data.length < 1) {
      return sendToast({
        title: "Not in the system",
        body: `A product with serial number ${serial_number} is not registered in the system.`,
      });
    }
    await releaseProduct(data[0]);
  } else {
    if (data.length > 0) {
      return sendToast({
        title: "Already exists",
        body: `A product with serial number ${serial_number} already exists in the system.`,
      });
    }
    await addProduct({ serial_number });
  }

  input.serial_number = "";
  v$.value.$reset();
}

async function handleSubmit() {
  const valid = await v$.value.$validate();

  if (!valid) return;

  state.submitting = true;
  await sendInput();
  state.submitting = false;
}

watch(
  input,
  (next) => {
    emits("update:modelValue", next);

    if (!input.serial_number) return;

    if (!scannerHasFocus.value) return;

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
  (next) => Object.assign(input, next),
  { deep: true, immediate: true }
);
</script>
