<script setup>
import { defineProps, defineEmits, computed, ref } from "vue";
import useEstadoClass from "@/Composables/useEstado";
import { useTiempo } from "@/Composables/useTiempo";
import { formatFecha } from "@/Utils/date";
import { formatCOP } from "@/Utils/formateoMoneda";
import { router } from "@inertiajs/vue3";
import { useInfoUser } from "@/stores/infoUsers";
import dayjs from "dayjs";
import ConfirmacionesPop from "../../Confirmaciones/ConfirmacionesPop.vue";

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(["close"]);
const infoUserStore = useInfoUser();
const { getEstadoClass } = useEstadoClass();

const usuarioSeleccionado = computed(() => infoUserStore.user);

const { tiempoActivo, diasRestantes } = useTiempo(usuarioSeleccionado);

const inicialesNombreUsuario = computed(() => {
  const nombres = infoUserStore.primerNombre || "";
  const apellidos = infoUserStore.primerApellido || "";
  const firstNameInitial = nombres.charAt(0).toUpperCase() || "";
  const lastNameInitial = apellidos.charAt(0).toUpperCase() || "";
  return firstNameInitial + lastNameInitial;
});

const inicialesNombreEstablecimiento = computed(() => {
  const nombreEstablecimiento = infoUserStore.nombreEstablecimiento || "Tienda";
  const palabrasAIgnorar = ["de", "el", "la", "los", "las", "y", "a"];
  const iniciales = nombreEstablecimiento
    .split(" ")
    .filter((palabra) => !palabrasAIgnorar.includes(palabra.toLowerCase()))
    .map((palabra) => palabra[0])
    .join("");
  return iniciales.toUpperCase().slice(0, 2);
});

const activeTab = ref(0);
const tabs = [{ label: "Información general" }, { label: "Editar usuario" }];

// --- 2. LÓGICA PARA EL MODAL DE CONFIRMACIÓN ---
const confirmationState = ref({
  isOpen: false,
  title: "",
  message: "",
  icon: "help",
  iconBgClass: "bg-gray-700",
  confirmText: "Confirmar",
  onConfirm: () => {},
});

function openConfirmationModal({
  title,
  message,
  onConfirm,
  icon = "help",
  iconBgClass = "bg-gray-700",
  confirmText = "Confirmar",
}) {
  confirmationState.value = {
    isOpen: true,
    title,
    message,
    icon,
    iconBgClass,
    confirmText,
    onConfirm,
  };
}

function closeConfirmationModal() {
  confirmationState.value.isOpen = false;
}

function handleConfirm() {
  confirmationState.value.onConfirm();
  closeConfirmationModal();
}

function eliminarUsuario() {
  openConfirmationModal({
    title: "¿Estás seguro de enviar a la papelera?",
    message: `Estás a punto de enviar a la papelera a ${infoUserStore.nombreCompleto}.`,
    icon: "info",
    iconBgClass: "bg-yellow-500/20 border-semaforo-amarillo",
    confirmText: "Sí, enviar",
    onConfirm: () => {
      router.delete(
        route("usuarios.destroy", {
          usuarioAEliminar: infoUserStore.idUsuario,
        }),
        {
          preserveScroll: true,
          onSuccess: () => {
            emit("close");
          },
        }
      );
    },
  });
}
const historialFacturas = computed(() => {
  return [...infoUserStore.facturas].sort((a, b) =>
    dayjs(b.fecha_pago).diff(dayjs(a.fecha_pago))
  );
});

const proximaFactura = computed(() => {
  if (infoUserStore.facturas.length === 0) {
    return null;
  }

  const ultimaFacturaPagada = historialFacturas.value.find(
    (f) => f.estado.tipo_estado === "Pagado"
  );

  if (!ultimaFacturaPagada) {
    return historialFacturas.value[0];
  }
  const fechaUltimoPago = dayjs(ultimaFacturaPagada.fecha_pago);
  const proximaFechaPago = fechaUltimoPago.add(infoUserStore.duracionMembresia, "day");

  return {
    fecha_pago: proximaFechaPago.toISOString(),
    estado: { tipo_estado: "Pendiente" },
    medio_pago: ultimaFacturaPagada.medio_pago,
    monto_total: ultimaFacturaPagada.monto_total,
  };
});
</script>

<template>
  <Transition name="modal-fade" appear>
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm"
      @click.self="$emit('close')"
    >
      <Transition name="modal-slide" appear>
        <div
          v-if="infoUserStore.isAuthenticated"
          class="relative bg-mono-negro_opacity_full rounded-xl shadow-lg w-full max-w-[90%] text-gray-300 overflow-hidden"
        >
          <div class="mb-6 flex justify-between items-center my-3 px-4">
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
            <button
              @click="$emit('close')"
              class="text-gray-400 material-symbols-rounded text-[25px] hover:text-gray-200 focus:outline-none"
            >
              Close
            </button>
          </div>
          <div v-if="activeTab === 0" class="p-3">
            <div
              class="contenido px-3 max-h-[85vh] w-full overflow-auto scrollbar-custom contenido-principal"
            >
              <div class="miPerfil w-full">
                <div
                  class="rounded-[15px] p-3 flex flex-col gap-5 div2 bg-mono-blanco_opacity dark:bg-secundary-opacity"
                >
                  <div class="w-full h-[130px] rounded-md opacity-50 bg-primary"></div>

                  <div class="flex justify-center flex-col items-center">
                    <div
                      class="-mt-28 ml-5 mb-2 grid place-content-center foto w-[180px] h-[180px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                    >
                      <template v-if="infoUserStore.fotoUrlCompletaUsuario">
                        <div class="relative group w-[160px] h-[160px]">
                          <img
                            :src="infoUserStore.fotoUrlCompletaUsuario"
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
                        {{ infoUserStore.nombreCompleto }}
                      </h3>
                      <div
                        class="grid place-items-center"
                        v-if="infoUserStore.google_id === null"
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
                        <p
                          class="text-[14px] text-secundary-default dark:text-mono-blanco"
                        >
                          {{ infoUserStore.google_id || infoUserStore.idUsuario }}
                        </p>
                      </div>

                      <div class="flex items-center gap-1">
                        <div
                          class="w-3 h-3 rounded-full"
                          :class="getEstadoClass(infoUserStore.estadoUsuario)"
                        ></div>
                        <span class="text-secundary-default dark:text-mono-blanco">{{
                          infoUserStore.estadoUsuario
                        }}</span>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >phone</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          {{ infoUserStore.telefonoCompleto }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >email</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          {{ infoUserStore.email }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >id_card</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          {{
                            infoUserStore.tipoDocumento +
                            ": " +
                            infoUserStore.numero_documento
                          }}
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
                            infoUserStore.direccionResidencia +
                            ", " +
                            infoUserStore.barrioResidencia +
                            " " +
                            infoUserStore.ciudadResidencia
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
                      Membresia del usuario:
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
                        {{ infoUserStore.membresia }}
                      </p>
                      <p
                        class="-mt-3 font-light text-secundary-default dark:text-mono-blanco"
                      >
                        {{ infoUserStore.descripcionMembresia }}
                      </p>
                    </div>
                  </div>
                </div>
                <div
                  class="rounded-[15px] div4 flex justify-between items-center dark:bg-secundary-opacity bg-mono-blanco_opacity p-5"
                >
                  <h4
                    class="text-[60px] font-medium text-secundary-default dark:text-mono-blanco"
                  >
                    Información del usuario
                  </h4>

                  <div class="btn-initon flex justify-center gap-2 mt-5 p-2">
                    <button
                      @click="eliminarUsuario"
                      class="border border-semaforo-rojo_opacity text-semaforo-rojo_opacity rounded-md p-1 gap-1 w-[100%] flex items-center justify-center hover:text-semaforo-rojo hover:border-semaforo-rojo"
                    >
                      <span class="material-symbols-rounded text-[18px]">delete</span>
                      <span class="">Eliminar Perfil</span>
                    </button>
                  </div>
                </div>
                <div
                  class="rounded-[15px] p-3 div5 dark:bg-secundary-opacity bg-mono-blanco_opacity"
                >
                  <div class="w-full h-[130px] rounded-md opacity-50 bg-primary"></div>

                  <div class="flex gap-2 justify-center items-center">
                    <div
                      class="-mt-20 ml-5 mb-2 grid place-content-center foto w-[180px] h-[180px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                    >
                      <template v-if="infoUserStore.fotoUrlCompletaEstablecimiento">
                        <div class="relative group w-[160px] h-[160px]">
                          <img
                            :src="infoUserStore.fotoUrlCompletaEstablecimiento"
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
                      {{ infoUserStore.nombreEstablecimiento }}
                    </h3>
                    <div
                      class="grid place-items-center"
                      v-if="infoUserStore.google_id === null"
                    >
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
                        {{ infoUserStore.tokenEstablecimiento }}
                      </p>
                    </div>

                    <div class="flex items-center gap-1">
                      <div
                        class="w-3 h-3 rounded-full"
                        :class="getEstadoClass(infoUserStore.estadoTienda)"
                      ></div>
                      <span class="text-secundary-default dark:text-mono-blanco">{{
                        infoUserStore.estadoTienda
                      }}</span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-rounded text-[16px] text-primary"
                        >phone</span
                      >
                      <p class="text-secundary-default dark:text-mono-blanco">
                        {{ infoUserStore.telefonoEstablecimiento }}
                      </p>
                    </div>
                  </div>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-rounded text-[16px] text-primary"
                        >email</span
                      >
                      <p class="text-secundary-default dark:text-mono-blanco">
                        {{ infoUserStore.emailEstablecimiento }}
                      </p>
                    </div>
                  </div>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-rounded text-[16px] text-primary"
                        >category</span
                      >
                      <p class="text-secundary-default dark:text-mono-blanco">
                        {{ infoUserStore.tipoEstablecimiento }}
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
                          infoUserStore.direccionEstablecimiento +
                          ", " +
                          infoUserStore.ciudadEstablecimiento +
                          " " +
                          infoUserStore.barrioEstablecimiento
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
                        {{ infoUserStore.cargoEmpleado }}
                      </h3>
                    </div>
                    <div class="number flex items-center justify-between gap-2">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >key</span
                        >
                        <p
                          class="text-[14px] text-secundary-default dark:text-mono-blanco"
                        >
                          {{ infoUserStore.establecimientoAsignado?.id }}
                        </p>
                      </div>

                      <div class="flex items-center gap-1">
                        <div
                          class="w-3 h-3 rounded-full"
                          :class="getEstadoClass(infoUserStore.estadoEmpleado)"
                        ></div>
                        <span class="text-secundary-default dark:text-mono-blanco">{{
                          infoUserStore.estadoEmpleado
                        }}</span>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >event</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          {{ infoUserStore.horariosEmpleado }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >article_person</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          Contrato: {{ infoUserStore.tipoContratoEmpleado }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >attach_money</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          Salario: {{ formatCOP(infoUserStore.salarioBaseEmpleado) }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >tag</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          Cuenta de ahorros: 0{{ infoUserStore.cuentaAhorrosEmpleado }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >account_balance</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          Banco: {{ infoUserStore.bancoEmpleado }}
                        </p>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >account_balance_wallet</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          Medio de pago: {{ infoUserStore.medioPagoEmpleado }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >event</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          Ingresó: {{ formatFecha(infoUserStore.fechaIngresoEmpleado) }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >event</span
                        >
                        <p class="text-secundary-default dark:text-mono-blanco">
                          Actualizado:
                          {{ formatFecha(infoUserStore.fechaModificacionEmpleado) }}
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
                        {{ infoUserStore.aplicacion }}
                      </p>
                      <p
                        class="-mt-3 font-light text-secundary-default dark:text-mono-blanco"
                      >
                        {{ infoUserStore.descripcionApp }}
                      </p>
                    </div>
                    <p
                      class="flex items-center gap-1 text-secundary-default dark:text-mono-blanco"
                    >
                      <span class="material-symbols-rounded text-primary"
                        >app_badging</span
                      >{{ infoUserStore.categoriaApp }}
                    </p>
                    <p
                      class="flex items-center gap-1 text-secundary-default dark:text-mono-blanco"
                    >
                      <span class="material-symbols-rounded text-primary"
                        >calendar_clock</span
                      >{{ infoUserStore.periodoMembresia }}
                    </p>
                  </div>
                </div>
                <div
                  class="rounded-[15px] p-4 div8 dark:bg-secundary-opacity bg-mono-blanco_opacity"
                >
                  <div class="flex justify-between items-center">
                    <h4
                      class="text-secundary-default dark:text-mono-blanco font-semibold"
                    >
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
                    <h5 class="text-sm font-medium text-gray-400 mb-2">
                      Historial de Pagos
                    </h5>
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
          </div>
          <div v-if="activeTab === 1"></div>
        </div>
      </Transition>
    </div>
  </Transition>
  <ConfirmacionesPop
    :is-open="confirmationState.isOpen"
    :title="confirmationState.title"
    :message="confirmationState.message"
    :icon="confirmationState.icon"
    :icon-bg-class="confirmationState.iconBgClass"
    :confirm-text="confirmationState.confirmText"
    @close="closeConfirmationModal"
    @confirm="handleConfirm"
  />
</template>

<style scoped>
.input-field {
  @apply mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-white;
}
.error-message {
  @apply text-red-500 text-xs mt-1;
}

/* Transición para el modal completo */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.modal-slide-enter-active,
.modal-slide-leave-active {
  transition: all 0.3s ease-out;
}
.modal-slide-enter-from,
.modal-slide-leave-to {
  transform: translateY(20px);
  opacity: 0;
}

/* Transición para los pasos del formulario */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.25s ease-out;
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
