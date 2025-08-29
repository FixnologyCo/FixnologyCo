<script setup>
import { Head, usePage, Link } from "@inertiajs/vue3"; // <-- Añade Link
import { computed } from "vue";

const props = defineProps({
  usuario: {
    type: Object,
    default: null,
  },
  label: { type: String, default: "Button primary", required: true },
  icon: { type: String, default: "Keyboard_double_arrow_right" },
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth.user);
</script>

<template>
  <!-- <button
    :class="[
      'flex items-center justify-center gap-1 py-2.5 px-5 text-[14px] font-semibold rounded-md hover:-translate-y-1 active:shadow-none active:scale-90 text-mono-blanco',
      isAuthenticated
        ? 'bg-primary shadow-[2px_8px_20px] shadow-primary hover:bg-secondary hover:shadow-secondary active:bg-transparent active:border active:text-primary'
        : 'shadow-[2px_8px_20px] shadow-universal-naranja bg-universal-naranja hover:bg-universal-azul_secundaria hover:shadow-universal-azul_secundaria active:bg-transparent active:border active:text-universal-naranja ',
    ]"
  >
    <span>{{ label }}</span>
    <i class="material-symbols-rounded">{{ icon }}</i>
  </button> -->

  <!-- From Uiverse.io by Centered101 -->
  <button
    type="submit"
    :class="[
      'relative bottom-0 flex justify-center items-center gap-2 rounded-[8px] font-medium px-8 py-2.5 z-10 overflow-hidden ease-in-out duration-700 group hover:text-primary hover:bg-mono-blanco active:scale-95 active:duration-0 focus:bg-mono-blanco focus:text-mono-negro isolation-auto before:absolute before:w-full before:transition-all before:duration-700 before:hover:w-full before:-left-full before:hover:left-0 before:rounded-full before:bg-mono-blanco before:-z-10 before:aspect-square before:hover:scale-150 before:hover:duration-700 ]',
      isAuthenticated
        ? 'bg-primary text-mono-blanco shadow-[2px_4px_15px] shadow-primary hover:shadow-mono-blanco active:bg-transparent active:border active:text-primary'
        : 'bg-universal-naranja text-mono-blanco shadow-[2px_4px_15px] shadow-universal-naranja hover:shadow-mono-blanco active:bg-transparent active:border active:text-universal-naranja',
    ]"
  >
    <span
      class="truncate ease-in-out duration-300 group-active:-translate-x-[1000px] group-focus:translate-x-96"
      >{{ label }}</span
    >
    <div
      class="absolute flex flex-row justify-center items-center gap-3 -translate-x-[1000px] ease-in-out duration-300 group-active:translate-x-0 group-focus:translate-x-0"
    >
      <div
        class="animate-spin size-4 border-2 border-mono-negro border-t-transparent rounded-full"
      ></div>
      Cargando...
    </div>
    <i
      class="material-symbols-rounded text-[18px] group-active:-translate-x-[1000px] group-focus:translate-x-[1000px]"
      >{{ icon }}</i
    >
  </button>
</template>
