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

      <pre>{{
        authStore.user.establecimientos?.facturas?.[0]?.estado?.tipo_estado || "na"
      }}</pre>

      <!-- estadoFactura() { return this.establecimientos?.facturas?.[0]?.estado?.tipo_estado
      || null; }, medioPagoFactura() { return
      this.establecimientos?.facturas?.[0]?.medio_pago?.forma_pago || null; },
      montoFactura() { return this.establecimientos?.facturas?.[0]?.monto_total || null;
      }, diasRestantesMembresia() { return
      this.establecimientos?.facturas?.[0]?.dias_restantes || null; }, fechaPago() {
      return this.establecimientos?.facturas?.[0]?.fecha_pago || null; }, -->
    </div>
  </SidebarSuperAdmin>
</template>
