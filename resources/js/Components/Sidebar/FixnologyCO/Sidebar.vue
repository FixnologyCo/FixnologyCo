<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Colors from "@/Composables/ModularColores";
import { useSidebar } from "@/Composables/useSidebar";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
});

import Header from "@/Components/header/Header.vue";
const { sidebarExpandido, toggleSidebar } = useSidebar();
const {
  NombreApp,
  bgClase,
  bgOpacity,
  textoClase,
  focus,
  buttonLink,
  hoverClase,
} = Colors();

const page = usePage();

const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);
const dashboardRoute = computed(
  () =>
    new URL(route("aplicacion.dashboard", { aplicacion, rol }), window.location.origin)
      .pathname
);
const gestorUsuarios = computed(
  () =>
    new URL(
      route("aplicacion.gestorUsuarios", { aplicacion, rol }),
      window.location.origin
    ).pathname
);

const inicialesNombreEstablecimiento = computed(() => {
  const nombreEstablecimiento = authStore.nombreEstablecimiento || "Tienda de Ejemplo";

  const palabrasAIgnorar = ["de", "el", "la", "los", "las", "y", "a"];

  const iniciales = nombreEstablecimiento

    .split(" ")

    .filter((palabra) => !palabrasAIgnorar.includes(palabra.toLowerCase()))

    .map((palabra) => palabra[0])
    .join("");

  return iniciales.toUpperCase().slice(0, 2);
});
</script>

<template>
  <div
    class="dark:bg-[url('/resources/images/01.webp')] bg-[url('/resources/images/02.webp')] bg-cover w-full min-h-[100vh]"
  >
    <div class="p-4 flex backdrop-blur-md w-full min-h-[100vh] rounded-xl">
      <div class="sidebar pr-3 py-2" :class="[sidebarExpandido ? 'w-[12%]' : 'w-[45px]']">
        <div
          class="headerSidebar"
          :class="[
            sidebarExpandido
              ? 'flex justify-between items-start'
              : 'flex flex-col-reverse items-center justify-center gap-3',
          ]"
        >
          <template v-if="authStore.fotoUrlCompletaEstablecimiento">
            <div class="relative group w-[28px] h-[28px]">
              <img
                :src="authStore.fotoUrlCompletaEstablecimiento"
                class="rounded-[5px] w-full h-full object-cover shadow-lg"
              />
            </div>
            <p v-if="sidebarExpandido" class="text-mono-blanco">
              {{ authStore.nombreEstablecimiento }}
            </p>
          </template>

          <template v-else>
            <div
              class="relative flex justify-center rounded-[5px] items-center group w-[28px] h-[28px] bg-primary shadow-[0px_8px_20px] shadow-primary"
            >
              <p class="font-semibold text-mono-blanco">
                {{ inicialesNombreEstablecimiento }}
              </p>
            </div>
            <p v-if="sidebarExpandido" class="text-mono-blanco">
              {{ authStore.nombreEstablecimiento }}
            </p>
          </template>

          <button @click="sidebarExpandido = !sidebarExpandido" class="">
            <span class="material-symbols-rounded text-mono-blanco hover:text-primary">
              {{ sidebarExpandido ? "arrow_menu_close" : "arrow_menu_open" }}
            </span>
          </button>
        </div>

        <div class="navegacion mt-10">
          <ul class="flex flex-col gap-1">
            <li
              :class="
                ([currentRoute === dashboardRoute ? 'bg-red-400' : 'bg-blue-400'],
                [sidebarExpandido ? 'justify-between' : 'justify-center'])
              "
              class="py-2 px-3 rounded-full cursor-pointer flex items-center hover:bg-primary hover:bg-opacity-50 hover:shadow-[0px_4px_20px] hover:shadow-primary"
            >
              <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                <span class="flex items-center justify-between">
                  <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center">
                      <span class="text-[18px] material-symbols-rounded text-mono-blanco"
                        >space_dashboard</span
                      >
                    </div>
                    <span v-if="sidebarExpandido" class="text-[14px] text-mono-blanco"
                      >Dashboard</span
                    >
                  </div>
                  <div
                    v-if="sidebarExpandido"
                    :class="[
                      currentRoute === dashboardRoute
                        ? 'w-4 h-2.5 mx-3 bg-primary rounded-full'
                        : null,
                    ]"
                  ></div>
                </span>
              </a>
            </li>

            <div
              v-if="!sidebarExpandido"
              class="text-center text-mono-blanco dark:text-mono-blanco text-[12px]"
            >
              -
            </div>
            <p
              v-if="sidebarExpandido"
              class="text-mono-blanco dark:text-mono-blanco text-[12px] my-2.5"
            >
              Gestión
            </p>
            <li
              :class="
                ([currentRoute === gestorUsuarios ? [bgOpacity] : 'bg-transparent'],
                [sidebarExpandido ? 'justify-between' : 'justify-center'])
              "
              class="py-2 px-3 rounded-full cursor-pointer flex items-center hover:bg-primary hover:shadow-[0px_8px_20px] hover:shadow-primary"
            >
              <a :href="route('aplicacion.gestorUsuarios', { aplicacion, rol })">
                <span class="flex items-center">
                  <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center">
                      <span class="text-[18px] material-symbols-rounded text-mono-blanco"
                        >people</span
                      >
                    </div>
                    <span v-if="sidebarExpandido" class="text-[14px] text-mono-blanco"
                      >Clientes Fix</span
                    >
                  </div>
                  <div
                    v-if="sidebarExpandido"
                    :class="[
                      currentRoute === dashboardRoute
                        ? 'w-4 h-2.5 mx-3 bg-primary rounded-full'
                        : null,
                    ]"
                  ></div>
                </span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div
        :class="[sidebarExpandido ? 'w-[88%]' : 'w-[100%]']"
        class="contenido p-5 dark:bg-mono-negro_opacity_medio bg-mono-blanco_opacity backdrop-blur-md text-mono-blanco rounded-2xl"
      >
        <Header :auth="authStore" class="mb-5" />
        <slot></slot>
      </div>
    </div>
  </div>
</template>
