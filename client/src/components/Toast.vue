<script setup>
import { onMounted, ref } from "vue";
import Toast from "bootstrap/js/dist/toast";

const props = defineProps({
  title: {
    type: String,
    default: "title",
  },
  body: {
    type: String,
    default: "body",
  },
});

const toast = ref(null);

onMounted(() => {
  new Toast(toast.value).show();
});
</script>

<template>
  <teleport to="#toastContainer">
    <div
      ref="toast"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
      class="toast"
    >
      <div class="toast-header">
        <span class="h6 me-auto">{{ title }}</span>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="toast"
          aria-label="Close"
        ></button>
      </div>
      <div class="toast-body">{{ body }}</div>
    </div>
  </teleport>
</template>
