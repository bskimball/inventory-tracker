<script setup>
import Navigation from "./components/Navigation.vue";
import Navbar from "./components/Navbar.vue";
</script>

<template>
  <Navbar />
  <aside class="aside">
    <div class="container-fluid mb-4">
      <h6 class="text-muted">Menu</h6>
      <Navigation />
    </div>
  </aside>
  <main class="main">
    <div class="container-fluid">
      <RouterView />
    </div>
  </main>
  <footer class="footer"></footer>
  <div
    id="toastContainer"
    class="toast-container position-fixed p-3 bottom-0 start-0"
  ></div>
</template>
