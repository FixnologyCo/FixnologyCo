<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";
import "dayjs/locale/es";
import Header from "@/Components/header/Header.vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { useSidebar } from "@/Composables/useSidebar";
import ModalCrearApp from "@/Components/Modales/FixnologyCO/MisApps/ModalCrearApp.vue";
import ModalAppDetalle from "@/Components/Modales/FixnologyCO/MisApps/ModalDetallesApp.vue";
import ModalAppEditar from "@/Components/Modales/FixnologyCO/MisApps/ModalEditarApp.vue";

const { sidebarExpandido } = useSidebar();
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

const props = defineProps({
  auth: {
    type: Object,
    required: true,
  },
  app: Object,
  apps: {
    type: Array,
    default: () => [],
  },
  foto_base64: {
    type: String,
    default: "",
  },
  estados: {
    type: Array,
    required: true,
  },
  plan_aplicaciones: {
    type: Array,
    required: true,
  },
  membresias: {
    type: Array,
    required: true,
  },
  color_fondo: String,
  icono_app: String,
});

const auth = usePage().props.auth;
const mostrarModal = ref(false);

const opcionVisible = ref(null);
const appSeleccionada = ref(null);

const modalVisible = ref(false);
const abrirModalDetalles = (app) => {
  appSeleccionada.value = app;
  modalVisible.value = true;
  opcionVisible.value = false;
};

const modalVisibleEditar = ref(false);
const abrirModalEditar = (app) => {
  appSeleccionada.value = app;
  modalVisibleEditar.value = true;
  opcionVisible.value = false;
};

function alternarEstado(app) {
  const estadoActual = app.estado.tipo_estado.toLowerCase();
  const nuevoEstado = estadoActual === "activo" ? "inactivo" : "activo";

  const mensaje = `¿Estás seguro de que deseas ${
    nuevoEstado === "activo" ? "activar" : "desactivar"
  } esta aplicación?`;

  if (confirm(mensaje)) {
    router.put(
      route("aplicaciones.estado", app.id),
      {
        estado: nuevoEstado,
      },
      {
        preserveScroll: true,
      }
    );
  }
}
</script>

<script>
export default {
  data() {
    return {
      mostrarAppsActivas: true, // Por defecto mostrar activas
      mostrarAppsInactivas: false, // Por defecto ocultar inactivas
      opcionVisible: null,
      sidebarExpandido: true, // Ajusta según tu lógica
    };
  },
  computed: {
    appsActivas() {
      return this.apps.filter((app) => app.estado.tipo_estado === "Activo");
    },
    appsInactivas() {
      return this.apps.filter((app) => app.estado.tipo_estado === "Inactivo");
    },
  },
  methods: {
    toggleSeccionActivas() {
      this.mostrarAppsActivas = !this.mostrarAppsActivas;
    },
    toggleSeccionInactivas() {
      this.mostrarAppsInactivas = !this.mostrarAppsInactivas;
    },
    abrirModalDetalles(app) {
      // Tu lógica para abrir modal de detalles
      console.log("Mostrar detalles de:", app.nombre_app);
      this.opcionVisible = null;
    },
    abrirModalEditar(app) {
      // Tu lógica para abrir modal de edición
      console.log("Editar app:", app.nombre_app);
      this.opcionVisible = null;
    },
    alternarEstado(app) {
      // Tu lógica para cambiar el estado de la app
      console.log("Cambiar estado de:", app.nombre_app);
      this.opcionVisible = null;
    },
  },
};
</script>

<template>
  <Head title="Mis aplicaciones" />
  <MensajesLayout />

  <div class="flex">
    <SidebarSuperAdmin :auth="auth" />

    <div class="w-full">
      <Header :auth="auth" :foto_base64="foto_base64" />

      <div class="contenido p-3">
        <div class="indiceApps w-full flex justify-between items-center">
          <div class="indiceLeft w-[50%]">
            <h1 class="text-[30px] font-semibold text-mono-negro dark:text-mono-blanco">
              Lista de aplicaciones
            </h1>
            <p class="-mt-[10px] text-secundary-default dark:text-secundary-light">
              En este apartado estarán las aplicaciones de FixnologyCO
            </p>
          </div>
          <div class="indiceRight">
            <button :class="[buttonClase]" @click="mostrarModal = true">
              Crear nueva app <span class="material-symbols-rounded">add_circle</span>
            </button>
          </div>
        </div>

        <div>
          <!-- Sección de Listas Activas -->
          <div class="encabezadoOpciones mt-10 flex items-center gap-2 w-full">
            <span
              class="text-secundary-default dark:text-secundary-light text-[17px] w-[120px]"
              >Listas activas
            </span>
            <div class="line h-[2px] rounded-full w-full bg-secundary-light"></div>
            <div
              class="button flex justify-center items-center border-2 border-secundary-light rounded-full text-mono-negro dark:text-secundary-light cursor-pointer hover:bg-secundary-light hover:bg-opacity-10 transition-all duration-200"
              @click="toggleSeccionActivas"
            >
              <span
                class="material-symbols-rounded text-[20px] transition-transform duration-200"
                :class="{ 'rotate-180': mostrarAppsActivas }"
              >
                keyboard_arrow_down
              </span>
            </div>
          </div>

          <!-- Contenedor desplegable para apps activas -->
          <div
            class="overflow-hidden transition-all duration-300 ease-in-out"
            :class="
              mostrarAppsActivas
                ? 'min-h-[200px] max-h-[2200px] opacity-100'
                : 'max-h-0 opacity-0'
            "
          >
            <div class="listado flex flex-wrap gap-2">
              <div
                v-for="app in appsActivas"
                :key="app.id"
                class="tarjetaApp border border-secundary-light rounded-md p-4 mt-5"
                :class="[sidebarExpandido ? 'w-[32.5%]' : 'w-[24.5%]', 'border']"
              >
                <div class="top flex justify-between">
                  <div class="conjunto flex gap-3">
                    <div
                      class="icono grid place-content-center h-[50px] w-[50px] rounded-lg"
                      :class="app.color_fondo"
                    >
                      <i class="material-symbols-rounded text-mono-blanco">{{
                        app.icono_app || "help"
                      }}</i>
                    </div>
                    <div class="titulo-contador">
                      <h3
                        class="text-secundary-default dark:text-secundary-light font-semibold"
                      >
                        {{ app.nombre_app }}
                      </h3>
                      <p
                        class="text-secundary-default dark:text-mono-blanco -mt-2 text-[28px] font-bold"
                      >
                        {{ app.usuarios_en_tiendas }}
                      </p>
                    </div>
                  </div>

                  <div class="opciones relative">
                    <span
                      class="cursor-pointer material-symbols-rounded text-mono-negro dark:text-secundary-light"
                      @click="opcionVisible = opcionVisible === app.id ? null : app.id"
                    >
                      more_horiz
                    </span>

                    <div
                      v-if="opcionVisible === app.id"
                      class="cajaSelector absolute top-[25px] right-0 bg-white dark:bg-mono-negro border border-gray-300 dark:border-secundary-light rounded-lg p-2 z-[99999px] h-[auto] w-[180px] shadow-lg"
                    >
                      <button
                        class="optionApp text-[14px] w-full p-2 rounded-md flex items-center gap-2 text-mono-negro dark:text-mono-blanco"
                        :class="[hoverClase]"
                        @click="abrirModalDetalles(app)"
                      >
                        <span class="material-symbols-rounded text-[16px]">info</span>
                        <p>Mostrar detalles</p>
                      </button>

                      <button
                        class="optionApp text-[14px] w-full p-2 rounded-md flex items-center gap-2 text-mono-negro dark:text-mono-blanco"
                        :class="[hoverClase]"
                        @click="abrirModalEditar(app)"
                      >
                        <span class="material-symbols-rounded text-[16px]">draw</span>
                        <p>Editar app</p>
                      </button>

                      <button
                        @click="alternarEstado(app)"
                        class="optionApp text-[14px] w-full p-2 rounded-md flex items-center gap-2 text-mono-negro dark:text-mono-blanco"
                        :class="[hoverClase]"
                      >
                        <span class="material-symbols-rounded text-[16px]"
                          >nights_stay</span
                        >
                        <p>Inactivar app</p>
                      </button>
                    </div>
                  </div>
                </div>

                <div
                  class="line h-[1.5px] rounded-full w-full bg-secundary-light my-3"
                ></div>

                <div class="descripciones">
                  <p class="text-[14px] text-mono-negro dark:text-mono-blanco">
                    {{ app.descripcion ?? "Sin descripción" }}
                  </p>
                </div>

                <div class="tags flex items-center justify-between mt-1">
                  <div
                    class="plan rounded-[5px] bg-slate-500 px-1 text-mono-blanco text-[14px]"
                  >
                    ID: {{ app.id }}
                  </div>
                  <div
                    class="plan rounded-[5px] bg-universal-azul px-1 text-mono-blanco text-[14px]"
                  >
                    {{ app.membresia.nombre_membresia }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sección de Listas Inactivas -->
          <div class="encabezadoOpciones mt-10 flex items-center gap-2 w-full">
            <span
              class="text-secundary-default dark:text-secundary-light text-[17px] w-[140px]"
              >Listas inactivas</span
            >
            <div class="line h-[2px] rounded-full w-full bg-secundary-light"></div>
            <div
              class="button flex justify-center items-center border-2 border-secundary-light rounded-full text-mono-negro dark:text-secundary-light cursor-pointer hover:bg-secundary-light hover:bg-opacity-10 transition-all duration-200"
              @click="toggleSeccionInactivas"
            >
              <span
                class="material-symbols-rounded text-[20px] transition-transform duration-200"
                :class="{ 'rotate-180': mostrarAppsInactivas }"
              >
                keyboard_arrow_down
              </span>
            </div>
          </div>

          <!-- Contenedor desplegable para apps inactivas -->
          <div
            class="overflow-hidden transition-all duration-300 ease-in-out"
            :class="
              mostrarAppsInactivas
                ? 'min-h-[200px] max-h-[2200px] opacity-100'
                : 'max-h-0 opacity-0'
            "
          >
            <div class="listado flex flex-wrap gap-2">
              <div
                v-for="app in appsInactivas"
                :key="app.id"
                class="tarjetaApp border rounded-md p-4 mt-5 bg-gray-600"
                :class="[sidebarExpandido ? 'w-[32.5%]' : 'w-[24.5%]', 'border-gray-500']"
              >
                <div class="top flex justify-between">
                  <div class="conjunto flex gap-3">
                    <div
                      class="icono grid place-content-center h-[50px] w-[50px] rounded-lg bg-slate-500"
                    >
                      <i class="material-symbols-rounded text-mono-blanco">nights_stay</i>
                    </div>
                    <div class="titulo-contador">
                      <h3 class="text-gray-300 font-semibold">
                        {{ app.nombre_app }}
                      </h3>
                      <p class="text-gray-400 -mt-2 text-[28px] font-bold">
                        {{ app.usuarios_en_tiendas }}
                      </p>
                    </div>
                  </div>

                  <div class="opciones relative">
                    <span
                      class="cursor-pointer material-symbols-rounded text-gray-400"
                      @click="opcionVisible = opcionVisible === app.id ? null : app.id"
                    >
                      more_horiz
                    </span>

                    <div
                      v-if="opcionVisible === app.id"
                      class="cajaSelector absolute top-[20px] right-0 bg-white dark:bg-mono-negro border border-gray-300 dark:border-secundary-light rounded-lg p-2 z-50 w-[170px] shadow-lg"
                    >
                      <button
                        class="optionApp text-[14px] w-full p-2 rounded-md flex items-center gap-2 text-mono-negro dark:text-mono-blanco"
                        :class="[hoverClase]"
                        @click="abrirModalDetalles(app)"
                      >
                        <span class="material-symbols-rounded text-[16px]">info</span>
                        <p>Mostrar detalles</p>
                      </button>

                      <button
                        class="optionApp text-[14px] w-full p-2 rounded-md flex items-center gap-2 text-mono-negro dark:text-mono-blanco"
                        :class="[hoverClase]"
                        @click="abrirModalEditar(app)"
                      >
                        <span class="material-symbols-rounded text-[16px]">draw</span>
                        <p>Editar app</p>
                      </button>

                      <button
                        @click="alternarEstado(app)"
                        class="optionApp text-[14px] w-full p-2 rounded-md flex items-center gap-2 text-semaforo-rojo"
                        :class="[hoverClase]"
                      >
                        <span class="material-symbols-rounded text-[16px]">wb_sunny</span>
                        <p>Activar app</p>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="line h-[1.5px] rounded-full w-full bg-gray-500 my-3"></div>

                <div class="descripciones">
                  <p class="text-[14px] text-gray-400">
                    {{ app.descripcion ?? "Sin descripción" }}
                  </p>
                </div>

                <div class="tags flex items-center justify-between mt-1">
                  <div
                    class="plan rounded-[5px] bg-slate-500 px-1 text-mono-blanco text-[14px]"
                  >
                    ID: {{ app.id }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ModalCrearApp
      :mostrar="mostrarModal"
      @cerrar="mostrarModal = false"
      :estados="estados"
      :plan_aplicaciones="plan_aplicaciones"
      :membresias="membresias"
      :auth="auth"
    />

    <ModalAppDetalle
      :visible="modalVisible"
      :app="appSeleccionada"
      @cerrar="modalVisible = false"
    />
    <ModalAppEditar
      v-if="modalVisibleEditar"
      :visibleEditar="modalVisibleEditar"
      :app="appSeleccionada"
      :estados="estados"
      :plan_aplicaciones="plan_aplicaciones"
      :membresias="membresias"
      :auth="auth"
      @cerrar="modalVisibleEditar = false"
    />
  </div>
</template>
