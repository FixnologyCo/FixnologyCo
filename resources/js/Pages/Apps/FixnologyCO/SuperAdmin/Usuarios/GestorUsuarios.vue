<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import "dayjs/locale/es";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { formatFecha, formatFechaShort } from "@/Utils/date";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useInfoUser } from "@/stores/infoUsers"; // 1. Importa la tienda
import ModalDetallesUsuario from "@/Components/Modales/FixnologyCO/Usuarios/ModalDetallesUsuario.vue";
import ModalCrearUsuario from "@/Components/Modales/FixnologyCO/Usuarios/ModalCrearUsuario.vue";
import ModalPapeleraUsuario from "@/Components/Modales/FixnologyCO/Usuarios/ModalPapeleraUsuario.vue";
const authStore = useAuthStore();
const infoUserStore = useInfoUser();
defineOptions({
  layout: AuthenticatedLayout,
});
import useEstadoClass from "@/Composables/useEstado";
import { formatCOP } from "@/Utils/formateoMoneda";

const { getEstadoClass } = useEstadoClass();
const props = defineProps({
  todosLosUsuarios: { type: Array, required: true },
  misEmpleados: { type: Array, default: () => [] },
  usuariosEnPapelera: { type: Array },
  establecimientosDisponibles: { type: Array },
  filtrosDisponibles: { type: Object },
  usuariosEnPapelera: { type: Array },
});
const page = usePage();
console.log(props.misEmpleados);

const usuario = authStore.user;
const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

// Crea una lista de establecimientos únicos para el selector
const listaEstablecimientos = computed(() => {
  const establecimientos = props.todosLosUsuarios
    .map((u) => u.establecimiento_propietario)
    .filter((est) => est != null);

  // Elimina duplicados
  return [...new Map(establecimientos.map((item) => [item["id"], item])).values()];
});
function getInitials(usuario) {
  if (!usuario || !usuario.perfil_usuario) {
    return "";
  }

  const primerNombre = usuario.perfil_usuario.primer_nombre || "";
  const primerApellido = usuario.perfil_usuario.primer_apellido || "";

  const inicialNombre = primerNombre.charAt(0).toUpperCase();
  const inicialApellido = primerApellido.charAt(0).toUpperCase();

  // Devuelve las dos iniciales solo si ambas existen
  return inicialNombre && inicialApellido
    ? `${inicialNombre}${inicialApellido}`
    : inicialNombre;
}
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

const searchTerm = ref("");
const filters = ref({
  estado: "all",
  orden: "reciente",
  aplicacion: "all",
  membresia: "all",
  ciudad: "all",
});

const isAnyFilterActive = computed(() => {
  const { estado, orden, antiguedad, aplicacion, membresia, ciudad } = filters.value;
  return (
    estado !== "all" ||
    orden !== "reciente" ||
    aplicacion !== "all" ||
    membresia !== "all" ||
    ciudad !== "all"
  );
});

// ✅ Función para restablecer los filtros a su estado inicial
function resetFilters() {
  filters.value = {
    estado: "all",
    orden: "reciente",
    aplicacion: "all",
    membresia: "all",
    ciudad: "all",
  };
}

// ✅ PROPIEDAD COMPUTADA MEJORADA PARA FILTRAR TODOS LOS CAMPOS
const filteredUsers = computed(() => {
  let users = props.todosLosUsuarios;

  // 1. Aplicar filtro de búsqueda
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

  // 2. Aplicar filtros de selección
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

  // ✅ 3. APLICAR ORDEN MEJORADO
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
        // Compara fechas de la más antigua a la más nueva
        return (
          new Date(a.perfil_usuario?.created_at) - new Date(b.perfil_usuario?.created_at)
        );
      case "reciente":
      default:
        // Compara fechas de la más nueva a la más antigua
        return (
          new Date(b.perfil_usuario?.created_at) - new Date(a.perfil_usuario?.created_at)
        );
    }
  });

  return users;
});

// ✅ FUNCIÓN DE RESALTADO MEJORADA Y MÁS SEGURA
function highlightMatch(text) {
  // Si el texto es nulo o no hay búsqueda, devolvemos el texto original
  if (text === null || !searchTerm.value.trim()) {
    return text;
  }

  // Convertimos a string para asegurar que el método .replace funcione
  const textString = String(text);

  // Usamos una expresión regular para encontrar todas las coincidencias (global 'g')
  // sin importar mayúsculas/minúsculas (insensible 'i')
  const regex = new RegExp(searchTerm.value, "gi");

  return textString.replace(regex, (match) => {
    return `<mark class="bg-primary text-white rounded">${match}</mark>`;
  });
}

const viewMode = ref(localStorage.getItem("userViewMode") || "list");

// b) Observa los cambios en 'viewMode' y guarda el nuevo valor en localStorage.
watch(viewMode, (newValue) => {
  localStorage.setItem("userViewMode", newValue);
});

// Escuchamos los eventos cuando el componente se monta en la página
onMounted(() => {
  // Nos conectamos al canal privado 'user-updates'
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

// --- ESTADO PARA LA SELECCIÓN MÚLTIPLE ---
const isSelectionModeActive = ref(false);
const selectedUserIds = ref([]);

// Propiedad computada para saber si hay usuarios seleccionados
const hasSelectedUsers = computed(() => selectedUserIds.value.length > 0);

function toggleSelectionMode() {
  isSelectionModeActive.value = !isSelectionModeActive.value;
  // Si desactivamos el modo, limpiamos la selección
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
          // Limpiamos y desactivamos el modo de selección después de la acción
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
                            class="form-checkbox rounded bg-gray-700 border-gray-600 text-primary focus:ring-primary"
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
                      v-for="usuario in filteredUsers"
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
                              v-if="usuario.perfil_usuario.ruta_foto"
                              :src="usuario.perfil_usuario.ruta_foto"
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
