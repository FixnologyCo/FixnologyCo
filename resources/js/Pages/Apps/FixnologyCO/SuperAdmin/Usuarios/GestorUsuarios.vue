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
import ModalDetallesUsuario from "@/Components/Modales/FixnologyCO/Usuarios/ModalDetallesUsuario.vue";
const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
});
import useEstadoClass from "@/Composables/useEstado";
import { formatCOP } from "@/Utils/formateoMoneda";
const { getEstadoClass } = useEstadoClass();
const props = defineProps({
  usuariosDelEstablecimiento: Array,
});

// Quita la propiedad computada 'initials' y reemplázala con esta función.

function getInitials(usuario) {
  // Verificación para evitar errores si los datos no existen
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
const tabs = [{ label: "Clientes Fix" }, { label: "Empleados" }];

function irADetalle(id) {
  router.get(
    route("aplicacion.detallesUsuarios.id", {
      aplicacion: props.aplicacion,
      rol: props.rol,
      id: id,
    })
  );
}

const isModalOpen = ref(false);
const selectedUser = ref(null);

function openUserDetailsModal(usuario) {
  selectedUser.value = usuario;
  isModalOpen.value = true;
}

function closeUserDetailsModal() {
  isModalOpen.value = false;
}

/**
 * Busca en las facturas del establecimiento la que le pertenece al usuario actual.
 * @param {object} usuario El objeto del usuario del bucle v-for.
 * @returns {object|null} La factura encontrada o null si no existe.
 */
function getFacturaPropia(usuario) {
  // Obtenemos las facturas del establecimiento de forma segura con encadenamiento opcional (?.)
  const facturasDelEstablecimiento = usuario.perfil_empleado.establecimiento?.facturas;

  // Si no hay un array de facturas, no hay nada que buscar.
  if (!facturasDelEstablecimiento || facturasDelEstablecimiento.length === 0) {
    return null;
  }

  // Usamos el método find() para buscar la factura donde el cliente_id coincide con el id del usuario.
  return facturasDelEstablecimiento.find((factura) => factura.cliente_id === usuario.id);
}
</script>

<template>
  <div>
    <Head title="Gestor de usuarios" />
    <MensajesLayout />

    <div class="flex">
      <SidebarSuperAdmin :authStore>
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

            <div
              v-if="activeTab === 0"
              class="flex gap-5 w-full min-h-[75dvh] max-h-[80dvh] overflow-auto"
            >
              <div class="filtro w-[20%]">
                <div class="header flex items-center justify-between">
                  <h1 class="text-[30px] font-bold">Fixgis</h1>
                  <div
                    class="contador border border-secundary-light rounded-md px-2 py-1 text-[12px]"
                  >
                    {{ usuariosDelEstablecimiento.length }} Total usuarios
                  </div>
                </div>
                <div
                  class="contenidoFiltro bg-secundary-opacity rounded-lg border border-secundary-light py-2 px-3 mt-3"
                >
                  <label for="" class="text-[14px] text-secundary-light"
                    >Estado cliente</label
                  >
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
                    <div class="">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl"
                      >
                        lists
                      </button>

                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl"
                      >
                        grid_view
                      </button>
                    </div>

                    <div class="text-[30px]">|</div>
                    <div
                      class="flex gap-2 items-center rounded-xl bg-mono-negro_opacity_medio"
                    >
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-secundary-light hover:text-primary hover:border-primary rounded-xl"
                      >
                        atr
                      </button>
                      <button
                        class="py-2 px-5 text-[14px] bg-primary shadow-[0_5px_20px] shadow-primary rounded-xl"
                      >
                        Agregar cliente
                      </button>
                    </div>
                  </div>
                </div>
                <div class="cardsUsuarios my-3">
                  <div
                    v-for="usuario in usuariosDelEstablecimiento"
                    :key="usuario.id"
                    @click="openUserDetailsModal(usuario)"
                    class="contCard my-2 flex items-center gap-5 justify-between bg-mono-negro_opacity_medio backdrop-blur-xl p-3 rounded-[10px]"
                  >
                    <div class="flex items-center relative w-[70%]">
                      <div
                        class="w-3.5 border-2 h-3.5 absolute -top-1 -left-1 z-10 shadowM rounded-full"
                        :class="
                          usuario.tiene_sesion_activa
                            ? 'bg-semaforo-verde'
                            : 'bg-semaforo-rojo'
                        "
                      ></div>

                      <template v-if="!usuario.perfil_usuario.ruta_foto">
                        <div
                          class="relative h-[100px] w-[115px] rounded-[10px] overflow-hidden flex items-center justify-center text-mono-blanco bg-primary shadow-[0px_8px_20px] shadow-primary"
                        >
                          <span class="text-[25px] font-bold">{{
                            getInitials(usuario)
                          }}</span>
                        </div>
                      </template>

                      <template v-else>
                        <img
                          :src="usuario.perfil_usuario.ruta_foto"
                          class="border-2 rounded-[10px] w-[100px] h-[100px] object-cover shadowM"
                        />
                      </template>
                      <div class="datosPersonales mx-3 w-[100%]">
                        <div class="">
                          <div class="flex items-center gap-2">
                            <h3
                              class="text-mono-negro font-semibold dark:text-mono-blanco text-[25px]"
                            >
                              {{ usuario.perfil_usuario.primer_nombre }}
                              {{ usuario.perfil_usuario.segundo_nombre }}
                              {{ usuario.perfil_usuario.primer_apellido }}
                              {{ usuario.perfil_usuario.segundo_apellido }}
                            </h3>
                            <div
                              class="grid place-items-center"
                              v-if="usuario.google_id === null"
                            >
                              <span
                                class="material-symbols-rounded text-[18px] text-gray-700"
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
                          <div class="flex justify-between items-center">
                            <p class="flex items-center gap-1">
                              <span
                                class="material-symbols-rounded text-[18px] text-primary"
                                >email</span
                              >
                              {{ usuario.perfil_usuario.correo }}
                            </p>
                            <p class="flex items-center gap-1">
                              <span
                                class="material-symbols-rounded text-[18px] text-primary"
                                >map</span
                              >{{ usuario.perfil_usuario.ciudad_residencia }},
                              {{ usuario.perfil_usuario.barrio_residencia }}
                            </p>
                            <p class="flex items-center gap-1">
                              <span
                                class="material-symbols-rounded text-[18px] text-primary"
                                >phone</span
                              >{{ usuario.perfil_usuario.indicativo.codigo_pais }}
                              {{ usuario.perfil_usuario.telefono }}
                            </p>
                          </div>
                        </div>
                        <div class="caract flex justify-between items-center">
                          <p class="flex items-center gap-1">
                            <span
                              class="material-symbols-rounded text-[18px] text-primary"
                              >security</span
                            >{{ usuario.perfil_usuario.rol.tipo_rol }}
                          </p>
                          <div
                            class="px-2 py-1 text-[14px] rounded-md"
                            :class="
                              getEstadoClass(usuario.perfil_usuario.estado.tipo_estado)
                            "
                          >
                            {{ usuario.perfil_usuario.estado.tipo_estado }}
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="text-[50px] font-extralight">|</p>

                    <div class="w-[25%]">
                      <h3
                        class="text-mono-negro font-semibold dark:text-mono-blanco text-[25px]"
                      >
                        {{
                          usuario.perfil_empleado.establecimientos.nombre_establecimiento
                        }}
                      </h3>

                      <div class="flex justify-between items-center">
                        <p class="flex items-center gap-1">
                          <span class="material-symbols-rounded text-[18px] text-primary"
                            >work</span
                          >
                          {{ usuario.perfil_empleado.cargo }}
                        </p>
                      </div>

                      <div v-if="(facturaPropia = getFacturaPropia(usuario))">
                        <p>
                          <strong>Su Pago:</strong>
                          {{ formatFecha(facturaPropia.fecha_pago) }}
                        </p>
                        <p
                          class="text-lg font-bold"
                          :class="
                            facturaPropia.estado.tipo_estado === 'Pagado'
                              ? 'text-green-400'
                              : 'text-red-400'
                          "
                        >
                          {{ formatCOP(facturaPropia.monto_total) }}
                        </p>
                      </div>

                      <div v-else>
                        <p class="text-sm text-gray-500">Empleado del establecimiento.</p>
                      </div>
                    </div>
                    <div class="w-[4%] justify-end flex">
                      <button
                        class="material-symbols-rounded p-2 text-[18px] border border-transparent hover:text-primary hover:border-primary rounded-xl"
                      >
                        info
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 1">
              <div class="overflow-x-auto bg-gray-800 rounded-lg shadow">
                <table class="w-full text-sm text-left text-gray-300">
                  <thead class="text-xs text-gray-200 uppercase bg-gray-700">
                    <tr>
                      <th scope="col" class="px-6 py-3">Usuario</th>
                      <th scope="col" class="px-6 py-3">Rol</th>
                      <th scope="col" class="px-6 py-3">Cargo en Establecimiento</th>
                      <th scope="col" class="px-6 py-3">Estado</th>
                      <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Acciones</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="usuario in usuariosDelEstablecimiento"
                      :key="usuario.id"
                      @click="openUserDetailsModal(usuario)"
                      class="border-b bg-gray-800 border-gray-700 hover:bg-gray-600"
                    >
                      <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
                        <div class="flex items-center gap-3">
                          <img
                            :src="usuario.perfil_usuario.ruta_foto"
                            alt="Foto de perfil"
                            class="w-10 h-10 rounded-full object-cover"
                          />
                          <div>
                            <div class="text-base font-semibold">
                              {{ usuario.perfil_usuario.primer_nombre }}
                              {{ usuario.perfil_usuario.primer_apellido }}
                            </div>
                            <div class="font-normal text-gray-400">
                              {{ usuario.email }}
                            </div>
                          </div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        {{ usuario.perfil_usuario.rol.tipo_rol }}
                      </td>

                      <td class="px-6 py-4">
                        {{ usuario.perfil_empleado.cargo }}
                      </td>

                      <td class="px-6 py-4">
                        <span
                          class="px-2 py-1 text-xs font-medium rounded-full"
                          :class="
                            getEstadoClass(usuario.perfil_empleado.estado.tipo_estado)
                          "
                        >
                          {{ usuario.perfil_empleado.estado.tipo_estado }}
                        </span>
                      </td>
                      <td class="px-6 py-4 text-right">
                        <button
                          @click.stop="openUserDetailsModal(usuario)"
                          class="font-medium text-blue-500 hover:underline"
                        >
                          Info
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <ModalDetallesUsuario
            :is-open="isModalOpen"
            :user="selectedUser"
            @close="closeUserDetailsModal"
          />
        </div>
      </SidebarSuperAdmin>
    </div>
  </div>
</template>
