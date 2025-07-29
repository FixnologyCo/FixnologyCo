<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import Header from "@/Components/header/Header.vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
  inheritAttrs: false,
});

const props = defineProps({
  auth: {
    type: Object,
    required: true,
  },
  usuario: {
    type: Object,
    required: true,
  },
});

const {
  appName,
  bgClase,
  bgOpacity,
  textoClase,
  buttonFocus,
  buttonSecundario,
} = Colors();
</script>

<template>
  <Head :title="`${authStore.aplicacion} ` || 'Dashboard'" />

  <SidebarSuperAdmin :auth="authStore">
    <MensajesLayout />
    <div class="">
      <h1 class="text-[30px] dark:text-mono-blanco text-mono-negro">Dashboard</h1>

      <pre>{{ authStore.aplicacion }}</pre>
    </div>
  </SidebarSuperAdmin>
</template>
