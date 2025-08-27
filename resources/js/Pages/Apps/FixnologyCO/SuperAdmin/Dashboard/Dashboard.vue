<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";

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
</script>

<template>
  <Head :title="`${authStore.aplicacion} ` || 'Dashboard'" />

  <SidebarSuperAdmin :auth="authStore">
    <MensajesLayout />
    <div class="">
      <h1 class="text-[30px] dark:text-mono-blanco text-mono-negro">Dashboard</h1>
    </div>
  </SidebarSuperAdmin>
</template>
