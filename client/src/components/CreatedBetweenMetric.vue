<template>
  <div class="mb-3">
    <form @submit.prevent="handleSubmit">
      <label class="form-label">Scanned between</label>
      <div class="input-group">
        <input
          ref="fromInputElement"
          v-model="state.from"
          type="date"
          id="dateFrom"
          :class="[
            'form-control',
            v$.from.$dirty && v$.from.$error && 'is-invalid',
          ]"
          :max="state.to"
        />
        <input
          v-model="state.to"
          type="date"
          id="dateTo"
          :class="[
            'form-control',
            v$.to.$dirty && v$.to.$error && 'is-invalid',
          ]"
          :min="state.from"
        />
        <button class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, computed } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import dayjs from "dayjs";

const emits = defineEmits(["submit", "update:modelValue"]);

const fromInputElement = ref(null);

const state = reactive({
  to: dayjs().format("YYYY-MM-DD"),
  from: dayjs().subtract(7, "days").format("YYYY-MM-DD"),
});
const rules = computed(() => ({
  to: { required },
  from: { required },
}));
const v$ = useVuelidate(rules, state);

function handleSubmit() {
  v$.value.$validate().then((valid) => {
    if (valid) {
      emits("submit", [state.from, state.to]);
    }
  });
}

onMounted(() => {
  fromInputElement.value.focus();
});
</script>
