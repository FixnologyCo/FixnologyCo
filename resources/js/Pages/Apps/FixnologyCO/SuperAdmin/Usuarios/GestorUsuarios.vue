<script setup>
import { Head, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import ModalDetallesUsuario from "@/Components/Modales/FixnologyCO/Usuarios/ModalDetallesUsuario.vue";
import ModalCrearUsuario from "@/Components/Modales/FixnologyCO/Usuarios/ModalCrearUsuario.vue";
import ModalPapeleraUsuario from "@/Components/Modales/FixnologyCO/Usuarios/ModalPapeleraUsuario.vue";
import vistasDatos from "@/Components/Shared/buttons/vistasDatos.vue";
import barraBusqueda from "@/Components/Shared/barraBusqueda/barraBusqueda.vue";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import useEstadoClass from "@/Composables/useEstado";
import {
  getInicialesEstablecimiento,
  getInicialesUsuario,
} from "@/Utils/inicialesNombres";
import {
  getFotoEstablecimientoUrlCompleta,
  getFotoUrlCompleta,
} from "@/Utils/ImagenUsuarios";
import { formatCOP } from "@/Utils/formateoMoneda";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useInfoUser } from "@/stores/infoUsers";
import "dayjs/locale/es";
import BtnPrimario from "@/Components/Shared/buttons/btnPrimario.vue";
import BtnSecundario from "@/Components/Shared/buttons/btnSecundario.vue";

const props = defineProps({
  todosLosUsuarios: { type: Array, required: true },
  misEmpleados: { type: Array, default: () => [] },
  usuariosEnPapelera: { type: Array },
  establecimientosDisponibles: { type: Array },
  filtrosDisponibles: { type: Object },
  usuariosEnPapelera: { type: Array },
});

const authStore = useAuthStore();
const infoUserStore = useInfoUser();
defineOptions({
  layout: AuthenticatedLayout,
});

const { getEstadoClass } = useEstadoClass();

const activeTab = ref(0);
const tabs = [{ label: "Clientes Fix" }, { label: "Colaboradores" }];

const isModalOpen = ref(false);
function openUserDetailsModal(usuario) {
  infoUserStore.setUser(usuario);
  isModalOpen.value = true;
}
function closeUserDetailsModal() {
  isModalOpen.value = false;
}

const isModalOpenCrearUser = ref(false);
function openUserCreationModal() {
  isModalOpenCrearUser.value = true;
}
function closeUserCreationModal() {
  isModalOpenCrearUser.value = false;
}

const isModalOpenPapelera = ref(false);
function openPapeleraModal() {
  isModalOpenPapelera.value = true;
}
function closePapeleraModal() {
  isModalOpenPapelera.value = false;
}

const filters = ref({
  estado: "all",
  orden: "reciente",
  aplicacion: "all",
  membresia: "all",
  ciudad: "all",
});

const isAnyFilterActive = computed(() => {
  const { estado, orden, aplicacion, membresia, ciudad } = filters.value;
  return (
    estado !== "all" ||
    orden !== "reciente" ||
    aplicacion !== "all" ||
    membresia !== "all" ||
    ciudad !== "all"
  );
});
function resetFilters() {
  filters.value = {
    estado: "all",
    orden: "reciente",
    aplicacion: "all",
    membresia: "all",
    ciudad: "all",
  };
}
const searchTerm = ref("");
const filteredUsers = computed(() => {
  let users = props.todosLosUsuarios;

  if (searchTerm.value.trim()) {
    const lowerCaseSearch = searchTerm.value.toLowerCase();
    users = users.filter((usuario) => {
      const searchableContent = [
        usuario.perfil_usuario?.primer_nombre,
        usuario.perfil_usuario?.primer_apellido,
        usuario.perfil_usuario?.correo,
        usuario.perfil_usuario?.telefono,
        usuario.perfil_usuario?.rol?.tipo_rol,
        usuario.perfil_empleado?.cargo,
        usuario.establecimiento_asignado?.nombre_establecimiento,
        usuario.numero_documento,
      ]
        .filter(Boolean)
        .join(" ")
        .toLowerCase();
      return searchableContent.includes(lowerCaseSearch);
    });
  }

  if (filters.value.estado !== "all") {
    users = users.filter(
      (u) => u.perfil_usuario?.estado?.tipo_estado === filters.value.estado
    );
  }
  if (filters.value.aplicacion !== "all") {
    users = users.filter(
      (u) =>
        u.establecimiento_asignado?.aplicacion_web?.nombre_app ===
        filters.value.aplicacion
    );
  }
  if (filters.value.membresia !== "all") {
    users = users.filter(
      (u) =>
        u.establecimiento_asignado?.aplicacion_web?.membresia?.nombre_membresia ===
        filters.value.membresia
    );
  }
  if (filters.value.ciudad !== "all") {
    users = users.filter(
      (u) => u.perfil_usuario?.ciudad_residencia === filters.value.ciudad
    );
  }

  users.sort((a, b) => {
    switch (filters.value.orden) {
      case "a-z":
        return (a.perfil_usuario?.primer_nombre || "").localeCompare(
          b.perfil_usuario?.primer_nombre || ""
        );
      case "z-a":
        return (b.perfil_usuario?.primer_nombre || "").localeCompare(
          a.perfil_usuario?.primer_nombre || ""
        );
      case "antiguo":
        return (
          new Date(a.perfil_usuario?.created_at) - new Date(b.perfil_usuario?.created_at)
        );
      case "reciente":
      default:
        return (
          new Date(b.perfil_usuario?.created_at) - new Date(a.perfil_usuario?.created_at)
        );
    }
  });
  return users;
});

const viewMode = ref(localStorage.getItem("userViewMode") || "list");

watch(viewMode, (newValue) => {
  localStorage.setItem("userViewMode", newValue);
});

function highlightMatch(text) {
  if (text === null || !searchTerm.value.trim()) {
    return text;
  }
  const textString = String(text);
  const regex = new RegExp(searchTerm.value, "gi");
  return textString.replace(regex, (match) => {
    return `<mark class="bg-primary text-white rounded">${match}</mark>`;
  });
}

onMounted(() => {
  window.Echo.private("UsuariosActualizados").listen(
    ".ListaUsuariosActualizados",
    (e) => {
      router.reload({
        only: [
          "todosLosUsuarios",
          "misEmpleados",
          "usuariosEnPapelera",
          "establecimientosDisponibles",
        ],
        preserveState: true,
        preserveScroll: true,
      });
    }
  );
});

onUnmounted(() => {
  window.Echo.leave("UsuariosActualizados");
});

const isSelectionModeActive = ref(false);
const selectedUserIds = ref([]);
const hasSelectedUsers = computed(() => selectedUserIds.value.length > 0);

function toggleSelectionMode() {
  isSelectionModeActive.value = !isSelectionModeActive.value;
  if (!isSelectionModeActive.value) {
    selectedUserIds.value = [];
  }
}

function bulkDeleteSelectedUsers() {
  if (!hasSelectedUsers.value) return;

  if (
    confirm(
      `¿Estás seguro de que quieres enviar ${selectedUserIds.value.length} usuario(s) a la papelera?`
    )
  ) {
    router.post(
      route("usuarios.bulkDestroy"),
      {
        ids: selectedUserIds.value,
      },
      {
        onSuccess: () => {
          selectedUserIds.value = [];
          isSelectionModeActive.value = false;
        },
        preserveScroll: true,
      }
    );
  }
}
</script>

<template>
  <div>
    <Head title="Gestor de usuarios" />

    <div class="flex">
      <SidebarSuperAdmin :authStore>
        <MensajesLayout />

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

            <div v-if="activeTab === 0" class="flex gap-5 w-full max-h-[80dvh]">
              <div class="filtro w-[20%]">
                <div class="header flex items-center justify-between">
                  <h1 class="text-[30px] font-bold">Filtros</h1>
                  <div
                    class="contador border border-secundary-light rounded-md px-2 py-1 text-[12px]"
                  >
                    {{ filteredUsers.length }} Resultados
                  </div>
                </div>
                <div
                  class="contenidoFiltro bg-secundary-opacity rounded-lg border border-secundary-light p-3 mt-3 space-y-4"
                >
                  <!-- Filtro por Estado -->
                  <div>
                    <label for="filtro-estado" class="text-[14px] text-secundary-light"
                      >Estado cliente</label
                    >
                    <select
                      v-model="filters.estado"
                      id="filtro-estado"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todos los Estados</option>
                      <option
                        v-for="estado in filtrosDisponibles.estados"
                        :key="estado"
                        :value="estado"
                      >
                        {{ estado }}
                      </option>
                    </select>
                  </div>
                  <!-- Filtro por Orden -->
                  <div>
                    <label for="filtro-orden" class="text-[14px] text-secundary-light"
                      >Ordenar por</label
                    >
                    <select
                      v-model="filters.orden"
                      id="filtro-orden"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="reciente">Más Recientes</option>
                      <option value="antiguo">Más Antiguos</option>
                      <option value="a-z">Nombre (A-Z)</option>
                      <option value="z-a">Nombre (Z-A)</option>
                    </select>
                  </div>
                  <!-- Filtro por Aplicación -->
                  <div>
                    <label for="filtro-app" class="text-[14px] text-secundary-light"
                      >Aplicación</label
                    >
                    <select
                      v-model="filters.aplicacion"
                      id="filtro-app"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todas las Apps</option>
                      <option
                        v-for="app in filtrosDisponibles.aplicaciones"
                        :key="app"
                        :value="app"
                      >
                        {{ app }}
                      </option>
                    </select>
                  </div>
                  <!-- Filtro por Membresía -->
                  <div>
                    <label for="filtro-membresia" class="text-[14px] text-secundary-light"
                      >Membresía</label
                    >
                    <select
                      v-model="filters.membresia"
                      id="filtro-membresia"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todas las Membresías</option>
                      <option
                        v-for="mem in filtrosDisponibles.membresias"
                        :key="mem"
                        :value="mem"
                      >
                        {{ mem }}
                      </option>
                    </select>
                  </div>
                  <!-- Filtro por Ciudad -->
                  <div>
                    <label for="filtro-ciudad" class="text-[14px] text-secundary-light"
                      >Ciudad</label
                    >
                    <select
                      v-model="filters.ciudad"
                      id="filtro-ciudad"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todas las Ciudades</option>
                      <option
                        v-for="ciudad in filtrosDisponibles.ciudades"
                        :key="ciudad"
                        :value="ciudad"
                      >
                        {{ ciudad }}
                      </option>
                    </select>
                  </div>
                  <div v-if="isAnyFilterActive">
                    <button
                      @click="resetFilters"
                      class="w-full mt-2 py-2 px-4 text-sm font-medium text-gray-300 bg-gray-700/50 rounded-md hover:bg-gray-700 transition-colors"
                    >
                      Restablecer Filtros
                    </button>
                  </div>
                </div>
              </div>
              <div class="contenido w-[80%]">
                <div class="header flex items-center gap-2">
                  <barraBusqueda v-model:searchTerm="searchTerm" />
                  <vistasDatos v-model:viewMode="viewMode" />
                  <div class="text-[30px]">|</div>
                  <div class="" v-if="selectedUserIds.length === 0">
                    <BtnSecundario
                      @click="toggleSelectionMode"
                      class="material-symbols-rounded hover:-translate-y-1"
                      :class="
                        isSelectionModeActive
                          ? 'bg-secondary border-secondary hover:border-secondary hover:text-mono-blanco text-mono-blanco '
                          : 'border border-secundary-light hover:border-primary '
                      "
                    >
                      atr</BtnSecundario
                    >
                  </div>

                  <BtnSecundario
                    name="fade"
                    v-if="hasSelectedUsers"
                    @click="bulkDeleteSelectedUsers"
                    class="relative material-symbols-rounded bg-semaforo-rojo border-semaforo-rojo hover:text-mono-blanco hover:-translate-y-1"
                  >
                    delete
                    <p
                      v-if="hasSelectedUsers"
                      class="absolute bg-mono-blanco text-semaforo-rojo text-[12px] w-4 h-4 flex justify-center items-center font-semibold rounded-full -top-2 -right-1"
                    >
                      <span>{{ selectedUserIds.length }}</span>
                    </p>
                  </BtnSecundario>

                  <BtnSecundario
                    @click="openPapeleraModal()"
                    class="material-symbols-rounded"
                  >
                    auto_delete</BtnSecundario
                  >
                  <BtnPrimario @click="openUserCreationModal()" class="w-[35%]"
                    ><span>Crear cliente</span
                    ><span class="material-symbols-rounded text-[16px]"
                      >people</span
                    ></BtnPrimario
                  >
                </div>
                <div
                  class="my-3 min-h-[75dvh] max-h-[80dvh] overflow-auto scrollbar-custom"
                >
                  <!-- Mensaje para cuando no hay resultados -->
                  <div
                    v-if="filteredUsers.length === 0"
                    class="text-center text-gray-400 mt-10"
                  >
                    <p class="text-lg">
                      No se encontraron usuarios que coincidan con tu búsqueda.
                    </p>
                  </div>

                  <!-- ✅ VISTA DE TABLA -->
                  <table
                    v-else-if="viewMode === 'table'"
                    class="w-full text-sm text-left text-gray-300"
                  >
                    <thead class="text-xs text-gray-200 uppercase bg-gray-700/50">
                      <tr>
                        <th v-if="isSelectionModeActive" class="px-2 py-3 w-4">
                          Seleccionar
                        </th>
                        <th scope="col" class="px-6 py-3">Usuario</th>
                        <th scope="col" class="px-6 py-3">Contacto</th>
                        <th scope="col" class="px-6 py-3">Rol</th>
                        <th scope="col" class="px-6 py-3">Establecimiento</th>
                        <th scope="col" class="px-6 py-3">
                          <span class="sr-only">Acciones</span>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="usuario in filteredUsers"
                        :key="usuario.id"
                        @click="openUserDetailsModal(usuario)"
                        class="border-b border-gray-700 hover:bg-gray-800/50 cursor-pointer"
                      >
                        <td v-if="isSelectionModeActive" class="px-2">
                          <input
                            type="checkbox"
                            :value="usuario.id"
                            v-model="selectedUserIds"
                            @click.stop
                            class="form-checkbox rounded bg-gray-700 border-gray-60 t-primary focus:ring-primary"
                          />
                        </td>
                        <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
                          <div class="flex items-center gap-3">
                            <img
                              v-if="usuario.perfil_usuario.ruta_foto"
                              :src="usuario.perfil_usuario.ruta_foto"
                              alt="Foto"
                              class="w-10 h-10 rounded-full object-cover"
                            />
                            <div
                              v-else
                              class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-bold"
                            >
                              {{ getInicialesUsuario(usuario) }}
                            </div>
                            <span
                              v-html="
                                highlightMatch(
                                  `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.primer_apellido}`
                                )
                              "
                            ></span>
                          </div>
                        </td>
                        <td
                          class="px-6 py-4"
                          v-html="highlightMatch(usuario.perfil_usuario.correo)"
                        ></td>
                        <td
                          class="px-6 py-4"
                          v-html="highlightMatch(usuario.perfil_usuario.rol.tipo_rol)"
                        ></td>
                        <td
                          class="px-6 py-4"
                          v-html="
                            highlightMatch(
                              usuario.establecimiento_asignado?.nombre_establecimiento
                            )
                          "
                        ></td>
                        <td>{{ getInicialesEstablecimiento(usuario) }}</td>
                        <td class="px-6 py-4 text-right">
                          <button
                            class="material-symbols-rounded p-1 text-gray-400 hover:text-primary"
                          >
                            more_vert
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- ✅ VISTA DE LISTAS -->
                  <div v-else class="cardsUsuarios" :class="{ '': viewMode === 'list' }">
                    <div
                      class="flex gap-2 items-center contCard bg-mono-negro_opacity_medio backdrop-blur-xl p-2 rounded-[10px] my-2 transition-all duration-300"
                      v-for="usuario in filteredUsers"
                      :key="usuario.id"
                    >
                      <div
                        class="grid place-content-center foto w-[75px] h-[75px] rounded-[10px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                      >
                        <template v-if="getFotoUrlCompleta(usuario)">
                          <div class="relative group w-[75px] h-[75px]">
                            <div
                              class="h-3 w-3 rounded-full absolute -top-1 -left-1"
                              :class="
                                usuario.tiene_sesion_activa === true
                                  ? 'bg-semaforo-verde shadow-[0px_10px_20px] shadow-semaforo-verde'
                                  : 'bg-semaforo-rojo shadow-[0px_10px_20px] shadow-semaforo-rojo'
                              "
                            ></div>
                            <img
                              :src="getFotoUrlCompleta(usuario)"
                              class="rounded-[10px] border-2 w-full h-full object-cover shadow-lg"
                            />
                          </div>
                        </template>

                        <template v-else>
                          <div class="relative group w-[75px] h-[75px]">
                            <div
                              class="h-3 w-3 rounded-full absolute -top-1 -left-1"
                              :class="
                                usuario.tiene_sesion_activa === true
                                  ? 'bg-semaforo-verde shadow-[0px_10px_20px] shadow-semaforo-verde'
                                  : 'bg-semaforo-rojo shadow-[0px_10px_20px] shadow-semaforo-rojo'
                              "
                            ></div>
                            <p
                              class="h-full w-full flex justify-center items-center text-[20px] font-semibold border-2 rounded-[10px]"
                            >
                              {{ getInicialesUsuario(usuario) }}
                            </p>
                          </div>
                        </template>
                      </div>
                      <div class="detalles w-[40%]">
                        <div class="nombre flex gap-1 items-center">
                          <h2
                            class="text-[20px] font-medium"
                            v-html="
                              highlightMatch(
                                `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.segundo_nombre} ${usuario.perfil_usuario.primer_apellido} ${usuario.perfil_usuario.segundo_apellido}`
                              )
                            "
                          ></h2>
                          <div
                            class="grid place-items-center"
                            v-if="usuario.google_id === null"
                          >
                            <span
                              class="material-symbols-rounded text-[18px] text-gray-700"
                              >verified_off</span
                            >
                          </div>
                          <div
                            class="grid place-items-center text-secundary-default dark:text-mono-blanco"
                            v-else
                          >
                            <span
                              class="material-symbols-rounded text-[18px] text-secondary"
                              >verified</span
                            >
                          </div>
                        </div>
                        <div class="flex -mt-2 justify-between items-end">
                          <p
                            class="text-secundary-light text-[14px]"
                            v-html="highlightMatch(`${usuario.perfil_usuario.correo}`)"
                          ></p>
                          <p
                            class="text-secundary-light text-[14px]"
                            v-html="
                              highlightMatch(
                                `${usuario.perfil_usuario.indicativo.codigo_pais} ${usuario.perfil_usuario.telefono}`
                              )
                            "
                          ></p>
                        </div>
                        <div class="flex justify-between items-center">
                          <div class="flex items-center gap-1">
                            <div
                              class="w-3 h-2 rounded-full"
                              :class="
                                getEstadoClass(usuario.perfil_usuario.estado.tipo_estado)
                              "
                            ></div>
                            <span
                              class="text-secundary-light text-[14px]"
                              v-html="
                                highlightMatch(
                                  `${usuario.perfil_usuario.estado.tipo_estado}`
                                )
                              "
                            ></span>
                          </div>
                          <p
                            class="text-secundary-light text-[14px]"
                            v-html="
                              highlightMatch(
                                `${usuario.perfil_usuario.barrio_residencia}, ${usuario.perfil_usuario.ciudad_residencia}`
                              )
                            "
                          ></p>
                          <p
                            class="text-secundary-light text-[14px]"
                            v-html="
                              highlightMatch(
                                `${usuario.establecimiento_asignado.aplicacion_web.membresia.nombre_membresia}`
                              )
                            "
                          ></p>
                        </div>
                      </div>
                      <p class="text-[50px] font-thin">|</p>
                      <div
                        class="grid place-content-center foto w-[60px] h-[60px] rounded-[10px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                      >
                        <template v-if="getFotoEstablecimientoUrlCompleta(usuario)">
                          <div class="relative group w-[60px] h-[60px]">
                            <img
                              :src="getFotoEstablecimientoUrlCompleta(usuario)"
                              class="rounded-[10px] border shadowM w-full h-full object-cover shadow-lg"
                            />
                          </div>
                        </template>

                        <template v-else>
                          <div class="relative group w-[60px] h-[60px]">
                            <p
                              class="h-full w-full flex justify-center items-center text-[20px] font-semibold border shadowM rounded-[10px]"
                            >
                              {{ getInicialesEstablecimiento(usuario) }}
                            </p>
                          </div>
                        </template>
                      </div>
                      <div class="detalles w-[40%]">
                        <div class="nombre flex gap-1 items-center">
                          <h2
                            class="text-[20px] font-medium"
                            v-html="
                              highlightMatch(
                                `${usuario.establecimiento_asignado.nombre_establecimiento}`
                              )
                            "
                          ></h2>
                        </div>
                        <div class="flex -mt-2 justify-between items-end">
                          <p
                            class="text-secundary-light text-[14px]"
                            v-html="
                              highlightMatch(
                                `${usuario.establecimiento_asignado.token.token_activacion}`
                              )
                            "
                          ></p>
                          <p
                            class="text-secundary-light text-[14px]"
                            v-html="
                              highlightMatch(
                                `${usuario.establecimiento_asignado.tipo_establecimiento}`
                              )
                            "
                          ></p>
                        </div>
                        <div class="flex justify-between items-center">
                          <div class="flex items-center gap-1">
                            <div
                              class="w-3 h-2 rounded-full"
                              :class="
                                getEstadoClass(
                                  usuario.establecimiento_asignado.estado.tipo_estado
                                )
                              "
                            ></div>
                            <span
                              class="text-secundary-light text-[14px]"
                              v-html="
                                highlightMatch(
                                  `${usuario.establecimiento_asignado.estado.tipo_estado}`
                                )
                              "
                            ></span>
                          </div>
                          <p
                            class="text-secundary-light text-[14px]"
                            v-html="
                              highlightMatch(
                                `${usuario.establecimiento_asignado.aplicacion_web.nombre_app} - ${usuario.establecimiento_asignado.aplicacion_web.membresia.nombre_membresia}`
                              )
                            "
                          ></p>

                          <div
                            v-if="
                              usuario.establecimiento?.facturas &&
                              usuario.establecimiento?.facturas.length > 0
                            "
                          >
                            <p
                              class="p-1 rounded-md text-[14px] font-bold"
                              :class="
                                getEstadoClass(
                                  usuario.establecimiento_asignado?.facturas[0].estado
                                    .tipo_estado
                                )
                              "
                              v-html="
                                highlightMatch(
                                  formatCOP(
                                    `${usuario.establecimiento_asignado?.facturas[0].monto_total}`
                                  )
                                )
                              "
                            ></p>
                          </div>

                          <div v-else>
                            <div v-if="usuario" class="text-sm text-gray-400 mt-2">
                              {{ usuario?.perfil_empleado?.cargo }} -
                              <strong class="text-primary font-semibold">
                                {{
                                  usuario.establecimiento_asignado?.nombre_establecimiento
                                }}
                              </strong>
                            </div>
                            <div v-else class="text-sm text-gray-500">
                              Sin Información :c
                            </div>
                          </div>
                        </div>
                      </div>
                      <p class="text-[50px] font-thin">|</p>

                      <div
                        class="flex justify-center items-center"
                        v-if="isSelectionModeActive"
                      >
                        <label
                          class="relative flex justify-center items-center cursor-pointer group"
                        >
                          <input
                            class="peer sr-only"
                            type="checkbox"
                            :value="usuario.id"
                            v-model="selectedUserIds"
                            @click.stop
                          />
                          <div
                            class="w-5 h-5 rounded-md bg-white border-2 border-primary transition-all duration-300 ease-in-out peer-checked:bg-gradient-to-br from-primary to-secondary peer-checked:border-0 peer-checked:rotate-12 after:content-[''] after:absolute after:top-1/2 after:left-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-4 after:h-4 after:opacity-0 after:bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS13aWR0aD0iMyIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSIyMCA2IDkgMTcgNCAxMiI+PC9wb2x5bGluZT48L3N2Zz4=')] after:bg-contain after:bg-no-repeat peer-checked:after:opacity-100 after:transition-opacity after:duration-300 hover:shadow-[0_0_15px_rgba(168,85,247,0.5)]"
                          ></div>
                        </label>
                      </div>
                      <div class="" v-else>
                        <BtnSecundario
                          @click="openUserDetailsModal(usuario)"
                          class="material-symbols-rounded border-none hover:text-primary hover:-translate-y-1"
                        >
                          info</BtnSecundario
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeTab === 1" class="flex gap-5 w-full max-h-[80dvh]">
              <div class="filtro w-[20%]">
                <div class="header flex items-center justify-between">
                  <h1 class="text-[30px] font-bold">Colaboradores</h1>
                  <div
                    class="contador border border-secundary-light rounded-md px-2 py-1 text-[12px]"
                  >
                    {{ filteredUsers.length }} Total
                  </div>
                </div>
                <div
                  class="contenidoFiltro bg-secundary-opacity rounded-lg border border-secundary-light p-3 mt-3 space-y-4"
                >
                  <!-- Filtro por Estado -->
                  <div>
                    <label for="filtro-estado" class="text-[14px] text-secundary-light"
                      >Estado cliente</label
                    >
                    <select
                      v-model="filters.estado"
                      id="filtro-estado"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todos los Estados</option>
                      <option
                        v-for="estado in filtrosDisponibles.estados"
                        :key="estado"
                        :value="estado"
                      >
                        {{ estado }}
                      </option>
                    </select>
                  </div>
                  <!-- Filtro por Orden -->
                  <div>
                    <label for="filtro-orden" class="text-[14px] text-secundary-light"
                      >Ordenar por Nombre</label
                    >
                    <select
                      v-model="filters.orden"
                      id="filtro-orden"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="a-z">A-Z</option>
                      <option value="z-a">Z-A</option>
                    </select>
                  </div>
                  <!-- Filtro por Aplicación -->
                  <div>
                    <label for="filtro-app" class="text-[14px] text-secundary-light"
                      >Aplicación</label
                    >
                    <select
                      v-model="filters.aplicacion"
                      id="filtro-app"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todas las Apps</option>
                      <option
                        v-for="app in filtrosDisponibles.aplicaciones"
                        :key="app"
                        :value="app"
                      >
                        {{ app }}
                      </option>
                    </select>
                  </div>
                  <!-- Filtro por Membresía -->
                  <div>
                    <label for="filtro-membresia" class="text-[14px] text-secundary-light"
                      >Membresía</label
                    >
                    <select
                      v-model="filters.membresia"
                      id="filtro-membresia"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todas las Membresías</option>
                      <option
                        v-for="mem in filtrosDisponibles.membresias"
                        :key="mem"
                        :value="mem"
                      >
                        {{ mem }}
                      </option>
                    </select>
                  </div>
                  <!-- Filtro por Ciudad -->
                  <div>
                    <label for="filtro-ciudad" class="text-[14px] text-secundary-light"
                      >Ciudad</label
                    >
                    <select
                      v-model="filters.ciudad"
                      id="filtro-ciudad"
                      class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                      <option value="all">Todas las Ciudades</option>
                      <option
                        v-for="ciudad in filtrosDisponibles.ciudades"
                        :key="ciudad"
                        :value="ciudad"
                      >
                        {{ ciudad }}
                      </option>
                    </select>
                  </div>
                  <div v-if="isAnyFilterActive">
                    <button
                      @click="resetFilters"
                      class="w-full mt-2 py-2 px-4 text-sm font-medium text-gray-300 bg-gray-700/50 rounded-md hover:bg-gray-700 transition-colors"
                    >
                      Restablecer Filtros
                    </button>
                  </div>
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
                      v-model="searchTerm"
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
                    <div>
                      <button
                        @click="viewMode = 'table'"
                        class="material-symbols-rounded p-2 text-[18px] rounded-xl transition-colors"
                        :class="
                          viewMode === 'table'
                            ? 'bg-primary text-white'
                            : 'border border-transparent hover:border-primary'
                        "
                      >
                        table
                      </button>
                      <button
                        @click="viewMode = 'list'"
                        class="material-symbols-rounded p-2 text-[18px] rounded-xl transition-colors"
                        :class="
                          viewMode === 'list'
                            ? 'bg-primary text-white'
                            : 'border border-transparent hover:border-primary'
                        "
                      >
                        lists
                      </button>
                      <button
                        @click="viewMode = 'grid'"
                        class="material-symbols-rounded p-2 text-[18px] rounded-xl transition-colors"
                        :class="
                          viewMode === 'grid'
                            ? 'bg-primary text-white'
                            : 'border border-transparent hover:border-primary'
                        "
                      >
                        grid_view
                      </button>
                    </div>

                    <div class="text-[30px]">|</div>
                    <div
                      class="flex gap-2 items-center rounded-xl bg-mono-negro_opacity_medio"
                    >
                      <button
                        @click="toggleSelectionMode"
                        class="relative material-symbols-rounded p-2 text-[18px] rounded-xl transition-colors"
                        :class="
                          isSelectionModeActive
                            ? 'bg-primary text-white'
                            : 'border border-secundary-light hover:border-primary'
                        "
                      >
                        atr
                        <p
                          v-if="hasSelectedUsers"
                          class="absolute bg-mono-blanco text-primary text-[14px] w-4 h-4 flex justify-center items-center font-semibold rounded-full -top-2 -right-1"
                        >
                          <span>{{ selectedUserIds.length }}</span>
                        </p>
                      </button>

                      <Transition name="fade">
                        <button
                          v-if="hasSelectedUsers"
                          @click="bulkDeleteSelectedUsers"
                          class="material-symbols-rounded p-2 text-[18px] border border-semaforo-rojo bg-semaforo-rojo rounded-xl"
                        >
                          delete
                        </button>
                      </Transition>

                      <button
                        @click="openPapeleraModal()"
                        class="material-symbols-rounded p-2 text-[18px] border border-secundary-light hover:text-primary hover:border-primary rounded-xl"
                      >
                        folder_delete
                      </button>
                      <button
                        @click="openUserCreationModal()"
                        class="py-2 px-5 text-[14px] bg-primary shadow-[0_5px_20px] shadow-primary rounded-xl"
                      >
                        Agregar cliente
                      </button>
                    </div>
                  </div>
                </div>
                <div
                  class="my-3 min-h-[75dvh] max-h-[80dvh] overflow-auto scrollbar-custom"
                >
                  <!-- Mensaje para cuando no hay resultados -->
                  <div
                    v-if="filteredUsers.length === 0"
                    class="text-center text-gray-400 mt-10"
                  >
                    <p class="text-lg">
                      No se encontraron usuarios que coincidan con tu búsqueda.
                    </p>
                  </div>

                  <!-- ✅ VISTA DE TABLA -->
                  <table
                    v-else-if="viewMode === 'table'"
                    class="w-full text-sm text-left text-gray-300"
                  >
                    <thead class="text-xs text-gray-200 uppercase bg-gray-700/50">
                      <tr>
                        <th v-if="isSelectionModeActive" class="px-2 py-3 w-4"></th>
                        <th scope="col" class="px-6 py-3">Usuario</th>
                        <th scope="col" class="px-6 py-3">Contacto</th>
                        <th scope="col" class="px-6 py-3">Rol</th>
                        <th scope="col" class="px-6 py-3">Establecimiento</th>
                        <th scope="col" class="px-6 py-3">
                          <span class="sr-only">Acciones</span>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="usuario in misEmpleados"
                        :key="usuario.id"
                        @click="openUserDetailsModal(usuario)"
                        class="border-b border-gray-700 hover:bg-gray-800/50 cursor-pointer"
                      >
                        <td v-if="isSelectionModeActive" class="px-2">
                          <input
                            type="checkbox"
                            :value="usuario.id"
                            v-model="selectedUserIds"
                            @click.stop
                            class="form-checkbox rounded bg-gray-700 border-gray-600 text-primary focus:ring-primary"
                          />
                        </td>
                        <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
                          <div class="flex items-center gap-3">
                            <img
                              v-if="fotoUrlCompletaUsuario(usuario)"
                              :src="fotoUrlCompletaUsuario(usuario)"
                              alt="Foto"
                              class="w-10 h-10 rounded-full object-cover"
                            />
                            <div
                              v-else
                              class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-bold"
                            >
                              {{ getInitials(usuario) }}
                            </div>
                            <span
                              v-html="
                                highlightMatch(
                                  `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.primer_apellido}`
                                )
                              "
                            ></span>
                          </div>
                        </td>
                        <td
                          class="px-6 py-4"
                          v-html="highlightMatch(usuario.perfil_usuario.correo)"
                        ></td>
                        <td
                          class="px-6 py-4"
                          v-html="highlightMatch(usuario.perfil_usuario.rol.tipo_rol)"
                        ></td>
                        <td
                          class="px-6 py-4"
                          v-html="
                            highlightMatch(
                              usuario.establecimiento_asignado?.nombre_establecimiento
                            )
                          "
                        ></td>
                        <td class="px-6 py-4 text-right">
                          <button
                            class="material-symbols-rounded p-1 text-gray-400 hover:text-primary"
                          >
                            more_vert
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- ✅ VISTA DE LISTA Y CUADRÍCULA (GRID) -->
                  <div
                    v-else
                    class="cardsUsuarios"
                    :class="{
                      'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4':
                        viewMode === 'grid',
                    }"
                  >
                    <div
                      v-for="usuario in misEmpleados"
                      :key="usuario.id"
                      @click="openUserDetailsModal(usuario)"
                      class="contCard hover:cursor-pointer bg-mono-negro_opacity_medio backdrop-blur-xl p-3 rounded-[10px] transition-all duration-300"
                      :class="{
                        'my-2 flex items-center gap-5 justify-between':
                          viewMode === 'list',
                        'flex flex-col gap-3': viewMode === 'grid',
                      }"
                    >
                      <!-- Contenido de la tarjeta (se adapta automáticamente) -->
                      <div
                        class="flex relative"
                        :class="{
                          'w-[70%] items-center': viewMode === 'list',
                          'w-full flex-col items-center text-center': viewMode === 'grid',
                        }"
                      >
                        <!-- ... (código del indicador de sesión) ... -->
                        <div
                          class="flex-shrink-0"
                          :class="{ 'mb-4': viewMode === 'grid' }"
                        >
                          <!-- ... (código de la foto o iniciales) ... -->
                        </div>
                        <div
                          class="datosPersonales w-full"
                          :class="{ 'mx-3': viewMode === 'list' }"
                        >
                          <h3
                            class="text-mono-negro font-semibold dark:text-mono-blanco text-lg"
                            v-html="
                              highlightMatch(
                                `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.primer_apellido}`
                              )
                            "
                          ></h3>
                          <p
                            class="text-sm text-gray-400"
                            v-html="highlightMatch(usuario.perfil_usuario.correo)"
                          ></p>
                          <!-- ... (resto de los datos de la tarjeta) ... -->
                        </div>
                      </div>
                      <p v-if="viewMode === 'list'" class="text-[50px] font-extralight">
                        |
                      </p>
                      <div
                        class="flex-shrink-0"
                        :class="{
                          'w-[25%]': viewMode === 'list',
                          'w-full border-t border-gray-700 pt-3 text-center':
                            viewMode === 'grid',
                        }"
                      >
                        <h4
                          class="font-semibold dark:text-mono-blanco text-md"
                          v-html="
                            highlightMatch(
                              usuario.establecimiento_asignado?.nombre_establecimiento
                            )
                          "
                        ></h4>
                        <p
                          class="text-sm text-gray-400"
                          v-html="highlightMatch(usuario.perfil_empleado.cargo)"
                        ></p>
                        <div
                          v-if="isSelectionModeActive"
                          class="absolute top-2 left-2 z-20"
                        >
                          <input
                            type="checkbox"
                            :value="usuario.id"
                            v-model="selectedUserIds"
                            @click.stop
                            class="form-checkbox h-5 w-5 rounded bg-gray-800 border-gray-600 text-primary focus:ring-primary"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </SidebarSuperAdmin>
      <ModalDetallesUsuario :is-open="isModalOpen" @close="closeUserDetailsModal" />
      <ModalCrearUsuario
        :is-open="isModalOpenCrearUser"
        :establecimientosDisponibles="establecimientosDisponibles"
        @close="closeUserCreationModal"
      />
      <ModalPapeleraUsuario
        :is-open="isModalOpenPapelera"
        @close="closePapeleraModal"
        :usuarios-en-papelera="usuariosEnPapelera"
      />
    </div>
  </div>
</template>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
