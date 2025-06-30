<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import Header from "@/Components/header/Header.vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import useEstadoClass from "@/Composables/useEstado";
const { getEstadoClass } = useEstadoClass();  
import { formatFecha, formatFechaShort } from "@/utils/date";
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
  fotoUser: {
    type: String,
    default: "",
  },
  aplicacion: {
    type: String,
    default: "",
  },
  errors: {
    type: Object,
    required: true,
  },
  foto_base64: {
    type: String,
    default: "",
  },
  detallesCliente: {
    type: Object,
    default: "",
  },
});

const page = usePage();

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || "Sin app";
const rol = props.auth.user.rol?.tipo_rol || "Sin rol"; // Obtén el tipo de rol

function obtenerIniciales(nombre, apellido) {
  const inicial1 = nombre?.charAt(0) || "";
  const inicial2 = apellido?.charAt(0) || "";
  return (inicial1 + inicial2).toUpperCase();
}

const activeTab = ref(0);
const tabs = [
  { label: "Información personal" },
  { label: "Información tienda" },
  { label: "Información membresía" },
  { label: "Información del plan" },
  { label: "Ajustes avanzados" },
];

const hoverColor = props.detallesCliente.tienda.aplicacion.color_hover || "bg-secundary-light";
const bgColor = props.detallesCliente.tienda.aplicacion.color_fondo || "bg-secundary-light";
const textColor = props.detallesCliente.tienda.aplicacion.color_texto || "text-mono-blanco";
const shadowColor = props.detallesCliente.tienda.aplicacion.color_shadow || "shadowM";
const borderColor = props.detallesCliente.tienda.aplicacion.color_border;
</script>

<template>
  <Head :title="`${detallesCliente.nombres_ct || 'No encontrado'}`" />

  <MensajesLayout />

  <div class="flex">
    <SidebarSuperAdmin :auth="auth" />

    <div class="w-full">
      <Header :auth="auth" :foto_base64="foto_base64" />

      <div class="contenido p-3 max-h-[90vh] w-full overflow-auto scrollbar-custom">
        <!-- navegable -->
        <div class="options flex gap-1 items-center text-[14px]">
          <a
            :href="route('aplicacion.GestorUsuarios', { aplicacion, rol })"
            class="text-mono-negro dark:text-mono-blanco dark:hover:text-universal-azul"
            :class="hoverTexto"
          >
            <p>Usuarios</p>
          </a>
          <span
            class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco"
            >chevron_right</span
          >
          <p class="font-bold text-mono-negro dark:text-mono-blanco">
            {{ detallesCliente.nombres_ct }} {{ detallesCliente.apellidos_ct }}
          </p>
        </div>

       
        <div class="contenido my-2">
          <div class="banner dark:opacity-20 opacity-70 w-full min-h-[150px] rounded-lg" :class="bgColor" ></div>
          <div class="flex items-end justify-between encabezado-config h-[auto] py-10 mx-12">
            <div class="left-foto -mt-[120px] flex items-end gap-4">
              <div
                class="grid place-content-center foto w-[230px] h-[230px] rounded-[55px] bg-secundary-opacity dark:bg-mono-blanco backdrop-blur-lg">
                <template v-if="fotoUser">
                  <div class="relative w-[220px] h-[220px] group">

                    <img v-if="fotoUser" :src="fotoUser"
                      class="rounded-[50px] w-full h-full object-cover shadow-lg" />
                  </div>
                </template>

                <template v-else>
                  <div class="p-2 w-[220px] h-[220px] rounded-[50px] grid place-content-center" :class="bgColor">
                    <p class="text-[45px] font-semibold">{{
                    obtenerIniciales(
                      detallesCliente.nombres_ct,
                      detallesCliente.apellidos_ct
                    )
                  }}</p>
                  </div>
                </template>
              </div>


              <div class="nombre">
                <h3 class="font-semibold text-[30px] text-mono-negro dark:text-mono-blanco">{{ detallesCliente.nombres_ct }} {{
                  detallesCliente.apellidos_ct }}</h3>

                <div class="flex items-center justify-between min-w-[300px] max-w-[450px]">
                  <p id="rol-usuario"
                    class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco py-1">
                   <div
                      class="p-1 font-semibold flex items-center rounded-[5px] text-mono-negro shadow-[0px 8px 20px bg-amber-400]"
                      :class="[bgColor]"
                    >
                      <span class="material-symbols-rounded text-[20px]">{{
                        detallesCliente.tienda.aplicacion.icono_app
                      }}</span>
                    </div>
                    {{ detallesCliente.tienda.aplicacion.nombre_app }}
                  </p>
                  <div
                    class="flex items-center gap-1 shadowM text-mono-blanco bg-universal-azul w-[auto] py-1 px-2 rounded-md">
                    {{
                      detallesCliente.tienda?.aplicacion?.membresia?.nombre_membresia }} <span
                      class="material-symbols-rounded text-[18px]">bolt</span></div>
                </div>


                <div class="botonesConfig my-3 flex gap-2 items-center">
                  <a :href="route('aplicacion.configuraciones.editarMiPerfil', { aplicacion, rol })">
                    <button class="rounded-md py-2 px-4 flex items-center justify-center gap-2 text-mono-negro" :class="[bgColor]">Editar perfil</button>
                  </a>
                  
                </div>
              </div>

            </div>

            <div class="right-info">

              <div class="datos-recurentes flex items-end flex-col gap-2">
                <div class="dias-restante text-right w-auto rounded-md">
                  <h4 class="text-secundary-default dark:text-mono-blanco ">La membresía finaliza en:</h4>
                  <p class="text-[35px] font-semibold -mt-3 text-secundary-default dark:text-mono-blanco">{{
                    diasRestantes }}<span class="text-[14px] text-secundary-default dark:text-mono-blanco">Días</span>
                  </p>
                </div>
                <div class="dias-activo w-auto rounded-md ">
                  <h4 class="text-right text-secundary-default dark:text-mono-blanco">Se unió a la familia: </h4>
                  <p class="text-[35px] font-semibold -mt-3 text-secundary-default dark:text-mono-blanco">{{
                    tiempoActivo }} <span class="text-[14px]"></span></p>
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
                    ? textColor + ' ' + borderColor
                    : 'text-secundary-default dark:text-secundary-light ' + ' dark:' + hoverColor + ' ' + hoverColor
                ]">
                  {{ tab.label }}
                </button>
              </nav>
            </div>

            <div v-if="activeTab === 0">
              <h2 class="text-2xl font-bold mb-4 text-secundary-default dark:text-mono-blanco">Datos personales</h2>
              <div class="flex w-full justify-between gap-10">
                <div class="left-table w-[50%] flex flex-col gap-1">
                  <div class="id-usuario">
                    <label for="id-usuario" class="text-[14px] text-secundary-default dark:text-secundary-light">ID
                      usuario:</label>
                    <p id="id-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">badge</span>
                      {{ detallesCliente.id || 'Sin Id' }}
                    </p>
                  </div>

                  <div class="estado-usuario">
                    <label for="estado-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Estado
                      usuario:</label>
                    <p id="estado-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                    <div class="h-3 w-4 rounded-full" :class="getEstadoClass(detallesCliente.estado?.tipo_estado)"></div>
                    {{ detallesCliente.estado?.tipo_estado || 'Sin estado' }}
                    </p>
                  </div>

                  <div class="rol-usuario">
                    <label for="rol-usuario" class="text-[14px] text-secundary-default dark:text-secundary-light">Rol
                      usuario:</label>
                    <p id="rol-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">local_police</span>
                      {{ detallesCliente.rol?.tipo_rol || 'Sin rol' }}
                    </p>
                  </div>

                  <div class="tienda-usuario">
                    <label for="tienda-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Tienda
                      asignada:</label>
                    <p id="tienda-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">store</span>
                      {{ detallesCliente.tienda?.nombre_tienda || 'Sin tienda' }}
                    </p>
                  </div>

                  <div class="fecha-creacion-usuario">
                    <label for="fecha-creacion-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Fecha
                      de
                      creación:</label>
                    <p id="fecha-creacion-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">calendar_month</span>
                      {{ formatFecha(detallesCliente.fecha_creacion) || 'Sin fecha' }}
                    </p>
                  </div>

                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-usuario">
                    <label for="nombre-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Nombre
                      usuario:</label>
                    <p id="nombre-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">person</span>
                      {{ detallesCliente.nombres_ct || 'Sin nombre' }} {{ detallesCliente.apellidos_ct || 'Sin apellido' }}
                    </p>
                  </div>

                  <div class="documento-usuario">
                    <label for="documento-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Documento
                      usuario:</label>
                    <p id="documento-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">description</span>
                      {{ detallesCliente.tipo_documento?.documento_legal || 'Sin documento' }} - {{
                        detallesCliente.numero_documento_ct || 'Sin número' }}
                    </p>
                  </div>

                  <div class="email usuario">
                    <label for="email-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Email
                      usuario:</label>
                    <p id="email-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">email</span>
                      {{ detallesCliente.email_ct || 'Sin email' }}
                    </p>
                  </div>

                  <div class="telefono-usuario">
                    <label for="telefono-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Teléfono
                      usuario:</label>
                    <p id="telefono-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">phone</span>
                      {{ detallesCliente.telefono_ct || 'Sin teléfono' }}
                    </p>
                  </div>

                  <div class="fecha-modificacion-usuario">
                    <label for="fecha-modificacion-usuario"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Fecha de
                      modificación:</label>
                    <p id="fecha-modificacion-usuario"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">update</span>
                      {{ formatFecha(detallesCliente.fecha_modificacion) || 'Sin fecha' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 1">
              <h2 class="text-2xl font-bold mb-4 text-secundary-default dark:text-mono-blanco">Datos mi tienda</h2>
              <div class="flex justify-between w-full gap-10">
                <div class="left-table w-[50%] flex flex-col gap-1">
                  <div class="id-tienda">
                    <label for="id-tienda" class="text-[14px] text-secundary-default dark:text-secundary-light">ID
                      tienda:</label>
                    <p id="id-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">store</span>
                      {{ detallesCliente.tienda?.id || 'Sin ID' }}
                    </p>
                  </div>

                  <div class="estado-tienda">
                    <label for="estado-tienda"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Estado
                      tienda:</label>
                    <p id="estado-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                    <div class="h-3 w-4 rounded-full" :class="getEstadoClass(detallesCliente.tienda?.estado?.tipo_estado)"></div>
                    {{ detallesCliente.tienda?.estado?.tipo_estado || 'Sin estado' }}
                    </p>
                  </div>

                  <div class="token-tienda">
                    <label for="id-tienda" class="text-[14px] text-secundary-default dark:text-secundary-light">Token
                      asignado:</label>
                    <p id="id-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">key</span>
                      {{ detallesCliente.tienda?.token?.token_activacion || 'Sin ID' }}
                    </p>
                  </div>
                  <div class="fecha-creacion">
                    <label for="fecha-creacion-tienda"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Fecha
                      de
                      creación:</label>
                    <p id="fecha-creacion-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">calendar_month</span>
                      {{ formatFecha(detallesCliente.tienda?.fecha_creacion) || 'Sin fecha' }}
                    </p>
                  </div>

                  <div class="fecha-modificacion-tienda">
                    <label for="fecha-modificacion-tienda"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Fecha de
                      modificación:</label>
                    <p id="fecha-modificacion-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">update</span>
                      {{ formatFecha(detallesCliente.tienda?.fecha_modificacion) || 'Sin fecha' }}
                    </p>
                  </div>

                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-tienda">
                    <label for="nombre-tienda"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Nombre
                      tienda:</label>
                    <p id="nombre-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">store</span>
                      {{ detallesCliente.tienda?.nombre_tienda || 'Sin nombre' }}
                    </p>
                  </div>

                  <div class="ciudad-tienda">
                    <label for="ciudad-tienda"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Ubicación
                      tienda:</label>
                    <p id="ciudad-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">location_city</span>
                      {{ detallesCliente.tienda?.barrio_tienda }}, {{ detallesCliente.tienda?.direccion_tienda }}
                    </p>
                  </div>

                  <div class="email-tienda">
                    <label for="email-tienda" class="text-[14px] text-secundary-default dark:text-secundary-light">Email
                      tienda:</label>
                    <p id="email-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">email</span>
                      {{ detallesCliente.tienda?.email_tienda || 'Sin email' }}
                    </p>
                  </div>

                  <div class="telefono-tienda">
                    <label for="telefono-tienda"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Teléfono
                      tienda:</label>
                    <p id="telefono-tienda"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">phone</span>
                      {{ detallesCliente.tienda?.telefono_tienda || 'Sin teléfono' }}
                    </p>
                  </div>
                </div>
              </div>


            </div>

            <div v-else-if="activeTab === 2">
              <h2 class="text-2xl font-bold mb-4 text-secundary-default dark:text-mono-blanco">Detalles del Plan</h2>
              <p><strong>ID:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.id }}</p>
              <p><strong>Sucursales:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.cantidad_sucursales }}</p>
              <p><strong>Empleados:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.cantidad_empleados }}</p>
              <p><strong>Proveedores:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.cantidad_proveedores }}</p>
              <p><strong>Facturación electrónica:</strong> {{
                detallesCliente.tienda?.aplicacion?.plan?.detalles?.cantidad_facturacion_electronica }}</p>
              <p><strong>Facturación física:</strong> {{
                detallesCliente.tienda?.aplicacion?.plan?.detalles?.cantidad_facturacion_fisica }}</p>
              <p><strong>Productos:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.cantidad_productos }}</p>
              <p><strong>Servicios:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.cantidad_servicios }}</p>
              <p><strong>Manejo reservas:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.manejo_reservas }}</p>
              <p><strong>Manejo POS:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.manejo_pos }}</p>
              <p><strong>Manejo de gastos:</strong> {{ detallesCliente.tienda?.aplicacion?.plan?.detalles?.manejo_control_gastos }}
              </p>
              <p><strong>Fecha creación:</strong> {{
                formatFecha(detallesCliente.tienda?.aplicacion?.plan?.detalles?.fecha_creacion) }}</p>
              <p><strong>Fecha modificación:</strong> {{
                formatFecha(detallesCliente.tienda?.aplicacion?.plan?.detalles?.fecha_modificacion) }}</p>
            </div>

            <div v-else-if="activeTab === 3">
              <h2 class="text-2xl font-bold mb-4 text-secundary-default dark:text-mono-blanco">Datos membresía activa
              </h2>
              <div class="flex justify-between w-full gap-10">
                <div class="left-table w-[50%] flex flex-col gap-1">
                  <div class="id-membresia">
                    <label for="id-membresia" class="text-[14px] text-secundary-default dark:text-secundary-light">ID
                      membresía:</label>
                    <p id="id-membresia"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">card_membership</span>
                      {{ detallesCliente.tienda?.aplicacion?.membresia?.id || 'Sin ID' }}
                    </p>
                  </div>

                  <div class="estado-membresia">
                    <div class="estado-tienda">
                      <label for="estado-tienda"
                        class="text-[14px] text-secundary-default dark:text-secundary-light">Estado
                        tienda:</label>
                      <p id="estado-tienda"
                        class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <div class="h-3 w-4 rounded-full" :class="getEstadoClass(detallesCliente.tienda?.estado?.tipo_estado)"></div>
                      {{ detallesCliente.tienda?.aplicacion?.membresia?.estado?.tipo_estado || 'Sin estado' }}
                      </p>
                    </div>
                  </div>

                  <div class="precio-membresia">
                    <label for="precio-membresia"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Precio
                      membresía:</label>
                    <p id="precio-membresia"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">attach_money</span>
                      {{ formatFecha(detallesCliente.tienda?.aplicacion?.membresia?.precio) || 'Sin precio' }}
                    </p>
                  </div>

                  <div class="duracion-membresia">
                    <label for="duracion-membresia"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Duración
                      membresía:</label>
                    <p id="duracion-membresia"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">timer</span>
                      {{ detallesCliente.tienda?.aplicacion?.membresia?.duracion || 'Sin duración' }} días
                    </p>
                  </div>
                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-membresia">
                    <label for="nombre-membresia" class="text-[14px] text-secundary-default dark:text-secundary-light">
                      Nombre membresía:
                    </label>
                    <p id="nombre-membresia"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">card_membership</span>
                      {{ detallesCliente.tienda?.aplicacion?.membresia?.nombre_membresia || 'Sin nombre' }}
                    </p>
                  </div>

                  <div class="perioro-membresia">
                    <label for="periodo-membresia"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Periodo
                      membresía:</label>
                    <p id="periodo-membresia"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">date_range</span>
                      {{ detallesCliente.tienda?.aplicacion?.membresia?.periodo || 'Sin periodo' }}
                    </p>
                  </div>

                  <div class="descripcion-membresia">
                    <label for="descripcion-membresia"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Descripción
                      membresía:</label>
                    <p id="descripcion-membresia"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">description</span>
                      {{ detallesCliente.tienda?.aplicacion?.membresia?.descripcion || 'Sin descripción' }}
                    </p>
                  </div>

                  <div class="fecha-creacion-membresia">
                    <label for="fecha-creacion-membresia"
                      class="text-[14px] text-secundary-default dark:text-secundary-light">Fecha de
                      activación:</label>
                    <p id="fecha-creacion-membresia"
                      class="flex items-center gap-1.5 text-secundary-default dark:text-mono-blanco border border-secundary-light px-2 py-1 rounded-md w-full">
                      <span class="material-symbols-rounded text-[20px]" :class="[textoClase]">calendar_month</span>
                      {{ formatFecha(detallesCliente.tienda?.pagos_membresia?.fecha_activacion) || 'Sin fecha' }}
                    </p>
                  </div>
                </div>
              </div>

            </div>



            <div v-else-if="activeTab === 4" class="h-auto">
              <h2 class="text-2xl font-bold mb-4 text-secundary-default dark:text-mono-blanco">Configuraciones Avanzadas
              </h2>
              <a :href="route('aplicacion.historial', { aplicacion, rol })">
                <button class="mb-10 text-mono-negro dark:text-mono-blanco rounded-md px-2 py-1" :class="[bgClase]">Revisar historial de mi app</button>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
