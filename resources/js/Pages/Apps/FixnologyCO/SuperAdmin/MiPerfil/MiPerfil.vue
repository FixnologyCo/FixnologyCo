<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { defineProps, defineEmits, computed, ref } from "vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";

import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { formatFecha } from "@/Utils/date";
import { formatCOP } from "@/Utils/formateoMoneda";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useTiempo } from "@/Composables/useTiempo";
import useEstadoClass from "@/Composables/useEstado";
import { useSidebar } from "@/Composables/useSidebar";
import dayjs from "dayjs";

const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
  inheritAttrs: false,
});

const { getEstadoClass } = useEstadoClass();
const { sidebarExpandido, toggleSidebar } = useSidebar();

const page = usePage();

// 1. Creamos una propiedad computada que siempre refleje el usuario de la tienda.
//    Esto es una fuente de datos reactiva.
const usuario = computed(() => authStore.user);

// 2. Llamamos a useTiempo UNA SOLA VEZ, pasándole la fuente reactiva.
//    El composable se encargará del resto.
const { tiempoActivo, diasRestantes } = useTiempo(usuario);

const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

const inicialesNombreUsuario = computed(() => {
  const nombres = authStore.primerNombre || "";
  const apellidos = authStore.primerApellido || "";
  const firstNameInitial = nombres.split(" ")[0]?.charAt(0).toUpperCase() || "";
  const lastNameInitial = apellidos.split(" ")[0]?.charAt(0).toUpperCase() || "";
  return firstNameInitial + lastNameInitial;
});

const inicialesNombreEstablecimiento = computed(() => {
  const nombreEstablecimiento = authStore.nombreEstablecimiento || "Tienda de Ejemplo";
  const palabrasAIgnorar = ["de", "el", "la", "los", "las", "y", "a"];
  const iniciales = nombreEstablecimiento
    .split(" ")
    .filter((palabra) => !palabrasAIgnorar.includes(palabra.toLowerCase()))
    .map((palabra) => palabra[0])
    .join("");

  return iniciales.toUpperCase().slice(0, 2);
});

const activeTab = ref(0);
const tabs = [
  { label: "Cliente" },
  { label: "Tienda" },
  { label: "Plan" },
  { label: "Membresía" },
  { label: "Ajustes avanzados" },
];

// 1. Obtiene el historial de facturas (todas menos la próxima)
const historialFacturas = computed(() => {
  // Ordenamos las facturas de la más reciente a la más antigua
  return [...authStore.facturas].sort((a, b) =>
    dayjs(b.fecha_pago).diff(dayjs(a.fecha_pago))
  );
});

// 2. Calcula la información de la próxima factura
const proximaFactura = computed(() => {
  if (authStore.facturas.length === 0) {
    return null; // No hay facturas, no hay próximo cobro
  }

  // Encontramos la última factura pagada
  const ultimaFacturaPagada = historialFacturas.value.find(
    (f) => f.estado.tipo_estado === "Pagado"
  );

  if (!ultimaFacturaPagada) {
    // Si no hay ninguna pagada, podríamos mostrar la más reciente pendiente
    return historialFacturas.value[0];
  }

  // Calculamos la fecha del próximo pago
  const fechaUltimoPago = dayjs(ultimaFacturaPagada.fecha_pago);
  const proximaFechaPago = fechaUltimoPago.add(authStore.duracionMembresia, "day");

  // Creamos un objeto "virtual" para la próxima factura
  return {
    fecha_pago: proximaFechaPago.toISOString(),
    estado: { tipo_estado: "Pendiente" },
    medio_pago: ultimaFacturaPagada.medio_pago,
    monto_total: ultimaFacturaPagada.monto_total,
  };
});
</script>

<template>
  <div>
    <MensajesLayout />

    <Head title="Configuraciones" />

    <div class="flex">
      <SidebarSuperAdmin :auth="authStore">
        <div
          class="contenido px-3 max-h-[85vh] w-full overflow-auto scrollbar-custom contenido-principal"
        >
          <div class="options flex gap-1 items-center justify-center text-[14px] mb-10">
            <a
              :href="route('aplicacion.dashboard', { aplicacion, rol })"
              class="text-mono-blanco hover:text-secondary flex items-center gap-1"
            >
              <span class="material-symbols-rounded text-[16px] text-primary">home</span>
              <p>Home</p>
            </a>
            <span
              class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco"
              >chevron_right</span
            >
            <p class="font-bold text-mono-negro dark:text-mono-blanco">Mi perfil</p>
          </div>
          <!-- <h1 class="text-[30px] dark:text-mono-blanco text-mono-negro">Mi perfil</h1> -->
          <div class="miPerfil w-full">
            <div
              class="rounded-[15px] p-3 flex flex-col gap-5 div2 bg-mono-blanco_opacity dark:bg-secundary-opacity"
            >
              <div class="w-full h-[130px] rounded-md opacity-50 bg-primary"></div>

              <div class="flex justify-center flex-col items-center">
                <div
                  class="-mt-28 ml-5 mb-2 grid place-content-center foto w-[180px] h-[180px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                >
                  <template v-if="authStore.fotoUrlCompletaUsuario">
                    <div class="relative group w-[160px] h-[160px]">
                      <img
                        :src="authStore.fotoUrlCompletaUsuario"
                        class="rounded-[18px] w-full h-full object-cover shadow-lg"
                      />
                    </div>
                  </template>

                  <template v-else>
                    <div
                      class="relative flex justify-center rounded-[18px] items-center group w-[160px] h-[160px] bg-primary shadow-[0px_10px_20px] shadow-primary"
                    >
                      <p class="text-[60px] font-semibold">
                        {{ inicialesNombreUsuario }}
                      </p>
                    </div>
                  </template>
                </div>
                <div class="flex items-center justify-between gap-1">
                  <span class="material-symbols-rounded text-[18px] text-primary"
                    >crown</span
                  >
                  <h3
                    class="text-mono-negro text-[25px] font-semibold dark:text-mono-blanco"
                  >
                    {{ authStore.nombreCompleto }}
                  </h3>
                  <div
                    class="grid place-items-center"
                    v-if="authStore.google_id === null"
                  >
                    <span class="material-symbols-rounded text-[18px] text-gray-700"
                      >verified_off</span
                    >
                  </div>
                  <div class="text-secundary-default dark:text-mono-blanco" v-else>
                    <span
                      class="grid place-items-center material-symbols-rounded text-[18px] text-universal-azul_secundaria"
                      >verified</span
                    >
                  </div>
                </div>
              </div>

              <div class="infoBasica">
                <div class="number flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >key</span
                    >
                    <p class="text-[14px] text-secundary-default dark:text-mono-blanco">
                      {{ authStore.google_id || authStore.idUsuario }}
                    </p>
                  </div>

                  <div class="flex items-center gap-1">
                    <div
                      class="w-3 h-3 rounded-full"
                      :class="getEstadoClass(authStore.estadoUsuario)"
                    ></div>
                    <span class="text-secundary-default dark:text-mono-blanco">{{
                      authStore.estadoUsuario
                    }}</span>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >phone</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      {{ authStore.telefonoCompleto }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >email</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      {{ authStore.email }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >id_card</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      {{ authStore.tipoDocumento + ": " + authStore.numero_documento }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >location_chip</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      {{
                        authStore.direccionResidencia +
                        ", " +
                        authStore.barrioResidencia +
                        " " +
                        authStore.ciudadResidencia
                      }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="datos-recurentes flex justify-between mt-3 gap-2">
                <div class="dias-restante w-auto rounded-md">
                  <h4 class="text-secundary-default dark:text-mono-blanco">
                    Tu membresía finaliza en:
                  </h4>
                  <p
                    class="font-semibold text-[25px] -mt-1 text-secundary-default dark:text-mono-blanco"
                  >
                    {{ diasRestantes
                    }}<span
                      class="text-[14px] text-secundary-default dark:text-mono-blanco"
                    >
                      Días</span
                    >
                  </p>
                </div>
                <div class="dias-activo w-auto rounded-md">
                  <h4 class="text-right text-secundary-default dark:text-mono-blanco">
                    Te uniste a la familia:
                  </h4>
                  <p
                    class="font-semibold -mt-1 text-secundary-default text-[25px] dark:text-mono-blanco"
                  >
                    {{ tiempoActivo }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="rounded-[15px] p-3 div3 border border-mono-negro_opacity_medio dark:border-mono-blanco_opacity"
            >
              <div class="flex justify-between items-center">
                <h4 class="text-secundary-default dark:text-mono-blanco">
                  ¡Felicitaciones!, tu membresía es
                </h4>
                <span
                  class="material-symbols-rounded text-[16px] text-primary cursor-pointer text-secundary-default dark:text-mono-blanco dark:hover:text-universal-azul_secundaria"
                  >info</span
                >
              </div>

              <div class="px-3 flex justify-between items-center">
                <div class="">
                  <p
                    class="text-[40px] font-medium text-secundary-default dark:text-mono-blanco"
                  >
                    {{ authStore.membresia }}
                  </p>
                  <p
                    class="-mt-3 font-light text-secundary-default dark:text-mono-blanco"
                  >
                    {{ authStore.descripcionMembresia }}
                  </p>
                </div>

                <button
                  class="bg-semaforo-verde flex items-center gap-1 p-2 rounded-lg shadow-verde hover:bg-semaforo-verde_opacity hover:text-semaforo-verde"
                >
                  Actualiza tu plan
                  <span class="material-symbols-rounded text-[20px]">diamond</span>
                </button>
              </div>
            </div>
            <div
              class="rounded-[15px] div4 flex justify-between items-center dark:bg-secundary-opacity bg-mono-blanco_opacity p-5"
            >
              <h4
                class="text-[60px] font-medium text-secundary-default dark:text-mono-blanco"
              >
                Mi perfil
              </h4>
              <a :href="route('aplicacion.miPerfil.editarMiPerfil', { aplicacion, rol })">
                <button
                  class="flex items-center gap-1 p-2 rounded-lg bg-primary shadow-[0px_8px_20px] shadow-primary"
                >
                  Actualiza tus datos
                  <span class="material-symbols-rounded text-[20px]">edit</span>
                </button>
              </a>
            </div>
            <div
              class="rounded-[15px] p-3 div5 dark:bg-secundary-opacity bg-mono-blanco_opacity"
            >
              <div class="w-full h-[130px] rounded-md opacity-50 bg-primary"></div>

              <div class="flex gap-2 justify-center items-center">
                <div
                  class="-mt-20 ml-5 mb-2 grid place-content-center foto w-[180px] h-[180px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                >
                  <template v-if="authStore.fotoUrlCompletaEstablecimiento">
                    <div class="relative group w-[160px] h-[160px]">
                      <img
                        :src="authStore.fotoUrlCompletaEstablecimiento"
                        class="rounded-[18px] w-full h-full object-cover shadow-lg"
                      />
                    </div>
                  </template>

                  <template v-else>
                    <div
                      class="relative flex justify-center rounded-[18px] items-center group w-[160px] h-[160px] bg-primary shadow-[0px_8px_20px] shadow-primary"
                    >
                      <p class="text-[60px] font-semibold">
                        {{ inicialesNombreEstablecimiento }}
                      </p>
                    </div>
                  </template>
                </div>
              </div>
              <div class="flex justify-between items-center">
                <h4 class="text-secundary-default dark:text-mono-blanco">
                  Tienda vinculada
                </h4>
                <span
                  class="material-symbols-rounded text-[16px] text-primary cursor-pointer text-secundary-default dark:text-mono-blanco dark:hover:text-universal-azul_secundaria"
                  >info</span
                >
              </div>

              <div class="flex items-center justify-between gap-1">
                <span class="material-symbols-rounded text-[20px] text-primary"
                  >store</span
                >
                <h3
                  class="text-mono-negro font-semibold text-[35px] dark:text-mono-blanco"
                >
                  {{ authStore.nombreEstablecimiento }}
                </h3>
                <div class="grid place-items-center" v-if="authStore.google_id === null">
                  <span class="material-symbols-rounded text-[20px] text-gray-700"
                    >verified_off</span
                  >
                </div>
                <div class="text-secundary-default dark:text-mono-blanco" v-else>
                  <span
                    class="grid place-items-center material-symbols-rounded text-[18px] text-universal-azul_secundaria"
                    >verified</span
                  >
                </div>
              </div>
              <div class="number flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-rounded text-[16px] text-primary"
                    >key</span
                  >
                  <p class="text-[14px] text-secundary-default dark:text-mono-blanco">
                    {{ authStore.tokenTienda }}
                  </p>
                </div>

                <div class="flex items-center gap-1">
                  <div
                    class="w-3 h-3 rounded-full"
                    :class="getEstadoClass(authStore.estadoTienda)"
                  ></div>
                  <span class="text-secundary-default dark:text-mono-blanco">{{
                    authStore.estadoTienda
                  }}</span>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-rounded text-[16px] text-primary"
                    >phone</span
                  >
                  <p class="text-secundary-default dark:text-mono-blanco">
                    {{ authStore.telefonoEstablecimiento }}
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-rounded text-[16px] text-primary"
                    >email</span
                  >
                  <p class="text-secundary-default dark:text-mono-blanco">
                    {{ authStore.emailEstablecimiento }}
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-rounded text-[16px] text-primary"
                    >category</span
                  >
                  <p class="text-secundary-default dark:text-mono-blanco">
                    {{ authStore.tipoEstablecimiento }}
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-rounded text-[16px] text-primary"
                    >location_chip</span
                  >
                  <p class="text-secundary-default dark:text-mono-blanco">
                    {{
                      authStore.direccionEstablecimiento +
                      ", " +
                      authStore.ciudadEstablecimiento +
                      " " +
                      authStore.barrioEstablecimiento
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="rounded-[15px] p-3 div6 flex flex-col justify-center dark:bg-secundary-opacity bg-mono-blanco_opacity"
            >
              <div class="infoBasicaEmpleado">
                <div class="flex justify-between items-center">
                  <h4 class="text-secundary-default dark:text-mono-blanco">
                    Información de empleado
                  </h4>
                  <span
                    class="material-symbols-rounded text-[16px] text-primary cursor-pointer text-secundary-default dark:text-mono-blanco dark:hover:text-universal-azul_secundaria"
                    >info</span
                  >
                </div>
                <div class="flex items-center justify-between gap-1">
                  <h3
                    class="text-mono-negro font-semibold text-[30px] dark:text-mono-blanco"
                  >
                    {{ authStore.cargoEmpleado }}
                  </h3>
                </div>
                <div class="number flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >key</span
                    >
                    <p class="text-[14px] text-secundary-default dark:text-mono-blanco">
                      {{ authStore.establecimientoAsignado.id }}
                    </p>
                  </div>

                  <div class="flex items-center gap-1">
                    <div
                      class="w-3 h-3 rounded-full"
                      :class="getEstadoClass(authStore.estadoEmpleado)"
                    ></div>
                    <span class="text-secundary-default dark:text-mono-blanco">{{
                      authStore.estadoEmpleado
                    }}</span>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >event</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      {{ authStore.horariosEmpleado }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >article_person</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      Contrato: {{ authStore.tipoContratoEmpleado }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >attach_money</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      Salario: {{ formatCOP(authStore.salarioBaseEmpleado) }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >tag</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      Cuenta de ahorros: 0{{ authStore.cuentaAhorrosEmpleado }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >account_balance</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      Banco: {{ authStore.bancoEmpleado }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >account_balance_wallet</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      Medio de pago: {{ authStore.medioPagoEmpleado }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >event</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      Ingresó: {{ formatFecha(authStore.fechaIngresoEmpleado) }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >event</span
                    >
                    <p class="text-secundary-default dark:text-mono-blanco">
                      Actualizado: {{ formatFecha(authStore.fechaModificacionEmpleado) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="rounded-[15px] p-3 div7 dark:bg-secundary-opacity bg-mono-blanco_opacity"
            >
              <div class="flex justify-between items-center">
                <h4 class="text-secundary-default dark:text-mono-blanco">
                  Estas disfrutando de tu app
                </h4>
                <span
                  class="material-symbols-rounded text-[16px] text-primary cursor-pointer text-secundary-default dark:text-mono-blanco dark:hover:text-universal-azul_secundaria"
                  >info</span
                >
              </div>
              <div class="px-3 flex justify-between items-center">
                <div class="">
                  <p
                    class="text-[40px] font-medium text-secundary-default dark:text-mono-blanco"
                  >
                    {{ authStore.aplicacion }}
                  </p>
                  <p
                    class="-mt-3 font-light text-secundary-default dark:text-mono-blanco"
                  >
                    {{ authStore.descripcionApp }}
                  </p>
                </div>
                <p
                  class="flex items-center gap-1 text-secundary-default dark:text-mono-blanco"
                >
                  <span class="material-symbols-rounded text-primary">app_badging</span
                  >{{ authStore.categoriaApp }}
                </p>
                <p
                  class="flex items-center gap-1 text-secundary-default dark:text-mono-blanco"
                >
                  <span class="material-symbols-rounded text-primary">calendar_clock</span
                  >{{ authStore.periodoMembresia }}
                </p>
              </div>
            </div>
            <div
              class="rounded-[15px] p-4 div8 dark:bg-secundary-opacity bg-mono-blanco_opacity"
            >
              <div class="flex justify-between items-center">
                <h4 class="text-secundary-default dark:text-mono-blanco font-semibold">
                  Facturación de la Cuenta
                </h4>
                <span class="material-symbols-rounded text-[16px] text-gray-500"
                  >info</span
                >
              </div>

              <!-- Próximo Cobro -->
              <div class="mt-4">
                <h5 class="text-sm font-medium text-gray-400 mb-2">Próximo Cobro</h5>
                <div
                  v-if="proximaFactura"
                  class="w-full p-3 rounded-md bg-gray-800/50 outline-dashed outline-1 outline-gray-600"
                >
                  <div class="flex justify-between items-center">
                    <p class="font-semibold text-gray-200">
                      {{ formatFecha(proximaFactura.fecha_pago) }}
                    </p>
                    <span
                      class="text-xs font-bold px-2 py-1 rounded-full"
                      :class="getEstadoClass(proximaFactura.estado.tipo_estado)"
                    >
                      {{ proximaFactura.estado.tipo_estado }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center mt-1">
                    <p class="flex items-center gap-1 text-sm text-gray-400">
                      <span class="material-symbols-rounded text-[16px] text-primary"
                        >credit_card</span
                      >
                      {{ proximaFactura.medio_pago.forma_pago }}
                    </p>
                    <p class="font-semibold text-lg text-white">
                      {{ formatCOP(proximaFactura.monto_total) }}
                    </p>
                  </div>
                </div>
                <div v-else class="text-center py-4 text-gray-500 text-sm">
                  No hay información de próximo cobro.
                </div>
              </div>

              <!-- Historial de Facturas -->
              <div class="mt-6">
                <h5 class="text-sm font-medium text-gray-400 mb-2">Historial de Pagos</h5>
                <div
                  v-if="historialFacturas.length > 0"
                  class="space-y-2 max-h-48 overflow-y-auto pr-2"
                >
                  <div
                    v-for="factura in historialFacturas"
                    :key="factura.id"
                    class="w-full p-2 rounded-md bg-gray-800/50"
                  >
                    <div class="flex justify-between items-center">
                      <p class="text-sm text-gray-300">
                        {{ formatFecha(factura.fecha_pago) }}
                      </p>
                      <span
                        class="text-xs font-bold px-2 py-1 rounded-full"
                        :class="getEstadoClass(factura.estado.tipo_estado)"
                      >
                        {{ factura.estado.tipo_estado }}
                      </span>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                      <p class="flex items-center gap-1 text-xs text-gray-500">
                        <span class="material-symbols-rounded text-[14px]"
                          >credit_card</span
                        >
                        {{ factura.medio_pago.forma_pago }}
                      </p>
                      <p class="font-medium text-gray-200">
                        {{ formatCOP(factura.monto_total) }}
                      </p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-4 text-gray-500 text-sm">
                  No hay historial de facturas.
                </div>
              </div>
            </div>
          </div>
        </div>
      </SidebarSuperAdmin>
    </div>
  </div>
</template>
