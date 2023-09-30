<template>
  <div id="scan">
    <div class="mb-3">
      <ScanToggle v-model="state.action" />
    </div>
    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <form @submit.prevent="scanSubmit">
            <div class="form-text mb-2">
              <div
                v-if="scannerHasFocus"
                class="text-success d-flex align-items-center"
              >
                <span class="dot me-2 bg-success"></span
                ><span>Scanning is ready</span>
              </div>
              <div v-else class="text-warning d-flex align-items-center">
                <span class="dot me-2 bg-warning"></span
                ><span>Scanning not ready</span>
              </div>
            </div>
            <label for="scannerInput" class="form-label">Scanner Input</label>
            <input
              ref="scannerInput"
              type="text"
              class="form-control"
              id="scannerInput"
              v-model="scanData"
              @focus="scannerHasFocus = true"
              @blur="scannerHasFocus = false"
              autocomplete="off"
            />
            <div class="form-text d-flex text-info">
              <div class="me-1">
                <FontAwesomeIcon :icon="faInfoCircle" />
              </div>
              <p v-if="!scannerHasFocus">
                click on input above to enable scanning
              </p>
              <p v-else>point scanner to bar code label and pull trigger</p>
            </div>
          </form>
        </div>
        <hr />
        <div class="mb-3">
          <ProductNavigation />
          <div class="tab-content py-3">
            <div
              :class="[
                'tab-pane fade',
                $route.query.type === 'brackets' && 'show active',
              ]"
              role="tabpanel"
            >
              <BracketForm v-model="state.input" />
            </div>
            <div
              :class="[
                'tab-pane fade',
                $route.query.type === 'vinyl' && 'show active',
              ]"
              role="tabpanel"
            >
              <VinylForm v-model="state.input" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch, provide } from "vue";
import { useRoute, useRouter } from "vue-router";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons";
import ProductNavigation from "../components/ProductNavigation.vue";
import ScanToggle from "../components/ScanToggle.vue";
import VinylForm from "../components/VinylForm.vue";
import BracketForm from "../components/BracketForm.vue";

const router = useRouter();
const route = useRoute();

// view state
// focused input for scanner and ref for data
const scannerInput = ref(null);
const scanData = ref("");
const scannerHasFocus = ref(false);
const state = reactive({
  action: "Capture",
  type: "Vinyl",
  input: {
    lot_number: "",
    part_number: "",
    serial_number: "",
    quantity: 0,
  },
});
provide("scannerHasFocus", scannerHasFocus);

// check if letters exist in string
function hasLetters(string) {
  const regExp = /[a-zA-Z]/g;
  return regExp.test(string);
}

// data from the scanner is sent to the correct input
// there is a slight delay so the user can see the data in the scanner input first
function scanSubmit() {
  if (scanData.value.length === 9) {
    // brackets have a 9 digit serial number
    state.type = "brackets";
    router.replace({ query: { ...route.query, type: "brackets" } });
    state.input.serial_number = scanData.value;
  } else {
    state.type = "vinyl";
    router.replace({ query: { ...route.query, type: "vinyl" } });
    if (hasLetters(scanData.value)) {
      // only part numbers contain letters
      state.input.part_number = scanData.value;
    } else {
      if (scanData.value.length > 6) {
        // right now we're assuming there aren't over 100,000 parts and this is a lot number
        // must be the lot number
        state.input.lot_number = scanData.value;
      } else {
        // less than 6 digits is likely a quantity
        state.input.quantity = parseInt(scanData.value);
      }
    }
  }

  setTimeout(() => {
    // slight delay
    // clear input
    scanData.value = "";
    // re-focus for the next scan
    scannerInput.value.focus();
  }, 100);
}

function setUrl() {
  if (route.query.type) {
    state.type = route.query.type;
  }

  if (route.query.action) {
    state.action = route.query.action;
  }

  router.replace({
    query: {
      ...route.query,
      action: state.action.toLowerCase(),
      type: state.type.toLowerCase(),
    },
  });
}

onMounted(() => {
  scannerInput.value.focus();
});

watch(
  () => state.action,
  () => {
    setTimeout(() => scannerInput.value.focus(), 150);
  }
);

watch(
  () => route.params,
  () => setUrl(),
  { immediate: true }
);
</script>
