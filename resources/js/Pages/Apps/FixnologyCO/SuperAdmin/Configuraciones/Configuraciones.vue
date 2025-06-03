<script setup>

import { Head, usePage, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { defineProps, computed, ref } from 'vue';
import SidebarSuperAdmin from '@/Components/Sidebar/FixnologyCO/Sidebar.vue';
import Header from '@/Components/header/Header.vue';
import dayjs from 'dayjs'
import 'dayjs/locale/es' // ✅ Importa el idioma español
import Colors from '@/Composables/ModularColores';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';

const { appName, bgClase, bgOpacity, focus, textoClase, buttonFocus, borderClase, hover } = Colors();

dayjs.locale('es')

const props = defineProps({
  auth: {
    type: Object,
  },
  clientes: {
    type: Array,
    default: () => [],
  },
  aplicacion: {
    type: String,
    default: ''
  },
  errors: {
    type: Object,
    required: true
  }
})

const user = props.auth.user


const logout = () => {
  router.visit(route('logout'), {
    method: 'post',
    preserveScroll: true,
  });
};

const inicialesNombreUsuario = computed(() => {
  const nombres = props.auth.user?.nombres_ct || '';
  const apellidos = props.auth.user?.apellidos_ct || '';


  const firstNameInitial = nombres.split(' ')[0]?.charAt(0).toUpperCase() || '';
  const lastNameInitial = apellidos.split(' ')[0]?.charAt(0).toUpperCase() || '';

  return firstNameInitial + lastNameInitial;
});

const formatCOP = (value) => {
  if (value === null || value === undefined || isNaN(value)) {
    return 'Sin precio';
  }
  return parseFloat(value).toLocaleString('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });
};

dayjs.locale('es');
const formatFecha = (fecha) => {
  if (!fecha || !dayjs(fecha).isValid()) return 'Sin fecha';
  return dayjs(fecha).format('dddd D [de] MMMM [de] YYYY [a las] h:mm a');
};
const page = usePage();

const activeTab = ref(0)

const tabs = [
  { label: 'Cliente' },
  { label: 'Tienda' },
  { label: 'Plan' },
  { label: 'Membresía' },
  { label: 'Token' },
  { label: 'Ajustes avanzados' }
]

function getEstadoClass(estado) {
  if (estado === 'Inactivo' || estado === 'Pendiente') return 'bg-semaforo-rojo shadow-rojo';
  if (estado === 'Suspendido') return 'bg-semaforo-amarillo shadow-amarillo';
  if (estado === 'Activo' || estado === 'Pagada') return 'bg-semaforo-verde shadow-verde';
  return '';
}
</script>

<template>
  <div>

    <Head title="Configuraciones" />
    <MensajesLayout />


    <div class="flex">
      <SidebarSuperAdmin :auth="auth" :cantidad-apps="cantidadApps" :cantidad-clientes-rol1="cantidadClientesRol1"
        :usuarios-rol4="usuariosRol4" />

      <div class="w-full px-3">
        <Header :auth="auth" />

        <div class="contenido max-h-[90vh] w-full overflow-auto scrollbar-custom">
          <div class="banner w-full min-h-[150px] rounded-lg" :class="[bgOpacity]"></div>
          <div class="flex items-end justify-between encabezado-config h-[auto] py-10 mx-12">
            <div class="left-foto -mt-[120px] flex items-end gap-4">
              <div
                class="grid place-content-center foto w-[250px] h-[250px] rounded-[60px] bg-secundary-opacity backdrop-blur-lg">
                <div class="p-2 w-[210px] h-[210px] rounded-[50px] grid place-content-center" :class="[bgClase]">
                  <p class="text-[45px] font-semibold">{{ inicialesNombreUsuario }}</p>
                </div>
              </div>

              <div class="nombre">
                <h3 class="font-semibold text-[30px]">{{ user.nombres_ct }} {{
                  user.apellidos_ct }}</h3>

                <div class="flex items-center justify-between">
                  <p class="-mt-[8px] text-secundary-light text-[16px] font-medium">
                    {{ user.rol?.tipo_rol || 'Sin rol' }}
                  </p>
                  <div class="flex items-center gap-1 shadowM bg-universal-azul w-[auto] py-1 px-2 rounded-md">{{
                    user.tienda?.aplicacion?.membresia?.nombre_membresia }} <span
                      class="material-symbols-rounded text-[18px]">bolt</span></div>
                </div>


                <div class="botonesConfig my-3 flex gap-2 items-center">
                  <button class="px-4 py-2 rounded-md" :class="bgClase">Editar perfil</button>
                  <button @click="logout"
                    class="opcion flex items-center gap-2 justify-center border cursor-pointer py-2 px-4 rounded-md">
                    <p>Cerrar sesión</p>
                  </button>
                </div>
              </div>

            </div>

            <div class="right-info">
              <div class="datos-recurentes flex items-center gap-10">
                <div class="dias-restante w-auto rounded-md">
                  <h4 class="">Restantes</h4>
                  <p class="text-[40px] font-semibold -mt-3">000 <span class="text-[14px]">Días</span></p>
                </div>
                <div class="dias-activo w-auto rounded-md">
                  <h4 class="">Activo </h4>
                  <p class="text-[40px] font-semibold -mt-3">000 <span class="text-[14px]">Días</span></p>
                </div>
              </div>
            </div>
          </div>

          <div class="w-full px-12 ">
            <div class="mb-6">
              <nav class="-mb-px flex space-x-4">
                <button v-for="(tab, index) in tabs" :key="index" @click="activeTab = index" :class="[
                  'text-md font-medium px-4 py-2',
                  activeTab === index
                    ? textoClase + ' ' + borderClase
                    : 'text-secundary-light ' + hover
                ]">
                  {{ tab.label }}
                </button>
              </nav>
            </div>

            <div v-if="activeTab === 0">
              <h2 class="text-2xl font-bold mb-4">Datos personales</h2>
              <div class="flex w-full justify-between gap-10">
                <div class="left-table w-[50%] flex flex-col gap-1">
                  <div class="id-usuario">
                    <label for="id-usuario" class="text-[14px] text-secundary-light">ID usuario:</label>
                    <p id="id-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">badge</span>
                      {{ user.id || 'Sin Id' }}
                    </p>
                  </div>

                  <div class="estado-usuario">
                    <label for="estado-usuario" class="text-[14px] text-secundary-light">Estado usuario:</label>
                    <p id="estado-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                    <div class="h-3 w-4 rounded-full" :class="getEstadoClass(user.estado?.tipo_estado)"></div>
                    {{ user.estado?.tipo_estado || 'Sin estado' }}
                    </p>
                  </div>

                  <div class="rol-usuario">
                    <label for="rol-usuario" class="text-[14px] text-secundary-light">Rol usuario:</label>
                    <p id="rol-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">local_police</span>
                      {{ user.rol?.tipo_rol || 'Sin rol' }}
                    </p>
                  </div>

                  <div class="tienda-usuario">
                    <label for="tienda-usuario" class="text-[14px] text-secundary-light">Tienda asignada:</label>
                    <p id="tienda-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">store</span>
                      {{ user.tienda?.nombre_tienda || 'Sin tienda' }}
                    </p>
                  </div>

                  <div class="fecha-creacion-usuario">
                    <label for="fecha-creacion-usuario" class="text-[14px] text-secundary-light">Fecha de
                      creación:</label>
                    <p id="fecha-creacion-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">calendar_month</span>
                      {{ formatFecha(user.fecha_creacion) || 'Sin fecha' }}
                    </p>
                  </div>

                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-usuario">
                    <label for="nombre-usuario" class="text-[14px] text-secundary-light">Nombre usuario:</label>
                    <p id="nombre-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">person</span>
                      {{ user.nombres_ct || 'Sin nombre' }} {{ user.apellidos_ct || 'Sin apellido' }}
                    </p>
                  </div>

                  <div class="documento-usuario">
                    <label for="documento-usuario" class="text-[14px] text-secundary-light">Documento usuario:</label>
                    <p id="documento-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">description</span>
                      {{ user.tipo_documento?.documento_legal || 'Sin documento' }} - {{
                        user.numero_documento_ct || 'Sin número' }}
                    </p>
                  </div>

                  <div class="email usuario">
                    <label for="email-usuario" class="text-[14px] text-secundary-light">Email usuario:</label>
                    <p id="email-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">email</span>
                      {{ user.email_ct || 'Sin email' }}
                    </p>
                  </div>

                  <div class="telefono-usuario">
                    <label for="telefono-usuario" class="text-[14px] text-secundary-light">Teléfono usuario:</label>
                    <p id="telefono-usuario" class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">phone</span>
                      {{ user.telefono_ct || 'Sin teléfono' }}
                    </p>
                  </div>

                  <div class="fecha-modificacion-usuario">
                    <label for="fecha-modificacion-usuario" class="text-[14px] text-secundary-light">Fecha de
                      modificación:</label>
                    <p id="fecha-modificacion-usuario"
                      class="flex items-center gap-1.5 border px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">update</span>
                      {{ formatFecha(user.fecha_modificacion) || 'Sin fecha' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 1">
              <h2 class="text-2xl font-bold mb-4">Datos de la Tienda</h2>
              <div class="flex justify-between">
                <div class="left-table">
                  <p><strong>ID:</strong> {{ user.tienda?.id }}</p>
                  <p class="flex items-center gap-1.5">
                  <div class="h-3 w-4 rounded-full" :class="getEstadoClass(user.tienda?.estado?.tipo_estado)"></div> {{
                    user.tienda?.estado?.tipo_estado || 'Sin estado' }}</p>
                  <p><strong>Token:</strong> {{ user.tienda?.token?.token_activacion }}</p>
                  <p><strong>Nombre:</strong> {{ user.tienda?.nombre_tienda }}</p>
                  <p><strong>Dirección:</strong> {{ user.tienda?.direccion_tienda }}</p>
                </div>
                <div class="right-table">
                  <p><strong>Email:</strong> {{ user.tienda?.email_tienda }}</p>
                  <p><strong>Teléfono:</strong> {{ user.tienda?.telefono_tienda }}</p>
                  <p><strong>Fecha de creación:</strong> {{ formatFecha(user.tienda?.fecha_creacion) }}</p>
                  <p><strong>Fecha de modificación:</strong> {{ formatFecha(user.tienda?.fecha_modificacion) }}</p>
                  <p><strong>Ciudad:</strong> {{ user.tienda?.barrio_tienda }}</p>
                </div>
              </div>


            </div>

            <div v-else-if="activeTab === 2">
              <h2 class="text-2xl font-bold mb-4">Detalles del Plan</h2>
              <p><strong>ID:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.id }}</p>
              <p><strong>Sucursales:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.cantidad_sucursales }}</p>
              <p><strong>Empleados:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.cantidad_empleados }}</p>
              <p><strong>Proveedores:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.cantidad_proveedores }}</p>
              <p><strong>Facturación electrónica:</strong> {{
                user.tienda?.aplicacion?.plan?.detalles?.cantidad_facturacion_electronica }}</p>
              <p><strong>Facturación física:</strong> {{
                user.tienda?.aplicacion?.plan?.detalles?.cantidad_facturacion_fisica }}</p>
              <p><strong>Productos:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.cantidad_productos }}</p>
              <p><strong>Servicios:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.cantidad_servicios }}</p>
              <p><strong>Manejo reservas:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.manejo_reservas }}</p>
              <p><strong>Manejo POS:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.manejo_pos }}</p>
              <p><strong>Manejo de gastos:</strong> {{ user.tienda?.aplicacion?.plan?.detalles?.manejo_control_gastos }}
              </p>
              <p><strong>Fecha creación:</strong> {{
                formatFecha(user.tienda?.aplicacion?.plan?.detalles?.fecha_creacion) }}</p>
              <p><strong>Fecha modificación:</strong> {{
                formatFecha(user.tienda?.aplicacion?.plan?.detalles?.fecha_modificacion) }}</p>
            </div>

            <div v-else-if="activeTab === 3">
              <h2 class="text-2xl font-bold mb-4">Datos de la Membresía</h2>
              <p><strong>ID:</strong> {{ user.tienda?.aplicacion?.membresia?.id }}</p>
              <p><strong>Nombre:</strong> {{ user.tienda?.aplicacion?.membresia?.nombre_membresia }}</p>
              <p><strong>Precio:</strong> {{ user.tienda?.aplicacion?.membresia?.precio }} USD</p>
              <p><strong>Periodo:</strong> {{ user.tienda?.aplicacion?.membresia?.periodo }}</p>
              <p><strong>Duración:</strong> {{ user.tienda?.aplicacion?.membresia?.duracion }} días</p>
              <p class="flex items-center gap-1.5">
              <div class="h-3 w-4 rounded-full"
                :class="getEstadoClass(user.tienda?.aplicacion?.membresia?.estado?.tipo_estado)"></div> {{
                  user.tienda?.aplicacion?.membresia?.estado?.tipo_estado }}</p>
              <p><strong>Descripción:</strong> {{ user.tienda?.aplicacion?.membresia?.descripcion }}</p>
            </div>

            <div v-else-if="activeTab === 4">
              <h2 class="text-2xl font-bold mb-4">Datos del Token</h2>
              <p><strong>ID:</strong> {{ user.tienda?.token?.id }}</p>
              <p class="flex items-center gap-1.5">
              <div class="h-3 w-4 rounded-full" :class="getEstadoClass(user.tienda?.token?.estado?.tipo_estado)"></div>
              {{ user.tienda?.token?.estado?.tipo_estado }}</p>
              <p><strong>Tienda:</strong> {{ user.tienda?.nombre_tienda }}</p>
              <p><strong>Token Activación:</strong> {{ user.tienda?.token?.token_activacion }}</p>
              <p><strong>Fecha creación:</strong> {{ formatFecha(user.tienda?.token?.fecha_creacion) }}</p>
              <p><strong>Fecha modificación:</strong> {{ formatFecha(user.tienda?.token?.fecha_modificacion) }}</p>
            </div>

            <div v-else-if="activeTab === 5">
              <h2 class="text-2xl font-bold mb-4">Configuraciones Avanzadas</h2>
              <p>Esta sección está reservada para configuraciones avanzadas que no están disponibles en la interfaz
                principal.</p>
              <p>Próximamente...</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>


</template>
