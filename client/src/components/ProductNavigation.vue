<template>
  <ul class="nav nav-tabs" role="tablist">
    <li
      class="nav-item"
      v-for="item in items"
      :key="item.href"
      role="presentation"
    >
      <router-link
        :class="['nav-link', $route.query.type === item.href && 'active']"
        :to="{ path: $route.path, query: { ...$route.query, type: item.href } }"
        replace
      >
        {{ item.title }}
      </router-link>
    </li>
  </ul>
</template>

<script setup>
const items = [
  { href: `vinyl`, title: "Vinyl" },
  { href: `brackets`, title: "Brackets" },
];
</script>
