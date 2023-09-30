<template>
  <div class="form-check form-check-inline">
    <input
      type="radio"
      class="form-check-input"
      name="action"
      id="actionCapture"
      value="Capture"
      v-model="action"
    />
    <label for="actionCapture" class="form-check-label">Capture</label>
  </div>
  <div class="form-check form-check-inline">
    <input
      type="radio"
      class="form-check-input"
      name="action"
      id="actionRelease"
      value="Release"
      v-model="action"
    />
    <label for="actionRelease" class="form-check-label">Release</label>
  </div>
</template>

<script setup>
import { watch, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const props = defineProps({
  modelValue: {
    type: String,
    default: "Capture",
  },
});
const emit = defineEmits(["update:modelValue"]);
const action = ref(props.modelValue);

onMounted(() => {
  if (route.query.action) {
    action.value =
      route.query.action.charAt(0).toUpperCase() + route.query.action.slice(1);
  }
});

watch(action, (next) => {
  emit("update:modelValue", next);
  router.replace({
    query: { ...route.query, action: next.toLowerCase() },
  });
});
</script>
