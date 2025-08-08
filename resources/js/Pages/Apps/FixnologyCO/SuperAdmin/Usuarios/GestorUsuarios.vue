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
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useInfoUser } from "@/stores/infoUsers";
import "dayjs/locale/es";
import BtnPrimario from "@/Components/Shared/buttons/btnPrimario.vue";
import BtnSecundario from "@/Components/Shared/buttons/btnSecundario.vue";
import VistaUsuarioTabla from "@/Components/InfoCards/UsuariosCards/VistaUsuarioTabla.vue";
import VistaUsuarioLista from "@/Components/InfoCards/UsuariosCards/VistaUsuarioLista.vue";
import VistaUsuarioTarjeta from "@/Components/InfoCards/UsuariosCards/VistaUsuarioTarjeta.vue";
import FiltroUsuarios from "@/Components/Shared/Filtros/FiltroUsuarios.vue";

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

const viewMode = ref(localStorage.getItem("userViewMode") || "list");
watch(viewMode, (newValue) => {
  localStorage.setItem("userViewMode", newValue);
});

const viewComponents = {
  table: VistaUsuarioTabla,
  list: VistaUsuarioLista,
  grid: VistaUsuarioTarjeta,
};

const currentViewComponent = computed(() => viewComponents[viewMode.value]);
const aplicacion = authStore.aplicacion;
const rol = authStore.rol;
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
              <div
                class="options flex gap-1 items-center justify-center text-[14px] mb-10"
              >
                <a
                  :href="route('aplicacion.dashboard', { aplicacion, rol })"
                  class="text-mono-blanco hover:text-secondary flex items-center gap-1"
                >
                  <span class="material-symbols-rounded text-[16px] text-primary"
                    >home</span
                  >
                  <p>Home</p>
                </a>
                <span
                  class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco"
                  >chevron_right</span
                >
                <p class="font-bold text-mono-negro dark:text-mono-blanco">
                  Gestor de usuarios
                </p>
              </div>
            </div>

            <div class="flex gap-5 w-full max-h-[80dvh]">
              <div class="w-[20%]">
                <FiltroUsuarios
                  v-model:filters="filters"
                  :filtros-disponibles="filtrosDisponibles"
                  :results-count="filteredUsers.length"
                  @reset="resetFilters"
                />
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
                  <div
                    v-if="filteredUsers.length === 0"
                    class="text-center text-gray-400 mt-10"
                  >
                    <p class="text-lg">
                      No se encontraron usuarios que coincidan con tu búsqueda.
                    </p>
                  </div>
                  <component
                    v-else
                    :is="currentViewComponent"
                    :users="filteredUsers"
                    :is-selection-mode-active="isSelectionModeActive"
                    :highlight-match="highlightMatch"
                    v-model:selectedUserIds="selectedUserIds"
                    @open-details="openUserDetailsModal"
                  />
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
