<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

// Composables importados
import { useTema } from "@/Composables/useTema";
import { useDateTime } from "@/Composables/useDateTime";

// Componentes
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ConfirmacionesPop from "../Modales/Confirmaciones/ConfirmacionesPop.vue";

// Store
import { useAuthStore } from "@/stores/auth";

// --- SETUP INICIAL ---
const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
});

// --- COMPOSABLES ---
const { modoOscuro, animando, animarCambioTema } = useTema();
const { nombreDia, dia, mes, anio, hora, saludo } = useDateTime();

import { useConfirmationModal } from "@/Composables/useConfirmationModal";
const {
  confirmationState,
  openConfirmationModal,
  closeConfirmationModal,
  handleConfirm,
} = useConfirmationModal();

// --- ESTADO LOCAL DEL COMPONENTE ---
const isMenuOpen = ref(false);
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

// --- PROPIEDADES COMPUTADAS (AHORA REACTIVAS) ---
const aplicacion = computed(() => authStore.aplicacion || "Sin app");
const rol = computed(() => authStore.rol || "Sin rol");

const initials = computed(() => {
  const nombres = authStore.primerNombre || "";
  const apellidos = authStore.primerApellido || "";
  const firstNameInitial = nombres.split(" ")[0]?.charAt(0).toUpperCase() || "";
  const lastNameInitial = apellidos.split(" ")[0]?.charAt(0).toUpperCase() || "";
  return firstNameInitial + lastNameInitial;
});

const logout = () => {
  openConfirmationModal({
    title: "¿Estás seguro de cerrar sesión?",
    message: "Estás a punto de cerrar sesión y regresar a la sección de login.",
    icon: "info",
    iconBgClass: "bg-yellow-500/20 border-semaforo-amarillo",
    confirmText: "Sí, cerrar",
    onConfirm: () => {
      authStore.detenerContadorSesion();
      router.post(route("logout"));
    },
  });
};
</script>

<template>
  <div>
    <header class="header flex items-center justify-between gap-3">
      <div class="left w-[20%] rounded-md">
        <div class="relative min-w-[280px] max-w-[400px]">
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

              <template v-if="!authStore.fotoUrlCompletaUsuario">
                <div
                  class="user relative h-[35px] w-[35px] rounded-full overflow-hidden flex items-center justify-center text-mono-blanco bg-primary shadow-[0px_8px_20px] shadow-primary"
                >
                  <span class="text-[12px] font-bold">{{ initials }}</span>
                </div>
              </template>

              <template v-else>
                <img
                  :src="authStore.fotoUrlCompletaUsuario"
                  class="border-2 rounded-[50px] w-[40px] h-[40px] object-cover shadowM"
                />
              </template>
            </div>

            <div class="ml-2">
              <p class="font-semibold text-mono-negro dark:text-mono-blanco">
                {{ saludo }}, {{ authStore.primerNombre }}.
              </p>
              <p
                class="text-[12px] font-medium text-secundary-default dark:text-secundary-light"
              >
                {{ nombreDia }} {{ dia }} de {{ mes }} {{ anio }}, {{ hora }}
              </p>
            </div>

            <div v-if="isMenuOpen === true" class="" @click="toggleMenu">
              <span
                class="material-symbols-rounded text-[16px] hover:text-primary dark:text-mono-blanco text-mono-negro cursor-pointer"
                >keyboard_arrow_up</span
              >
            </div>
            <div class="" v-else @click="toggleMenu">
              <span
                class="material-symbols-rounded text-[16px] hover:text-primary dark:text-mono-blanco text-mono-negro cursor-pointer"
                >keyboard_arrow_down</span
              >
            </div>
          </div>

          <div
            v-if="isMenuOpen"
            class="absolute right-0 mt-2 w-52 bg-mono-blanco_opacity dark:bg-mono-negro_opacity_full backdrop-blur-[100px] rounded-xl shadow-lg p-3 z-20"
          >
            <div class="nombreUsuario px-2 py-2">
              <div class="flex items-center justify-between">
                <span class="material-symbols-rounded text-[18px] text-primary"
                  >crown</span
                >
                <h3 class="text-mono-negro font-semibold dark:text-mono-blanco">
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
              :href="route('aplicacion.miPerfil', { aplicacion, rol })"
              class="flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco hover:bg-primary hover:shadow-[0px_4px_20px] hover:shadow-primary"
              ><span class="material-symbols-rounded text-[18px]">for_you</span> Mi
              Perfil</a
            >

            <a
              href="#"
              class="flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco hover:bg-primary hover:shadow-[0px_4px_20px] hover:shadow-primary"
              ><span class="material-symbols-rounded text-[18px]">settings</span>
              Configuraciones</a
            >
            <a
              href="#"
              class="flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco hover:bg-primary hover:shadow-[0px_4px_20px] hover:shadow-primary"
              ><span class="material-symbols-rounded text-[18px]">Help</span> Ayuda</a
            >

            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
            <a
              href="#"
              class="flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco hover:bg-primary hover:shadow-[0px_4px_20px] hover:shadow-primary"
              ><span class="material-symbols-rounded text-[18px]"
                >deployed_code_update</span
              >
              Actualizaciones
            </a>
            <a
              @click="logout"
              class="cursor-pointer flex items-center gap-2 px-2 rounded-full py-2 text-sm text-mono-negro dark:text-mono-blanco hover:bg-primary hover:shadow-[0px_4px_20px] hover:shadow-primary"
              ><span class="material-symbols-rounded text-[18px]">logout</span> Cerrar
              sesión</a
            >
          </div>
        </div>
      </div>

      <div class="flex gap-2 items-center">
        <p
          class="text-[12px] font-medium text-mono-negro dark:text-mono-blanco flex items-center"
        >
          <span class="material-symbols-rounded text-[13px] mr-1">timer</span>
          {{ authStore.tiempoEnLinea }}
        </p>
        <a :href="route('aplicacion.miPerfil', { aplicacion, rol })">
          <div
            class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer hover:bg-primary hover:shadow-[0px_4px_20px] hover:shadow-primary"
          >
            <span class="material-symbols-rounded text-[20px] text-mono-blanco">
              notifications
            </span>
          </div>
        </a>
        <a :href="route('aplicacion.miPerfil', { aplicacion, rol })">
          <div
            class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer hover:bg-primary hover:shadow-[0px_4px_20px] hover:shadow-primary"
          >
            <span class="material-symbols-rounded text-[20px] text-mono-blanco">
              history
            </span>
          </div>
        </a>
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

          <span
            v-if="animando"
            class="absolute inset-0 bg-mono-blanco/10 backdrop-blur-sm animate-ping z-0 rounded-md"
          ></span>
        </button>
      </div>
    </header>
    <ConfirmacionesPop
      :is-open="confirmationState.isOpen"
      :title="confirmationState.title"
      :message="confirmationState.message"
      :icon="confirmationState.icon"
      :icon-bg-class="confirmationState.iconBgClass"
      :confirm-text="confirmationState.confirmText"
      @close="closeConfirmationModal"
      @confirm="handleConfirm"
    />
  </div>
</template>
