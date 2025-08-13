<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";

import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import ConfirmacionesPop from "@/Components/Modales/Confirmaciones/ConfirmacionesPop.vue";
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

const isModalConfirmated = ref(false);
function openConfirmatedModal() {
  isModalConfirmated.value = true;
}
function closeConfirmatedModal() {
  isModalConfirmated.value = false;
}
</script>

<template>
  <Head :title="`${authStore.aplicacion} ` || 'Dashboard'" />

  <SidebarSuperAdmin :auth="authStore">
    <MensajesLayout />
    <div class="">
      <h1 class="text-[30px] dark:text-mono-blanco text-mono-negro">Dashboard</h1>
    </div>
    <button @click="openConfirmatedModal()">confirmacion</button>
  </SidebarSuperAdmin>
  <ConfirmacionesPop :is-open="isModalConfirmated" @close="closeConfirmatedModal" />
</template>
