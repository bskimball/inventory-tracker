import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", redirect: "/scan" },
    { path: "/scan", component: () => import("./views/Scan.vue") },
    { path: "/lookup", component: () => import("./views/Lookup.vue") },
    { path: "/counter", component: () => import("./views/Counter.vue") },
  ],
});

export default router;
