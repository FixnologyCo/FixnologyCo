<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

import Colors from "@/Composables/ModularColores";
import { useTema  } from "@/Composables/useTema";
const { modoOscuro, animando, animarCambioTema, backgroundStyle } = useTema();
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
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

const aplicacion = authStore.aplicacion || "Sin app";
const rol = authStore.rol || "Sin rol";

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

const isMenuOpen = ref(false);

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};
const logout = () => {
  router.visit(route("logout"), {
    method: "post",
    preserveScroll: true,
  });
};
</script>

<template>
  <header class="header flex items-center justify-between gap-3">
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
            <h1 class="text-[25px] font-medium text-mono-negro dark:text-mono-blanco">
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
          class="absolute inset-0 bg-mono-blanco/10 backdrop-blur-sm animate-ping z-0 rounded-md"
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
        <span
          class="material-symbols-rounded text-[20px] dark:text-mono-blanco text-mono-negro"
        >
          help
        </span>
      </div>

      <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
        <div
          class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
          :class="[hoverClase]"
        >
          <span
            class="material-symbols-rounded text-[20px] dark:text-mono-blanco text-mono-negro"
          >
            notifications
          </span>
        </div>
      </a>

      <div class="relative">
        <div class="cajaUser flex items-center">
          <div class="flex gap-1 items-center relative">
            <div v-if="authStore.isAuthenticated === true">
              <div
                class="bg-semaforo-verde w-2.5 h-2.5 absolute top-0 left-1 z-10 shadow shadow-semaforo-verde rounded-full"
              ></div>
            </div>
            <div v-else>
              <div
                class="bg-semaforo-rojo w-2.5 h-2.5 absolute top-0 left-1 z-10 shadow shadow-semaforo-rojo rounded-full"
              ></div>
            </div>

            <template v-if="authStore.rutaFoto !== 'Sin foto'">
              <img
                :src="authStore.rutaFoto"
                class="border-2 rounded-[50px] w-[40px] h-[40px] object-cover shadowM"
              />
            </template>

            <template v-else>
              <div
                class="user relative bg-universal-naranja shadow shadow-universal-naranja text-mono-blanco h-[35px] w-[35px] rounded-full overflow-hidden flex items-center justify-center"
                :class="[bgClase]"
              >
                <span class="text-[12px] font-bold">{{ initials }}</span>
              </div>
            </template>
          </div>

          <span
            @click="toggleMenu"
            class="material-symbols-rounded text-[16px] cursor-pointer"
            >keyboard_arrow_down</span
          >
        </div>

        <div
          v-if="isMenuOpen"
          class="absolute right-0 mt-2 w-52 bg-mono-blanco dark:bg-mono-negro_opacity_full rounded-xl shadow-lg p-3 z-20"
        >
          <div class="nombreUsuario px-4 py-2">
            <div class="flex items-center justify-between">
              <h3 class="text-mono-negro dark:text-mono-blanco">
                {{ authStore.primerNombre + " " + authStore.primerApellido }}
              </h3>
              <div class="grid place-items-center" v-if="authStore.google_id === null">
                <span class="material-symbols-rounded text-[18px] text-gray-700"
                  >verified_off</span
                >
              </div>
              <div class="" v-else>
                <span
                  class="grid place-items-center material-symbols-rounded text-[18px] text-universal-azul_secundaria"
                  >verified</span
                >
              </div>
            </div>
          </div>

          <a
            :href="route('aplicacion.configuraciones', { aplicacion, rol })"
            :class="hoverClase"
            class="flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco"
            ><span class="material-symbols-rounded text-[18px]">for_you</span> Mi
            Perfil</a
          >

          <a
            href="#"
            :class="hoverClase"
            class="flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco"
            ><span class="material-symbols-rounded text-[18px]">settings</span>
            Configuraciones</a
          >

          <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
          <a
            href="#"
            :class="hoverClase"
            class="flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco"
            ><span class="material-symbols-rounded text-[18px]"
              >deployed_code_update</span
            >
            Actualizaciones
          </a>
          <a
            @click="logout"
            :class="hoverClase"
            class="cursor-pointer flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco"
            ><span class="material-symbols-rounded text-[18px]">logout</span> Cerrar
            sesión</a
          >
        </div>
      </div>
    </div>
  </header>
</template>
