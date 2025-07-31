<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { formatFecha, formatFechaShort } from "@/Utils/date";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
});
import useEstadoClass from "@/Composables/useEstado";
const { getEstadoClass } = useEstadoClass();

function obtenerIniciales(nombre, apellido) {
  const inicial1 = nombre?.charAt(0) || "";
  const inicial2 = apellido?.charAt(0) || "";
  return (inicial1 + inicial2).toUpperCase();
}

const props = defineProps({
  usuariosDelEstablecimiento: Array,
});

const seleccionado = ref(false);

const activeTab = ref(0);
const tabs = [{ label: "Clientes Fix" }, { label: "Empleados" }];

function irADetalle(id) {
  router.get(
    route("aplicacion.detallesUsuarios.id", {
      aplicacion: props.aplicacion,
      rol: props.rol,
      id: id,
    })
  );
}
</script>

<template>
  <div>
    <Head title="Gestor de usuarios" />
    <MensajesLayout />

    <div class="flex">
      <SidebarSuperAdmin :authStore>
        <div class="w-full">
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
                      ? 'text-primary border-b-2 border-b-primary'
                      : 'text-secundary-default dark:text-secundary-light dark:hover:text-primary',
                  ]"
                >
                  {{ tab.label }}
                </button>
              </nav>
            </div>

            <div
              v-if="activeTab === 0"
              class="flex gap-5 w-full min-h-[75dvh] max-h-[80dvh] overflow-auto"
            >
              <div class="filtro w-[20%]">
                <div class="header flex items-center justify-between">
                  <h1 class="text-[30px] font-bold">Fixgis</h1>
                  <div
                    class="contador border border-secundary-light rounded-md px-2 py-1 text-[12px]"
                  >
                    {{ usuariosDelEstablecimiento.length }} Total usuarios
                  </div>
                </div>
                <div
                  class="contenidoFiltro bg-secundary-opacity rounded-lg border border-secundary-light py-2 px-3 mt-3"
                >
                  <label for="" class="text-[14px] text-secundary-light"
                    >Estado cliente</label
                  >
                </div>
              </div>
              <div class="contenido w-[80%]">
                <div class="header flex items-end justify-between">
                  <div
                    class="flex items-center gap-2 border p-2 rounded-xl bg-mono-negro_opacity_medio backdrop-blur-xl w-[70%]"
                  >
                    <span class="material-symbols-rounded">explore</span>
                    <input
                      type="search"
                      name="search"
                      placeholder="Buscar usuario"
                      id=""
                      class="bg-transparent outline-none w-full"
                    />
                    <p>|</p>
                    <div class="flex gap-1 text-[12px] hover:text-primary cursor-pointer">
                      <span class="material-symbols-rounded text-[16px]">ar_on_you</span>
                      <p>Escanear</p>
                    </div>
                  </div>
                  <div
                    class="flex items-center gap-3 rounded-xl bg-mono-negro_opacity_medio"
                  >
                    <div class="">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl"
                      >
                        lists
                      </button>

                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl"
                      >
                        grid_view
                      </button>
                    </div>

                    <div class="text-[30px]">|</div>
                    <div
                      class="flex gap-2 items-center rounded-xl bg-mono-negro_opacity_medio"
                    >
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-secundary-light hover:text-primary hover:border-primary rounded-xl"
                      >
                        atr
                      </button>
                      <button
                        class="py-2 px-5 text-[14px] bg-primary shadow-[0_5px_20px] shadow-primary rounded-xl"
                      >
                        Agregar cliente
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 1">
              <pre class="text-mono-blanco">usuario:{{ usuariosDelEstablecimiento }}</pre>
            </div>
          </div>
        </div>
      </SidebarSuperAdmin>
    </div>
  </div>
</template>
