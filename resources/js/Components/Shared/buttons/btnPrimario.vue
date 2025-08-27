<script setup>
import { Head, usePage, Link } from "@inertiajs/vue3"; // <-- Añade Link
import { computed } from "vue";

const props = defineProps({
  usuario: {
    type: Object,
    default: null, // Es importante permitir que sea nulo
  },
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth.user);
</script>

<template>
  <button
    :class="[
      'flex items-center justify-center gap-1 py-2.5 px-5 text-[14px] font-semibold rounded-md hover:-translate-y-1 active:shadow-none active:scale-90 text-mono-blanco',
      isAuthenticated
        ? 'bg-primary shadow-[2px_8px_20px] shadow-primary hover:bg-secondary hover:shadow-secondary active:bg-transparent active:border active:text-primary'
        : 'shadow-[2px_8px_20px] shadow-universal-naranja bg-universal-naranja hover:bg-universal-azul_secundaria hover:shadow-universal-azul_secundaria active:bg-transparent active:border active:text-universal-naranja ',
    ]"
  >
    <slot></slot>
  </button>
</template>
