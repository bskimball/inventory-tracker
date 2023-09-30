<script setup>
import { computed } from "vue";

const props = defineProps({
  pageSize: Number,
  total: Number,
  currentPage: Number,
});
const emits = defineEmits(["update:current-page"]);

const numberOfPages = computed(() => props.total / props.pageSize);

const fullArrayOfNumbers = computed(() => {
  return Array.from(
    { length: Math.ceil(numberOfPages.value) },
    (_, i) => i + 1
  );
});

const shownNumbers = computed(() => {
  if (numberOfPages.value < 5) {
    return fullArrayOfNumbers.value;
  }
  const first = [props.currentPage < 4 ? 2 : "..."];
  const middle = () => {
    let val = props.currentPage;
    if (props.currentPage < 3) {
      val = 3;
    }
    if (
      props.currentPage >
      fullArrayOfNumbers.value[fullArrayOfNumbers.value.length - 3]
    ) {
      val = fullArrayOfNumbers.value[fullArrayOfNumbers.value.length - 3];
    }
    return [val];
  };
  const last = [
    props.currentPage >
    fullArrayOfNumbers.value[fullArrayOfNumbers.value.length - 4]
      ? fullArrayOfNumbers.value[fullArrayOfNumbers.value.length - 2]
      : "...",
  ];

  return [...first, ...middle(), ...last];
});
</script>

<template>
  <nav aria-label="Page navigation example">
    <ul class="pagination">
      <li :class="['page-item', props.currentPage === 1 && 'disabled']">
        <a
          class="page-link"
          href="#"
          aria-label="Previous"
          @click.prevent="
            () => emits('update:current-page', props.currentPage - 1)
          "
        >
          <span aria-hidden="true">&lsaquo;</span>
        </a>
      </li>
      <li
        v-if="numberOfPages > 5"
        :class="['page-item', props.currentPage === 1 && 'active']"
      >
        <a
          href="#"
          aria-label="First"
          class="page-link"
          @click.prevent="() => emits('update:current-page', 1)"
        >
          {{ 1 }}
        </a>
      </li>
      <li
        v-for="num in shownNumbers"
        :class="[
          'page-item',
          num === props.currentPage && 'active',
          num === '...' && 'disabled',
        ]"
        :aria-current="num === props.currentPage ? 'page' : undefined"
      >
        <a
          class="page-link"
          href="#"
          @click.prevent="() => emits('update:current-page', num)"
        >
          {{ num }}
        </a>
      </li>
      <li
        v-if="numberOfPages > 5"
        :class="[
          'page-item',
          props.currentPage ===
            fullArrayOfNumbers[fullArrayOfNumbers.length - 1] && 'active',
        ]"
      >
        <a
          href="#"
          aria-label="Last"
          class="page-link"
          @click.prevent="
            () =>
              emits(
                'update:current-page',
                fullArrayOfNumbers[fullArrayOfNumbers.length - 1]
              )
          "
        >
          {{ fullArrayOfNumbers[fullArrayOfNumbers.length - 1] }}
        </a>
      </li>
      <li
        :class="[
          'page-item',
          props.currentPage ===
            fullArrayOfNumbers[fullArrayOfNumbers.length - 1] && 'disabled',
        ]"
      >
        <a
          class="page-link"
          href="#"
          aria-label="Next"
          @click.prevent="
            () => emits('update:current-page', props.currentPage + 1)
          "
        >
          <span aria-hidden="true">&rsaquo;</span>
        </a>
      </li>
    </ul>
  </nav>
</template>
