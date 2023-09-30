<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from "vue";
import Modal from "bootstrap/js/dist/modal";

const props = defineProps({ open: { type: Boolean, default: false } });
const emits = defineEmits(["update:open"]);

const modalEl = ref(null);
const modal = ref(null);

function modalHidden() {
  emits("update:open", false);
}

onMounted(() => {
  modal.value = new Modal(modalEl.value);
  modalEl.value.addEventListener("hidden.bs.modal", modalHidden);
});

onBeforeUnmount(() => {
  modalEl.value.removeEventListener("hidden.bs.modal", modalHidden);
});

watch(
  () => props.open,
  (next) => (next ? modal.value.show() : modal.value.hide())
);
</script>

<template>
  <div ref="modalEl" class="modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div v-if="$slots.header" class="modal-header">
          <h5 class="modal-title">
            <slot name="title"></slot>
          </h5>
          <button
            class="btn-close"
            aria-label="Close"
            @click="() => modal.hide()"
          ></button>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
        <div v-if="$slots.footer" class="modal-footer">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</template>
