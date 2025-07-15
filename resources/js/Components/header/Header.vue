<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Colors from "@/Composables/ModularColores";
import { useTema } from "@/Composables/useTema";
const { modoOscuro, animando, animarCambioTema } = useTema();
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"; // 1. Importa tu layout
import { useAuthStore } from "@/stores/auth"; // 1. Importa la tienda
const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
});

const {
  NombreApp,
  bgClase,
  textoClase,
  focus,
  buttonLink,
  hoverTexto,
  hoverClase,
} = Colors();

const page = usePage();

const aplicacion = authStore.aplicacion || "Sin app";
const rol = authStore.rol || "Sin rol";

const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);
const dashboardRoute = computed(
  () =>
    new URL(route("aplicacion.dashboard", { aplicacion, rol }), window.location.origin)
      .pathname
);
const clientesFixRoute = computed(
  () =>
    new URL(route("aplicacion.clientesFix", { aplicacion, rol }), window.location.origin)
      .pathname
);
const configuracionesRoute = computed(
  () =>
    new URL(
      route("aplicacion.configuraciones", { aplicacion, rol }),
      window.location.origin
    ).pathname
);
// const historialRoute = computed(() => new URL(route('aplicacion.historial', { aplicacion, rol }), window.location.origin).pathname);

const iconosPorComponente = {
  Dashboard: "dashboard",
  MisApps: "apps",
  GestorUsuarios: "group",
  DetallesUsuario: "digital_wellbeing",
  Productos: "inventory",
  Reportes: "bar_chart",
  Historial: "history",
  Configuraciones: "settings",
  EditarMiPerfil: "account_circle",
};

const componente = usePage().component.split("/").pop();

function separarCamelCase(texto) {
  const conEspacios = texto.replace(/([a-z])([A-Z])/g, "$1 $2");
  return conEspacios.charAt(0).toUpperCase() + conEspacios.slice(1).toLowerCase();
}

const ruta = separarCamelCase(componente);
const icono = iconosPorComponente[componente] || "description";

const initials = computed(() => {
  const nombres = authStore.primerNombre || "";
  const apellidos = authStore.primerApellido || "";

  const firstNameInitial = nombres.split(" ")[0]?.charAt(0).toUpperCase() || "";
  const lastNameInitial = apellidos.split(" ")[0]?.charAt(0).toUpperCase() || "";

  return firstNameInitial + lastNameInitial;
});
</script>

<template>
  <header
    class="header px-3 py-2 flex items-center justify-between gap-3 bg-mono-blanco shadow-md dark:bg-mono-negro"
  >
    <div class="left w-[20%] rounded-md">
      <div class="infoTienda flex items-center gap-2">
        <div class="">
          <div class="flex items-center" v-if="authStore && authStore">
            <span
              class="material-symbols-rounded align-middle mr-1"
              :class="[textoClase]"
            >
              {{ icono }}
            </span>
            <h1 class="text-[25px] font-semibold text-mono-negro dark:text-mono-blanco">
              {{ ruta }}
            </h1>
          </div>
          <div v-else>
            <p class="text-mono-negro dark:text-mono-blanco">
              Cargando información del usuario...
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-2 items-center">
      <button
        @click="animarCambioTema"
        class="flex items-center justify-center gap-2 h-[35px] w-[35px] rounded-full border border-secundary-light text-sm transition-all duration-500 ease-in-out relative overflow-hidden"
        :class="[
          modoOscuro ? 'text-mono-blanco' : 'text-mono-negro',
          animando ? 'scale-105 shadow-lg rotate-1' : '',
        ]"
      >
        <span
          class="material-symbols-rounded text-[20px] transition-transform duration-500"
          :class="{ 'animate-spin-slow': animando }"
        >
          {{ modoOscuro ? "light_mode" : "dark_mode" }}
        </span>
        <!-- destello -->
        <span
          v-if="animando"
          class="absolute inset-0 bg-white/10 backdrop-blur-sm animate-ping z-0 rounded-md"
        ></span>
      </button>

      <!-- <a :href="route('aplicacion.historial', { aplicacion, rol })">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
                    :class="[currentRoute === historialRoute ? [bgClase] : 'bg-transparent' , hoverClase]">
                    <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
                        history
                    </span>
                </div>
            </a> -->

      <div
        class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
        :class="[hoverClase]"
      >
        <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
          help
        </span>
      </div>

      <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
        <div
          class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
          :class="[hoverClase]"
        >
          <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
            notifications
          </span>
        </div>
      </a>
      <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
        <div
          class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
          :class="[
            currentRoute === configuracionesRoute ? [bgClase] : 'bg-transparent',
            hoverClase,
          ]"
        >
          <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
            settings
          </span>
        </div>
      </a>

      <div class="flex gap-1 items-center relative">
        <div class="" v-if="authStore.isAuthenticated === true">
          <div
            class="bg-semaforo-verde w-2.5 h-2.5 absolute top-0 left-1 z-10 shadow shadow-semaforo-verde rounded-full"
          ></div>
        </div>
        <div v-else>
          <div
            class="bg-semaforo-rojo w-2.5 h-2.5 absolute top-0 left-1 z-10 shadow shadow-semaforo-rojo rounded-full"
          ></div>
        </div>

        <template v-if="authStore.rutaFoto !== null">
          <img
            :src="authStore.rutaFoto"
            class="border-2 rounded-[50px] w-[40px] h-[40px] object-cover"
          />
        </template>

        <template v-else="authStore">
          <div
            class="user relative bg-universal-naranja shadow shadow-universal-naranja text-mono-blanco h-[35px] w-[35px] rounded-full overflow-hidden flex items-center justify-center"
            :class="[bgClase]"
          >
            <span class="text-[12px] font-bold">
              {{ initials }}
            </span>
          </div>
        </template>
        <div class="usuario">
          <div v-if="authStore && authStore">
            <h3 class="font-semibold text-[13px] text-mono-negro dark:text-mono-blanco">
              {{ authStore.nombreCompleto }}
            </h3>
            <p
              class="-mt-[5px] text-[12px] font-medium text-mono-negro dark:text-secundary-light"
            >
              {{ authStore.rol || "Sin rol" }}
            </p>
          </div>
          <div v-else>
            <p class="text-mono-negro dark:text-mono-blanco">
              Cargando información del usuario...
            </p>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
