<script setup>

import { Head, usePage, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { defineProps, computed, ref, onUnmounted, onMounted } from 'vue';
import SidebarSuperAdmin from '@/Components/Sidebar/FixnologyCO/Sidebar.vue';
import Header from '@/Components/header/Header.vue';
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/es' // ✅ Importa el idioma español
import Colors from '@/Composables/ModularColores';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';

const { appName, bgClase, bgOpacity, focus, textoClase, borderClase, buttonFocus, hover } = Colors();

dayjs.locale('es')
dayjs.extend(relativeTime)

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
  foto_base64: String,
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


const tienda = page.props.tienda ?? {}
const membresia = tienda.aplicacion?.membresia ?? {}

const tiempoActivo = ref('')

const calcularTiempo = () => {
  if (!user.fecha_creacion || !dayjs(user.fecha_creacion).isValid()) {
    tiempoActivo.value = 'Sin fecha'
    return
  }

  const fechaCreacion = dayjs(user.fecha_creacion)
  tiempoActivo.value = fechaCreacion.fromNow() // Ej: "hace 2 horas"
}

let intervaloRestante = null
let intervalo = null

onMounted(() => {
  calcularTiempo()

  // Recalcula cada 60 segundos
  intervalo = setInterval(() => {
    calcularTiempo()
  }, 60000)

  calcularDiasRestantes()
  // actualiza cada 24h
  intervaloRestante = setInterval(() => {
    calcularDiasRestantes()
  }, 86400000)
})

onUnmounted(() => {
  clearInterval(intervalo),
    clearInterval(intervaloRestante)
})

const diasRestantes = ref(0)

const calcularDiasRestantes = () => {
  const fechaActivacion = user.tienda?.pagos_membresia?.fecha_activacion
  const duracion = user.tienda?.aplicacion?.membresia?.duracion

  if (!fechaActivacion || !duracion) {
    diasRestantes.value = 0
    return
  }

  const activacion = dayjs(fechaActivacion)
  const hoy = dayjs()
  const diasTranscurridos = hoy.diff(activacion, 'day')
  const restantes = duracion - diasTranscurridos

  diasRestantes.value = restantes > 0 ? restantes : 0
}


const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol


const activeTab = ref(0)

const tabs = [
  { label: 'Cliente' },
  { label: 'Tienda' },
  { label: 'Plan' },
  { label: 'Membresía' },
  { label: 'Ajustes avanzados' }
]

function getEstadoClass(estado) {
  if (estado === 'Inactivo' || estado === 'Pendiente') return 'bg-semaforo-rojo shadow-rojo';
  if (estado === 'Suspendido') return 'bg-semaforo-amarillo shadow-amarillo';
  if (estado === 'Activo' || estado === 'Pagada') return 'bg-semaforo-verde shadow-verde';
  return '';
}


const onFileChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('foto', file)

  router.post(route('usuario.actualizar.foto'), formData, {
    preserveScroll: true,
    preserveState: true,
  })
}


</script>

<template>
  <div>

    <Head title="Configuraciones" />
    <MensajesLayout />


    <div class="flex">
      <SidebarSuperAdmin :auth="auth" />

      <div class="w-full px-3">
        <Header :auth="auth" :foto_base64="foto_base64"/>

        <div class="contenido max-h-[90vh] w-full overflow-auto scrollbar-custom">
          <div class="banner w-full min-h-[150px] rounded-lg" :class="[bgOpacity]"></div>
          <div class="flex items-end justify-between encabezado-config h-[auto] py-10 mx-12">
            <div class="left-foto -mt-[120px] flex items-end gap-4">
              <div
                class="grid place-content-center foto w-[250px] h-[250px] rounded-[60px] bg-secundary-opacity backdrop-blur-lg">
                <template v-if="foto_base64">
                  <div class="relative w-[220px] h-[220px] group">
                   
                    <img v-if="foto_base64" :src="foto_base64"
                      class="rounded-[50px] w-full h-full object-cover shadow-lg" />
                  </div>
                </template>



                <template v-else>
                  <div class="p-2 w-[220px] h-[220px] rounded-[50px] grid place-content-center" :class="[bgClase]">
                    <p class="text-[45px] font-semibold">{{ inicialesNombreUsuario }}</p>
                  </div>
                </template>
              </div>


              <div class="nombre">
                <h3 class="font-semibold text-[30px]">{{ user.nombres_ct }} {{
                  user.apellidos_ct }}</h3>

                <div class="flex items-center justify-between">
                  <p id="rol-usuario" class="flex items-center gap-1.5 py-1">
                    <span class="material-symbols-rounded text-[20px] text-universal-azul">local_police</span>
                    {{ user.rol?.tipo_rol || 'Sin rol' }}
                  </p>
                  <div class="flex items-center gap-1 shadowM bg-universal-azul w-[auto] py-1 px-2 rounded-md">{{
                    user.tienda?.aplicacion?.membresia?.nombre_membresia }} <span
                      class="material-symbols-rounded text-[18px]">bolt</span></div>
                </div>


                <div class="botonesConfig my-3 flex gap-2 items-center">
                  <a :href="route('aplicacion.configuraciones.editarMiPerfil', { aplicacion, rol })">
                    <button class="px-4 py-2 rounded-md" :class="bgClase">Editar perfil</button>
                  </a>
                  <button @click="logout"
                    class="opcion flex items-center gap-2 justify-center border border-secundary-light cursor-pointer py-2 px-4 rounded-md">
                    <p>Cerrar sesión</p>
                  </button>
                </div>
              </div>

            </div>

            <div class="right-info">

              <div class="datos-recurentes flex items-end flex-col gap-2">
                <div class="dias-restante text-right w-auto rounded-md">
                  <h4 class="">Tu membresía finaliza en:</h4>
                  <p class="text-[35px] font-semibold -mt-3">{{ diasRestantes }}<span class="text-[14px]">Días</span>
                  </p>
                </div>
                <div class="dias-activo w-auto rounded-md">
                  <h4 class="text-right">Te uniste a la familia: </h4>
                  <p class="text-[35px] font-semibold -mt-3">{{ tiempoActivo }} <span class="text-[14px]"></span></p>
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
                    <p id="id-usuario" class="flex items-center gap-1.5 border border-secundary-light secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">badge</span>
                      {{ user.id || 'Sin Id' }}
                    </p>
                  </div>

                  <div class="estado-usuario">
                    <label for="estado-usuario" class="text-[14px] text-secundary-light">Estado usuario:</label>
                    <p id="estado-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                    <div class="h-3 w-4 rounded-full" :class="getEstadoClass(user.estado?.tipo_estado)"></div>
                    {{ user.estado?.tipo_estado || 'Sin estado' }}
                    </p>
                  </div>

                  <div class="rol-usuario">
                    <label for="rol-usuario" class="text-[14px] text-secundary-light">Rol usuario:</label>
                    <p id="rol-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">local_police</span>
                      {{ user.rol?.tipo_rol || 'Sin rol' }}
                    </p>
                  </div>

                  <div class="tienda-usuario">
                    <label for="tienda-usuario" class="text-[14px] text-secundary-light">Tienda asignada:</label>
                    <p id="tienda-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">store</span>
                      {{ user.tienda?.nombre_tienda || 'Sin tienda' }}
                    </p>
                  </div>

                  <div class="fecha-creacion-usuario">
                    <label for="fecha-creacion-usuario" class="text-[14px] text-secundary-light">Fecha de
                      creación:</label>
                    <p id="fecha-creacion-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">calendar_month</span>
                      {{ formatFecha(user.fecha_creacion) || 'Sin fecha' }}
                    </p>
                  </div>

                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-usuario">
                    <label for="nombre-usuario" class="text-[14px] text-secundary-light">Nombre usuario:</label>
                    <p id="nombre-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">person</span>
                      {{ user.nombres_ct || 'Sin nombre' }} {{ user.apellidos_ct || 'Sin apellido' }}
                    </p>
                  </div>

                  <div class="documento-usuario">
                    <label for="documento-usuario" class="text-[14px] text-secundary-light">Documento usuario:</label>
                    <p id="documento-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">description</span>
                      {{ user.tipo_documento?.documento_legal || 'Sin documento' }} - {{
                        user.numero_documento_ct || 'Sin número' }}
                    </p>
                  </div>

                  <div class="email usuario">
                    <label for="email-usuario" class="text-[14px] text-secundary-light">Email usuario:</label>
                    <p id="email-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">email</span>
                      {{ user.email_ct || 'Sin email' }}
                    </p>
                  </div>

                  <div class="telefono-usuario">
                    <label for="telefono-usuario" class="text-[14px] text-secundary-light">Teléfono usuario:</label>
                    <p id="telefono-usuario" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">phone</span>
                      {{ user.telefono_ct || 'Sin teléfono' }}
                    </p>
                  </div>

                  <div class="fecha-modificacion-usuario">
                    <label for="fecha-modificacion-usuario" class="text-[14px] text-secundary-light">Fecha de
                      modificación:</label>
                    <p id="fecha-modificacion-usuario"
                      class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">update</span>
                      {{ formatFecha(user.fecha_modificacion) || 'Sin fecha' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 1">
              <h2 class="text-2xl font-bold mb-4">Datos mi tienda</h2>
              <div class="flex justify-between w-full gap-10">
                <div class="left-table w-[50%] flex flex-col gap-1">
                  <div class="id-tienda">
                    <label for="id-tienda" class="text-[14px] text-secundary-light">ID tienda:</label>
                    <p id="id-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">store</span>
                      {{ user.tienda?.id || 'Sin ID' }}
                    </p>
                  </div>

                  <div class="estado-tienda">
                    <label for="estado-tienda" class="text-[14px] text-secundary-light">Estado tienda:</label>
                    <p id="estado-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                    <div class="h-3 w-4 rounded-full" :class="getEstadoClass(user.tienda?.estado?.tipo_estado)"></div>
                    {{ user.tienda?.estado?.tipo_estado || 'Sin estado' }}
                    </p>
                  </div>

                  <div class="token-tienda">
                    <label for="id-tienda" class="text-[14px] text-secundary-light">Token asignado:</label>
                    <p id="id-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">key</span>
                      {{ user.tienda?.token?.token_activacion || 'Sin ID' }}
                    </p>
                  </div>
                  <div class="fecha-creacion">
                    <label for="fecha-creacion-tienda" class="text-[14px] text-secundary-light">Fecha de
                      creación:</label>
                    <p id="fecha-creacion-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">calendar_month</span>
                      {{ formatFecha(user.tienda?.fecha_creacion) || 'Sin fecha' }}
                    </p>
                  </div>

                  <div class="fecha-modificacion-tienda">
                    <label for="fecha-modificacion-tienda" class="text-[14px] text-secundary-light">Fecha de
                      modificación:</label>
                    <p id="fecha-modificacion-tienda"
                      class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">update</span>
                      {{ formatFecha(user.tienda?.fecha_modificacion) || 'Sin fecha' }}
                    </p>
                  </div>

                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-tienda">
                    <label for="nombre-tienda" class="text-[14px] text-secundary-light">Nombre tienda:</label>
                    <p id="nombre-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">store</span>
                      {{ user.tienda?.nombre_tienda || 'Sin nombre' }}
                    </p>
                  </div>

                  <div class="ciudad-tienda">
                    <label for="ciudad-tienda" class="text-[14px] text-secundary-light">Ubicación tienda:</label>
                    <p id="ciudad-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">location_city</span>
                      {{ user.tienda?.barrio_tienda }}, {{ user.tienda?.direccion_tienda }}
                    </p>
                  </div>

                  <div class="email-tienda">
                    <label for="email-tienda" class="text-[14px] text-secundary-light">Email tienda:</label>
                    <p id="email-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">email</span>
                      {{ user.tienda?.email_tienda || 'Sin email' }}
                    </p>
                  </div>

                  <div class="telefono-tienda">
                    <label for="telefono-tienda" class="text-[14px] text-secundary-light">Teléfono tienda:</label>
                    <p id="telefono-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">phone</span>
                      {{ user.tienda?.telefono_tienda || 'Sin teléfono' }}
                    </p>
                  </div>
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
              <h2 class="text-2xl font-bold mb-4">Datos membresía activa </h2>
              <div class="flex justify-between w-full gap-10">
                <div class="left-table w-[50%] flex flex-col gap-1">
                  <div class="id-membresia">
                    <label for="id-membresia" class="text-[14px] text-secundary-light">ID membresía:</label>
                    <p id="id-membresia" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">card_membership</span>
                      {{ user.tienda?.aplicacion?.membresia?.id || 'Sin ID' }}
                    </p>
                  </div>

                  <div class="estado-membresia">
                    <div class="estado-tienda">
                      <label for="estado-tienda" class="text-[14px] text-secundary-light">Estado tienda:</label>
                      <p id="estado-tienda" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <div class="h-3 w-4 rounded-full" :class="getEstadoClass(user.tienda?.estado?.tipo_estado)"></div>
                      {{ user.tienda?.aplicacion?.membresia?.estado?.tipo_estado || 'Sin estado' }}
                      </p>
                    </div>
                  </div>

                  <div class="precio-membresia">
                    <label for="precio-membresia" class="text-[14px] text-secundary-light">Precio membresía:</label>
                    <p id="precio-membresia" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">attach_money</span>
                      {{ formatCOP(user.tienda?.aplicacion?.membresia?.precio) || 'Sin precio' }}
                    </p>
                  </div>

                  <div class="duracion-membresia">
                    <label for="duracion-membresia" class="text-[14px] text-secundary-light">Duración
                      membresía:</label>
                    <p id="duracion-membresia" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">timer</span>
                      {{ user.tienda?.aplicacion?.membresia?.duracion || 'Sin duración' }} días
                    </p>
                  </div>
                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-membresia">
                    <label for="nombre-membresia" class="text-[14px] text-secundary-light">
                      Nombre membresía:
                    </label>
                    <p id="nombre-membresia" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">card_membership</span>
                      {{ user.tienda?.aplicacion?.membresia?.nombre_membresia || 'Sin nombre' }}
                    </p>
                  </div>

                  <div class="perioro-membresia">
                    <label for="periodo-membresia" class="text-[14px] text-secundary-light">Periodo
                      membresía:</label>
                    <p id="periodo-membresia" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">date_range</span>
                      {{ user.tienda?.aplicacion?.membresia?.periodo || 'Sin periodo' }}
                    </p>
                  </div>

                  <div class="descripcion-membresia">
                    <label for="descripcion-membresia" class="text-[14px] text-secundary-light">Descripción
                      membresía:</label>
                    <p id="descripcion-membresia" class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">description</span>
                      {{ user.tienda?.aplicacion?.membresia?.descripcion || 'Sin descripción' }}
                    </p>
                  </div>

                  <div class="fecha-creacion-membresia">
                    <label for="fecha-creacion-membresia" class="text-[14px] text-secundary-light">Fecha de
                      activación:</label>
                    <p id="fecha-creacion-membresia"
                      class="flex items-center gap-1.5 border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">calendar_month</span>
                      {{ formatFecha(user.tienda?.pagos_membresia?.fecha_activacion) || 'Sin fecha' }}
                    </p>
                  </div>
                </div>
              </div>

            </div>



            <div v-else-if="activeTab === 4">
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
