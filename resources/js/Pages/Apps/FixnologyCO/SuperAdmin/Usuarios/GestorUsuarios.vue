<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted, computed } from "vue";
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
  misEmpleados: {
    type: Array,
    required: true,
  },
  todosLosUsuarios: {
    type: Array,
    required: true,
  },
  usuariosEnPapelera: Array, // <-- Nueva prop
  establecimientosDisponibles: Array
});
const page = usePage();

const usuario = authStore.user;
const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

// Crea una lista de establecimientos únicos para el selector
const listaEstablecimientos = computed(() => {
  const establecimientos = props.todosLosUsuarios
    .map(u => u.establecimiento_propietario)
    .filter(est => est != null); // Filtra los nulos

  // Elimina duplicados
  return [...new Map(establecimientos.map(item => [item['id'], item])).values()];
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

// ✅ PROPIEDAD COMPUTADA MEJORADA PARA FILTRAR TODOS LOS CAMPOS
const filteredUsers = computed(() => {
  if (!searchTerm.value.trim()) {
    return props.todosLosUsuarios; // Si no hay búsqueda, muestra todos
  }

  const lowerCaseSearch = searchTerm.value.toLowerCase();

  return props.todosLosUsuarios.filter((usuario) => {
    // Creamos una cadena de texto con todos los datos que queremos buscar
    const searchableContent = [
      usuario.perfil_usuario?.primer_nombre,
      usuario.perfil_usuario?.primer_apellido,
      usuario.perfil_usuario?.correo,
      usuario.perfil_usuario?.telefono,
      usuario.perfil_usuario?.barrio_residencia,
      usuario.perfil_usuario?.ciudad_residencia,
      usuario.perfil_usuario?.rol?.tipo_rol,
      usuario.perfil_usuario?.estado?.tipo_estado,
      usuario.perfil_empleado?.cargo,
      usuario.establecimiento_asignado?.nombre_establecimiento,
      usuario.numero_documento,
    ]
      .join(" ") // Unimos todo en un solo string separado por espacios
      .toLowerCase(); // Convertimos todo a minúsculas

    // Devolvemos true si la cadena de contenido incluye el término de búsqueda
    return searchableContent.includes(lowerCaseSearch);
  });
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
                <button v-for="(tab, index) in tabs" :key="index" @click="activeTab = index" :class="[
                  'text-[12px] font-medium px-4 py-2',
                  activeTab === index
                    ? 'text-primary border-b-2 border-b-primary'
                    : 'text-secundary-default dark:text-secundary-light dark:hover:text-primary',
                ]">
                  {{ tab.label }}
                </button>
              </nav>
            </div>

            <div v-if="activeTab === 0" class="flex gap-5 w-full max-h-[80dvh]">
              <div class="filtro w-[20%]">
                <div class="header flex items-center justify-between">
                  <h1 class="text-[30px] font-bold">Fixgis</h1>
                  <div class="contador border border-secundary-light rounded-md px-2 py-1 text-[12px]">
                    {{ todosLosUsuarios.length }} Total usuarios
                  </div>
                </div>
                <div
                  class="contenidoFiltro bg-secundary-opacity rounded-lg border border-secundary-light py-2 px-3 mt-3">
                  <label for="" class="text-[14px] text-secundary-light">Estado cliente</label>
                </div>
              </div>
              <div class="contenido w-[80%]">
                <div class="header flex items-end justify-between">
                  <div
                    class="flex items-center gap-2 border p-2 rounded-xl bg-mono-negro_opacity_medio backdrop-blur-xl w-[70%]">
                    <span class="material-symbols-rounded">explore</span>
                    <input type="search" name="search" placeholder="Buscar usuario" id="" v-model="searchTerm"
                      class="bg-transparent outline-none w-full" />
                    <p>|</p>
                    <div class="flex gap-1 text-[12px] hover:text-primary cursor-pointer">
                      <span class="material-symbols-rounded text-[16px]">ar_on_you</span>
                      <p>Escanear</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3 rounded-xl bg-mono-negro_opacity_medio">
                    <div class="">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl">
                        lists
                      </button>

                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl">
                        grid_view
                      </button>
                    </div>

                    <div class="text-[30px]">|</div>
                    <div class="flex gap-2 items-center rounded-xl bg-mono-negro_opacity_medio">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-secundary-light hover:text-primary hover:border-primary rounded-xl">
                        atr
                      </button>

                      <button @click="openPapeleraModal()"
                        class="material-symbols-rounded p-2 text-[18px] border border-secundary-light hover:text-primary hover:border-primary rounded-xl">
                        folder_delete
                      </button>
                      <button @click="openUserCreationModal()"
                        class="py-2 px-5 text-[14px] bg-primary shadow-[0_5px_20px] shadow-primary rounded-xl">
                        Agregar cliente
                      </button>
                    </div>
                  </div>
                </div>
                <div class="cardsUsuarios my-3 min-h-[75dvh] max-h-[80dvh] overflow-auto scrollbar-custom">
                  <div v-if="filteredUsers.length === 0" class="text-center text-gray-400 mt-10">
                    <p class="text-lg">
                      No se encontraron usuarios que coincidan con tu búsqueda.
                    </p>
                  </div>
                  <div v-for="usuario in filteredUsers" :key="usuario.id"
                    class="contCard hover:bg-mono-blanco_opacity hover:cursor-pointer my-2 flex items-center gap-5 justify-between bg-mono-negro_opacity_medio backdrop-blur-xl p-3 rounded-[10px]">
                    <div class="flex items-center relative w-[70%]">
                      <div class="w-3.5 border-2 h-3.5 absolute -top-1 -left-1 z-10 shadowM rounded-full" :class="usuario.tiene_sesion_activa
                        ? 'bg-semaforo-verde'
                        : 'bg-semaforo-rojo'
                        "></div>

                      <template v-if="!usuario.perfil_usuario.ruta_foto">
                        <div
                          class="relative h-[100px] w-[115px] rounded-[10px] overflow-hidden flex items-center justify-center text-mono-blanco bg-primary shadow-[0px_8px_20px] shadow-primary">
                          <span class="text-[25px] font-bold">{{
                            getInitials(usuario)
                            }}</span>
                        </div>
                      </template>

                      <template v-else>
                        <img :src="usuario.perfil_usuario.ruta_foto"
                          class="border-2 rounded-[10px] w-[100px] h-[100px] object-cover shadowM" />
                      </template>
                      <div class="datosPersonales mx-3 w-[100%]">
                        <div class="">
                          <div class="flex items-center gap-2">
                            <h3 class="text-mono-negro font-semibold dark:text-mono-blanco text-[25px]" v-html="highlightMatch(
                              `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.segundo_nombre} ${usuario.perfil_usuario.primer_apellido} ${usuario.perfil_usuario.segundo_apellido}`
                            )
                              "></h3>
                            <div class="grid place-items-center" v-if="usuario.google_id === null">
                              <span class="material-symbols-rounded text-[18px] text-gray-700">verified_off</span>
                            </div>
                            <div class="" v-else>
                              <span
                                class="grid place-items-center material-symbols-rounded text-[18px] text-universal-azul_secundaria">verified</span>
                            </div>
                          </div>
                          <div class="flex justify-between items-center">
                            <p class="flex items-center gap-1">
                              <span class="material-symbols-rounded text-[18px] text-primary">email</span>
                            <p v-html="highlightMatch(
                              `${usuario.perfil_usuario.correo}`
                            )
                              "></p>
                            </p>
                            <p class="flex items-center gap-1">
                              <span class="material-symbols-rounded text-[18px] text-primary">map</span>
                            <p v-html="highlightMatch(
                              `${usuario.perfil_usuario.ciudad_residencia}, ${usuario.perfil_usuario.barrio_residencia}`
                            )
                              "></p>,



                            </p>
                            <p class="flex items-center gap-1">
                              <span class="material-symbols-rounded text-[18px] text-primary">phone</span>
                            <p v-html="highlightMatch(
                              `${usuario.perfil_usuario.indicativo.codigo_pais} ${usuario.perfil_usuario.telefono}`
                            )
                              "></p>

                            </p>
                          </div>
                        </div>
                        <div class="caract flex justify-between items-center">
                          <p class="flex items-center gap-1">
                            <span class="material-symbols-rounded text-[18px] text-primary">security</span>
                          <p v-html="highlightMatch(
                            `${usuario.perfil_usuario.rol.tipo_rol}`
                          )
                            "></p>
                          </p>
                          <div class="px-2 py-1 text-[14px] rounded-md" :class="getEstadoClass(usuario.perfil_usuario.estado.tipo_estado)
                            ">
                            <p v-html="highlightMatch(
                              `${usuario.perfil_usuario.estado.tipo_estado}`
                            )
                              "></p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="text-[50px] font-extralight">|</p>

                    <div class="w-[25%]">
                      <h3 class="text-mono-negro font-semibold dark:text-mono-blanco text-[25px]" v-html="highlightMatch(
                        `${usuario?.establecimiento_asignado?.nombre_establecimiento}`
                      )
                        ">

                      </h3>

                      <div class="flex justify-between items-center">
                        <p class="flex items-center gap-1">
                          <span class="material-symbols-rounded text-[18px] text-primary">work</span>
                        <p v-html="highlightMatch(
                          `${usuario?.perfil_empleado?.cargo}`
                        )
                          "></p>
                        </p>
                      </div>

                      <div v-if="
                        usuario.establecimiento_asignado?.facturas &&
                        usuario.establecimiento?.facturas.length > 0
                      ">
                        <p>
                          <strong>Pago:</strong>
                          {{
                            formatFecha(usuario.establecimiento?.facturas[0].fecha_pago)
                          }}
                        </p>
                        <p class="text-lg font-bold" :class="usuario.establecimiento?.facturas[0].estado.tipo_estado ===
                          'Pagado'
                          ? 'text-green-400'
                          : 'text-red-400'
                          ">
                          {{
                            formatCOP(usuario.establecimiento?.facturas[0].monto_total)
                          }}
                        </p>
                      </div>

                      <div v-else>
                        <div v-if="usuario" class="text-sm text-gray-400 mt-2">
                          {{ usuario?.perfil_empleado?.cargo }} -
                          <strong class="text-primary font-semibold">
                            {{ usuario.establecimiento_asignado?.nombre_establecimiento }}
                          </strong>
                        </div>
                        <div v-else class="text-sm text-gray-500">Sin Información :c</div>
                      </div>
                    </div>
                    <div class="w-[4%] justify-end flex" @click="openUserDetailsModal(usuario)">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl">
                        info
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeTab === 1" class="flex gap-5 w-full max-h-[80dvh]">
              <div class="filtro w-[20%]">
                <div class="header flex items-center justify-between">
                  <h1 class="text-[30px] font-bold">Colaboradores</h1>
                  <div class="contador border border-secundary-light rounded-md px-2 py-1 text-[12px]">
                    {{ misEmpleados.length }} Total usuarios
                  </div>
                </div>
                <div
                  class="contenidoFiltro bg-secundary-opacity rounded-lg border border-secundary-light py-2 px-3 mt-3">
                  <label for="" class="text-[14px] text-secundary-light">Estado cliente</label>
                </div>
              </div>
              <div class="contenido w-[80%]">
                <div class="header flex items-end justify-between">
                  <div
                    class="flex items-center gap-2 border p-2 rounded-xl bg-mono-negro_opacity_medio backdrop-blur-xl w-[70%]">
                    <span class="material-symbols-rounded">explore</span>
                    <input type="search" name="search" placeholder="Buscar usuario" id=""
                      class="bg-transparent outline-none w-full" />
                    <p>|</p>
                    <div class="flex gap-1 text-[12px] hover:text-primary cursor-pointer">
                      <span class="material-symbols-rounded text-[16px]">ar_on_you</span>
                      <p>Escanear</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3 rounded-xl bg-mono-negro_opacity_medio">
                    <div class="">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl">
                        lists
                      </button>

                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl">
                        grid_view
                      </button>
                    </div>

                    <div class="text-[30px]">|</div>
                    <div class="flex gap-2 items-center rounded-xl bg-mono-negro_opacity_medio">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-secundary-light hover:text-primary hover:border-primary rounded-xl">
                        atr
                      </button>
                      <button @click="openUserCreationModal()"
                        class="py-2 px-5 text-[14px] bg-primary shadow-[0_5px_20px] shadow-primary rounded-xl">
                        Agregar cliente
                      </button>
                    </div>
                  </div>
                </div>
                <div class="cardsUsuarios my-3 min-h-[75dvh] max-h-[80dvh] overflow-auto scrollbar-custom">
                  <div v-for="usuario in misEmpleados" :key="usuario.id" @click="openUserDetailsModal(usuario)"
                    class="contCard my-2 flex items-center gap-5 justify-between bg-mono-negro_opacity_medio backdrop-blur-xl p-3 rounded-[10px]">
                    <div class="flex items-center relative w-[70%]">
                      <div class="w-3.5 border-2 h-3.5 absolute -top-1 -left-1 z-10 shadowM rounded-full" :class="usuario.tiene_sesion_activa
                        ? 'bg-semaforo-verde'
                        : 'bg-semaforo-rojo'
                        "></div>

                      <template v-if="!usuario.perfil_usuario.ruta_foto">
                        <div
                          class="relative h-[100px] w-[115px] rounded-[10px] overflow-hidden flex items-center justify-center text-mono-blanco bg-primary shadow-[0px_8px_20px] shadow-primary">
                          <span class="text-[25px] font-bold">{{
                            getInitials(usuario)
                            }}</span>
                        </div>
                      </template>

                      <template v-else>
                        <img :src="usuario.perfil_usuario.ruta_foto"
                          class="border-2 rounded-[10px] w-[100px] h-[100px] object-cover shadowM" />
                      </template>
                      <div class="datosPersonales mx-3 w-[100%]">
                        <div class="">
                          <div class="flex items-center gap-2">
                            <h3 class="text-mono-negro font-semibold dark:text-mono-blanco text-[25px]">
                              {{ usuario.perfil_usuario.primer_nombre }}
                              {{ usuario.perfil_usuario.segundo_nombre }}
                              {{ usuario.perfil_usuario.primer_apellido }}
                              {{ usuario.perfil_usuario.segundo_apellido }}
                            </h3>
                            <div class="grid place-items-center" v-if="usuario.google_id === null">
                              <span class="material-symbols-rounded text-[18px] text-gray-700">verified_off</span>
                            </div>
                            <div class="" v-else>
                              <span
                                class="grid place-items-center material-symbols-rounded text-[18px] text-universal-azul_secundaria">verified</span>
                            </div>
                          </div>
                          <div class="flex justify-between items-center">
                            <p class="flex items-center gap-1">
                              <span class="material-symbols-rounded text-[18px] text-primary">email</span>
                              {{ usuario.perfil_usuario.correo }}
                            </p>
                            <p class="flex items-center gap-1">
                              <span class="material-symbols-rounded text-[18px] text-primary">map</span>{{
                                usuario.perfil_usuario.ciudad_residencia }},
                              {{ usuario.perfil_usuario.barrio_residencia }}
                            </p>
                            <p class="flex items-center gap-1">
                              <span class="material-symbols-rounded text-[18px] text-primary">phone</span>
                              {{ usuario.perfil_usuario.indicativo.codigo_pais }}
                              {{ usuario.perfil_usuario.telefono }}
                            </p>
                          </div>
                        </div>
                        <div class="caract flex justify-between items-center">
                          <p class="flex items-center gap-1">
                            <span class="material-symbols-rounded text-[18px] text-primary">security</span>{{
                              usuario.perfil_usuario.rol.tipo_rol }}
                          </p>
                          <div class="px-2 py-1 text-[14px] rounded-md" :class="getEstadoClass(usuario.perfil_usuario.estado.tipo_estado)
                            ">
                            {{ usuario.perfil_usuario.estado.tipo_estado }}
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="text-[50px] font-extralight">|</p>

                    <div class="w-[25%]">
                      <h3 class="text-mono-negro font-semibold dark:text-mono-blanco text-[25px]">
                        {{
                          usuario.establecimiento_asignado.nombre_establecimiento || null
                        }}
                      </h3>

                      <div class="flex justify-between items-center">
                        <p class="flex items-center gap-1">
                          <span class="material-symbols-rounded text-[18px] text-primary">work</span>
                          {{ usuario.perfil_empleado.cargo }}
                        </p>
                      </div>

                      <div v-if="
                        usuario.establecimiento?.facturas &&
                        usuario.establecimiento?.facturas.length > 0
                      ">
                        <p>
                          <strong>Pago:</strong>
                          {{
                            formatFecha(usuario.establecimiento?.facturas[0].fecha_pago)
                          }}
                        </p>
                        <p class="text-lg font-bold" :class="usuario.establecimiento?.facturas[0].estado.tipo_estado ===
                          'Pagado'
                          ? 'text-green-400'
                          : 'text-red-400'
                          ">
                          {{
                            formatCOP(usuario.establecimiento?.facturas[0].monto_total)
                          }}
                        </p>
                      </div>

                      <div v-else>
                        <div v-if="infoUserStore" class="text-sm text-gray-400 mt-2">
                          {{ usuario.perfil_empleado.cargo }} -
                          <strong class="text-primary font-semibold">
                            {{ usuario.establecimiento_asignado.nombre_establecimiento }}
                          </strong>
                        </div>
                        <div v-else class="text-sm text-gray-500">Sin Información :c</div>
                      </div>
                    </div>
                    <div class="w-[4%] justify-end flex">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl">
                        info
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </SidebarSuperAdmin>
      <ModalDetallesUsuario :is-open="isModalOpen" @close="closeUserDetailsModal" />
      <ModalCrearUsuario :is-open="isModalOpenCrearUser" :establecimientosDisponibles="establecimientosDisponibles"
        @close="closeUserCreationModal"/>
      <ModalPapeleraUsuario :is-open="isModalOpenPapelera" @close="closePapeleraModal"
        :usuarios-en-papelera="usuariosEnPapelera" />
    </div>
  </div>
</template>
