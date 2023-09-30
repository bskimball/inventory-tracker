import { createApp } from "vue";
import Toast from "../components/Toast.vue";

export default function ({ title, body }) {
  const wrapper = document.createElement("div");
  createApp(Toast, { title, body }).mount(wrapper);
}
