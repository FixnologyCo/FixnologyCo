<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { defineProps, computed, ref } from "vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Header from "@/Components/header/Header.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { formatFecha } from "@/Utils/date";
import { formatCOP } from "@/Utils/formateoMoneda";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useTiempo } from "@/Composables/useTiempo";
const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
  inheritAttrs: false,
});

const page = usePage();

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

const usuario = authStore.user;
const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

const { tiempoActivo, diasRestantes } = useTiempo(usuario);

const inicialesNombreUsuario = computed(() => {
  const nombres = authStore.primerNombre || "";
  const apellidos = authStore.primerApellido || "";
  const firstNameInitial = nombres.split(" ")[0]?.charAt(0).toUpperCase() || "";
  const lastNameInitial = apellidos.split(" ")[0]?.charAt(0).toUpperCase() || "";
  return firstNameInitial + lastNameInitial;
});

const activeTab = ref(0);
const tabs = [
  { label: "Cliente" },
  { label: "Tienda" },
  { label: "Plan" },
  { label: "Membresía" },
  { label: "Ajustes avanzados" },
];

function getEstadoClass(estado) {
  if (estado === "Inactivo" || estado === "Pendiente")
    return "bg-semaforo-rojo shadow-rojo";
  if (estado === "Suspendido") return "bg-semaforo-amarillo shadow-amarillo";
  if (estado === "Activo" || estado === "Pagada") return "bg-semaforo-verde shadow-verde";
  return "";
}

const logout = () => {
  router.visit(route("logout"), {
    method: "post",
    preserveScroll: true,
  });
};
</script>

<template>
  <div>
    <MensajesLayout />
    <Head title="Configuraciones" />

    <div class="flex">
      <SidebarSuperAdmin :auth="authStore">
        <div class="contenido px-3 max-h-[90vh] w-full overflow-auto scrollbar-custom">
          <div class="banner w-full min-h-[150px] rounded-lg" :class="[bgOpacity]"></div>
          <div
            class="flex items-end justify-between encabezado-config h-[auto] py-10 mx-12"
          >
            <div class="left-foto -mt-[120px] flex items-end gap-4">
              <div
                class="grid place-content-center foto w-[230px] h-[230px] rounded-[55px] bg-secundary-opacity dark:bg-mono-blanco backdrop-blur-lg"
              >
                <template v-if="authStore.rutaFoto  !== 'Sin foto'">
                  <div class="relative w-[220px] h-[220px] group">
                    <img
                      :src="authStore.rutaFoto"
                      class="rounded-[50px] w-full h-full object-cover shadow-lg"
                    />
                  </div>
                </template>

                <template v-else>
                  <div
                    class="p-2 w-[220px] h-[220px] rounded-[50px] grid place-content-center"
                    :class="[bgClase]"
                  >
                    <p class="text-[60px] font-semibold">{{ inicialesNombreUsuario }}</p>
                  </div>
                </template>
              </div>

              <div class="nombre">
                <h3
                  class="font-semibold text-[30px] text-mono-negro dark:text-mono-blanco"
                >
                  {{ authStore.nombreCompleto }}
                </h3>

                <div class="flex items-center justify-between">
                  <p
                    id="rol-usuario"
                    class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco py-1"
                  >
                    <span
                      class="material-symbols-rounded text-[20px] text-universal-azul_secundaria"
                      >local_police</span
                    >
                    {{ authStore.rol || "Sin rol" }}
                  </p>
                  <div
                    class="flex items-center gap-1 shadowM text-mono-negro font-semibold bg-universal-azul_secundaria w-[auto] py-1 px-2 rounded-md"
                  >
                    {{ authStore.membresia }}
                    <span class="material-symbols-rounded text-[18px]">bolt</span>
                  </div>
                </div>

                <div class="botonesConfig my-3 flex gap-2 items-center">
                  <a
                    :href="
                      route('aplicacion.configuraciones.editarMiPerfil', {
                        aplicacion,
                        rol,
                      })
                    "
                  >
                    <button class="" :class="buttonClase">Editar perfil</button>
                  </a>
                </div>
              </div>
            </div>

            <div class="right-info">
              <div class="datos-recurentes flex items-end flex-col gap-2">
                <div class="dias-restante text-right w-auto rounded-md">
                  <h4 class="text-secundary-default dark:text-mono-blanco">
                    Tu membresía finaliza en:
                  </h4>
                  <p
                    class="text-[35px] font-semibold -mt-3 text-secundary-default dark:text-mono-blanco"
                  >
                    {{ diasRestantes
                    }}<span
                      class="text-[14px] text-secundary-default dark:text-mono-blanco"
                      >Días</span
                    >
                  </p>
                </div>
                <div class="dias-activo w-auto rounded-md">
                  <h4 class="text-right text-secundary-default dark:text-mono-blanco">
                    Te uniste a la familia:
                  </h4>
                  <p
                    class="text-[35px] font-semibold -mt-3 text-secundary-default dark:text-mono-blanco"
                  >
                    {{ tiempoActivo }} <span class="text-[14px]"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="w-full px-12">
            <div class="mb-6">
              <nav class="-mb-px flex space-x-4">
                <button
                  v-for="(tab, index) in tabs"
                  :key="index"
                  @click="activeTab = index"
                  :class="[
                    'text-md font-medium px-4 py-2',
                    activeTab === index
                      ? textoClase + ' ' + borderClase
                      : 'text-secundary-default dark:text-secundary-light ' + hoverTexto,
                  ]"
                >
                  {{ tab.label }}
                </button>
              </nav>
            </div>
          </div>
          <div class="mx-12" v-if="activeTab === 0">
            <pre>{{ authStore.primerNombre }}</pre>
          </div>
        </div></SidebarSuperAdmin
      >
    </div>
  </div>
</template>
