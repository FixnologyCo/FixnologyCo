<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import Header from "@/Components/header/Header.vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import Colors from "@/Composables/ModularColores";
const {
  appName,
  bgClase,
  bgOpacity,
  focus,
  textoClase,
  borderClase,
  buttonFocus,
  hoverClase,
  hoverTexto,
  buttonClase,
  buttonSecundario,
} = Colors();

import useEstadoClass from "@/Composables/useEstado";

const { getEstadoClass } = useEstadoClass();

const props = defineProps({
  auth: {
    type: Object,
    required: true,
  },
  clientes: {
    type: Array,
    default: () => [],
  },
  aplicacion: {
    type: String,
    default: "",
  },
  errors: {
    type: Object,
    required: true,
  },
  foto_base64: {
    type: String,
    default: "",
  },
});

const user = props.auth.user;
const auth = usePage().props.auth;

const activeTab = ref(0);
const tabs = [{ label: "Clientes Fix" }, { label: "Empleados" }];
</script>

<template>
  <Head title="Gestor de usuarios" />
  <MensajesLayout />

  <div class="flex">
    <SidebarSuperAdmin :auth="auth" />

    <div class="w-full">
      <Header :auth="auth" :foto_base64="foto_base64" />

      <div class="contenido p-3">
        <div class="mb-6">
          <nav class="-mb-px flex space-x-4">
            <button
              v-for="(tab, index) in tabs"
              :key="index"
              @click="activeTab = index"
              :class="[
                'text-[12px] font-medium px-4 py-2',
                activeTab === index
                  ? textoClase + ' ' + borderClase
                  : 'text-secundary-default dark:text-secundary-light ' + hoverTexto,
              ]"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <div v-if="activeTab === 0">
          <div class="headerClientes flex items-center justify-between">
            <div class="total flex items-center gap-2">
              <div
                class="icon bg-secundary-light p-2 rounded-md grid place-content-center text-mono-negro"
              >
                <span class="material-symbols-rounded">diversity_1</span>
              </div>
              <p class="text-[30px] font-semibold text-mono-blanco">
                0 <span class="text-secundary-light text-[14px]">Total clientes</span>
              </p>
            </div>

            <div class="end flex gap-5">
              <div class="filtro-vista flex gap-4">
                <button
                  class="text-[14px] rounded-md border p-2 flex items-center text-mono-blanco"
                >
                  <span class="material-symbols-rounded text-[20px]">filter_alt</span>
                  Filtros
                </button>
              </div>

              <button class="text-[14px]" :class="buttonClase">
                <span class="material-symbols-rounded">add_circle</span> Agregar cliente
              </button>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 1">
           <div class="headerEmpledos flex items-center justify-between">
            <div class="total flex items-center gap-2">
              <div
                class="icon bg-secundary-light p-2 rounded-md grid place-content-center text-mono-negro"
              >
                <span class="material-symbols-rounded">engineering</span>
              </div>
              <p class="text-[30px] font-semibold text-mono-blanco">
                0 <span class="text-secundary-light text-[14px]">Total empleados</span>
              </p>
            </div>

            <div class="end flex gap-5">
              <div class="filtro-vista flex gap-4">
                <button
                  class="text-[14px] rounded-md border p-2 flex items-center text-mono-blanco"
                >
                  <span class="material-symbols-rounded text-[20px]">filter_alt</span>
                  Filtros
                </button>
              </div>

              <button class="text-[14px]" :class="buttonClase">
                <span class="material-symbols-rounded">add_circle</span> Agregar empleado
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
