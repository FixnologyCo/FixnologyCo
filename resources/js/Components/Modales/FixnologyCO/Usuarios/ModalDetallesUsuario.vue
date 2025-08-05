<script setup>
import { defineProps, defineEmits, computed, ref } from "vue";
import { useInfoUser } from "@/stores/infoUsers";
import useEstadoClass from "@/Composables/useEstado";
import { formatFecha } from "@/Utils/date";
import { formatCOP } from "@/Utils/formateoMoneda";
import { useTiempo } from "@/Composables/useTiempo";
import { router } from "@inertiajs/vue3";

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(["close"]);
const infoUserStore = useInfoUser();
const { getEstadoClass } = useEstadoClass();

// 1. Creamos una propiedad computada que siempre refleje el usuario de la tienda.
//    Esto es una fuente de datos reactiva.
const usuarioSeleccionado = computed(() => infoUserStore.user);

// 2. Llamamos a useTiempo UNA SOLA VEZ, pasándole la fuente reactiva.
//    El composable se encargará del resto.
const { tiempoActivo, diasRestantes } = useTiempo(usuarioSeleccionado);

// --- El resto de tus computed properties pueden usar la tienda directamente ---

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

function eliminarUsuario() {
  // 1. Mostramos una ventana de confirmación nativa del navegador.
  //    (Para una mejor experiencia, podrías reemplazar esto con un modal de confirmación).
  if (
    confirm(
      `¿Estás seguro de que quieres eliminar a ${infoUserStore.nombreCompleto}? Esta acción no se puede deshacer.`
    )
  ) {
    // 2. Usamos el router de Inertia para enviar una petición DELETE.
    router.delete(
      route("usuarios.destroy", { usuarioAEliminar: infoUserStore.idUsuario }),
      {
        onSuccess: () => {
          // 3. Si la eliminación es exitosa, cerramos el modal.
          //    Inertia recargará la página y mostrará el mensaje de éxito.
          emit("close");
        },
        onError: (errors) => {
          // Opcional: Manejar errores, por ejemplo, mostrando una notificación.
          console.error("Error al eliminar:", errors);
          alert("No se pudo eliminar el usuario.");
        },
      }
    );
  }
}
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
                    :class="getEstadoClass(infoUserStore.estadoFactura)"
                  >
                    <div class="flex justify-between items-center">
                      <p class="text-secundary-default dark:text-mono-blanco">
                        {{ formatFecha(infoUserStore.fechaPago) }}
                      </p>
                      <button
                        class="bg-semaforo-verde flex items-center text-[14px] gap-1 px-2 py-1 rounded-md shadow-verde hover:bg-semaforo-verde_opacity hover:text-semaforo-verde"
                      >
                        {{ infoUserStore.estadoFactura }}
                      </button>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                      <p
                        class="flex items-center gap-1 text-secundary-default dark:text-mono-blanco"
                      >
                        <span class="material-symbols-rounded text-[16px] text-primary"
                          >account_balance_wallet</span
                        >{{ infoUserStore.medioPagoFactura }}
                      </p>
                      <p>{{ formatCOP(infoUserStore.montoFactura) }}</p>
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
                        >{{ infoUserStore.medioPagoFactura }}
                      </p>
                      <p>{{ formatCOP(infoUserStore.montoFactura) }}</p>
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
</template>

<style scoped>
:deep(.modal-fade-enter-active),
:deep(.modal-fade-leave-active) {
  transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
:deep(.modal-fade-enter-from),
:deep(.modal-fade-leave-to) {
  opacity: 0;
}
:deep(.modal-slide-enter-active),
:deep(.modal-slide-leave-active) {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
:deep(.modal-slide-enter-from),
:deep(.modal-slide-leave-to) {
  opacity: 0;
  transform: translateY(30px) scale(0.97);
}
</style>
