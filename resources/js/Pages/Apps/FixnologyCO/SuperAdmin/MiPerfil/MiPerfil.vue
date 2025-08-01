<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { defineProps, computed, ref } from "vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";

import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { formatFecha } from "@/Utils/date";
import { formatCOP } from "@/Utils/formateoMoneda";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useTiempo } from "@/Composables/useTiempo";
import useEstadoClass from "@/Composables/useEstado";
import { useSidebar } from "@/Composables/useSidebar";

const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
  inheritAttrs: false,
});

const { getEstadoClass } = useEstadoClass();
const { sidebarExpandido, toggleSidebar } = useSidebar();

const page = usePage();

const usuario = authStore.user;
const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

const { tiempoActivo, diasRestantes } = useTiempo(usuario);

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

const fotoUsuario = computed(() => {
  const ruta = authStore.rutaFoto;

  if (ruta && ruta !== "Sin foto") {
    if (ruta.startsWith("http")) {
      return ruta;
    }
    return `/storage/${ruta}`;
  }

  return "Sin foto";
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
                    :class="sidebarExpandido ? 'text-[25px]' : 'text-[30px]'"
                    class="font-semibold -mt-1 text-secundary-default dark:text-mono-blanco"
                  >
                    {{ diasRestantes
                    }}<span
                      class="text-[14px] text-secundary-default dark:text-mono-blanco"
                      >Días</span
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
                    {{ tiempoActivo }} <span class="text-[14px]"></span>
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
              class="rounded-[15px] p-3 div8 dark:bg-secundary-opacity bg-mono-blanco_opacity"
            >
              <div class="flex justify-between items-center">
                <h4 class="text-secundary-default dark:text-mono-blanco">
                  Facturación de tu cuenta
                </h4>
                <span
                  class="material-symbols-rounded text-[16px] text-primary cursor-pointer text-secundary-default dark:text-mono-blanco dark:hover:text-universal-azul_secundaria"
                  >info</span
                >
              </div>

              <div
                class="w-full p-3 h-[auto] rounded-md mt-4 outline-dashed outline-1 outline-semaforo-verde"
                :class="getEstadoClass(authStore.estadoFactura)"
              >
                <div class="flex justify-between items-center">
                  <p class="text-secundary-default dark:text-mono-blanco">
                    {{ formatFecha(authStore.fechaPago) }}
                  </p>
                  <button
                    class="bg-semaforo-verde flex items-center text-[14px] gap-1 px-2 py-1 rounded-md shadow-verde hover:bg-semaforo-verde_opacity hover:text-semaforo-verde"
                  >
                    {{ authStore.estadoFactura }}
                  </button>
                </div>
                <div class="flex justify-between items-center mt-1">
                  <p
                    class="flex items-center gap-1 text-secundary-default dark:text-mono-blanco"
                  >
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >account_balance_wallet</span
                    >{{ authStore.medioPagoFactura }}
                  </p>
                  <p>{{ formatCOP(authStore.montoFactura) }}</p>
                </div>
              </div>
              <div class="flex justify-between items-center my-5">
                <h4 class="text-secundary-default dark:text-mono-blanco">
                  Próximo cobro
                </h4>
                <span
                  class="material-symbols-rounded text-[16px] text-primary cursor-pointer text-secundary-default dark:text-mono-blanco dark:hover:text-universal-azul_secundaria"
                  >info</span
                >
              </div>
              <div
                class="w-full p-3 h-[auto] rounded-md mt-4 outline-dashed outline-1 outline-gray-400 bg-mono-negro_opacity_full dark:bg-gray-800"
              >
                <div class="flex justify-between items-center">
                  <p class="">lunes 21 de julio de 4763 a las 12:27 pm</p>
                  <button
                    class="bg-gray-400 flex items-center text-[14px] gap-1 px-2 py-1 rounded-md shadowM"
                  >
                    Proceso
                  </button>
                </div>
                <div class="flex justify-between items-center mt-1">
                  <p class="flex items-center gap-1">
                    <span class="material-symbols-rounded text-[16px] text-primary"
                      >account_balance_wallet</span
                    >{{ authStore.medioPagoFactura }}
                  </p>
                  <p>{{ formatCOP(authStore.montoFactura) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </SidebarSuperAdmin>
    </div>
  </div>
</template>
