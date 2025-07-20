<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Colors from "@/Composables/ModularColores";
import { useSidebar } from "@/Composables/useSidebar";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";

import Header from "@/Components/header/Header.vue";

const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
});

const { sidebarExpandido, toggleSidebar } = useSidebar();
const { NombreApp, bgClase, bgOpacity, textoClase, focus, buttonLink, hover } = Colors();

const page = usePage();

const aplicacion = authStore.aplicacion || "Sin app";
const rol = authStore.rol || "Sin rol";

const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);
const dashboardRoute = computed(
  () =>
    new URL(route("aplicacion.dashboard", { aplicacion, rol }), window.location.origin)
      .pathname
);

const inicialesNombreTienda = computed(() => {
  const nombreTienda = authStore.nombreTienda || "";

  const inicialTienda = nombreTienda.split(" ")[0]?.charAt(0).toUpperCase() || "";
  return inicialTienda;
});
</script>

<template>
  <div class="bg-[url('/resources/images/02.webp')] bg-cover w-full min-h-[100vh]">
    <div
      class="p-4 flex bg-mono-negro_opacity_medio backdrop-blur-md w-full min-h-[100vh] rounded-xl"
    >
      <div
        class="sidebar w-[15%] pr-3 py-2"
        :class="[sidebarExpandido ? 'w-[15%]' : 'w-[45px]']"
      >
        <div class="headerSidebar flex justify-between items-start">
          <div
            v-if="sidebarExpandido"
            class="logoStore rounded-md w-7 h-7 grid place-items-center"
            :class="[bgClase]"
          >
            {{ inicialesNombreTienda }}
          </div>
          <button @click="sidebarExpandido = !sidebarExpandido">
            <span class="material-symbols-rounded text-mono-blanco">
              {{ sidebarExpandido ? "arrow_menu_close" : "arrow_menu_open" }}
            </span>
          </button>
        </div>
        <div class="navegacion mt-10">
          <ul class="flex flex-col gap-1">
            <li
              :class="[currentRoute === dashboardRoute ? [bgOpacity] : 'bg-transparent']"
              class="py-2 rounded-full cursor-pointer flex justify-center items-center"
            >
              <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                <span class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="flex items-center">
                      <span
                        class="text-[18px] material-symbols-rounded text-secundary-light"
                        >space_dashboard</span
                      >
                    </div>
                    <span v-if="sidebarExpandido" class="text-[14px] text-mono-blanco"
                      >Dashboard</span
                    >
                  </div>
                  <div
                    v-if="sidebarExpandido"
                    :class="[currentRoute === dashboardRoute ? focus : hover]"
                  ></div>
                </span>
              </a>
            </li>

            <div
              v-if="!sidebarExpandido"
              class="text-center text-secundary-light text-[12px]"
            >
              -
            </div>
            <p v-if="sidebarExpandido" class="text-secundary-light text-[12px] mt-2.5">
              Gestión
            </p>
          </ul>
        </div>
      </div>
      <div
        :class="[sidebarExpandido ? 'w-[85%]' : 'w-[100%]']"
        class="contenido p-6 dark:bg-mono-negro_opacity_full bg-mono-blanco_opacity text-mono-blanco rounded-2xl"
      >
        <Header :auth="authStore" class="mb-3" />
        <slot></slot>
      </div>
    </div>
  </div>
</template>
