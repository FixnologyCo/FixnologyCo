<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import Header from "@/Components/header/Header.vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { useTema } from "@/Composables/useTema";
const { modoOscuro } = useTema();

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
const user = ref(page.props.detallesCliente.id);

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || "Sin app";
const rol = props.auth.user.rol?.tipo_rol || "Sin rol"; // Obtén el tipo de rol

function obtenerIniciales(nombre, apellido) {
  const inicial1 = nombre?.charAt(0) || "";
  const inicial2 = apellido?.charAt(0) || "";
  return (inicial1 + inicial2).toUpperCase();
}
</script>

<template>
  <Head :title="`${detallesCliente.nombres_ct || 'No encontrado'}`" />

  <MensajesLayout />

  <div class="flex">
    <SidebarSuperAdmin :auth="auth" />

    <div class="w-full">
      <Header :auth="auth" :foto_base64="foto_base64" />

      <div class="contenido p-3">
        <!-- navegable -->
        <div class="options flex gap-1 items-center text-[14px]">
          <a
            :href="route('aplicacion.GestorUsuarios', { aplicacion, rol })"
            class="text-mono-negro dark:text-mono-blanco dark:hover:text-universal-azul"
            :class="hover"
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

        <div class="encabezadoDetalle flex justify-between items-center mt-3">
          <div class="leftDetalle">
            <div class="fotoNombreOpciones flex gap-3 items-center">
              <div
                class="w-[70px] h-[70px] rounded-full flex items-center justify-center text-white font-bold text-sm"
                :class="detallesCliente.foto_binaria ? '' : bgClase"
              >
                <img
                  v-if="detallesCliente.fotoUser"
                  :src="detallesCliente.fotoUser"
                  alt="Foto de usuario"
                  class="w-[70px] h-[70px] rounded-full object-cover border-2 shadowM"
                />
                <span v-else>
                  {{
                    obtenerIniciales(
                      detallesCliente.nombres_ct,
                      detallesCliente.apellidos_ct
                    )
                  }}
                </span>
              </div>
              <div class="nombreMembresia">
                <p class="text-mono-blanco font-semibold text-[20px]">
                  {{ detallesCliente.nombres_ct }} {{ detallesCliente.apellidos_ct }}
                </p>

                <div
                  class="membresiaId flex justify-between items-center my-1 text-mono-blanco"
                >
                <div class="app flex items-center gap-1">
                <div
                    class="p-1 font-semibold flex items-center rounded-[5px] text-mono-blanco"
                    :class="detallesCliente.tienda.aplicacion.color_fondo"
                  >
                    <span class="material-symbols-rounded text-[20px]">{{
                      detallesCliente.tienda.aplicacion.icono_app
                    }}</span
                    >
                    
                  </div>
                  <span>{{ detallesCliente.tienda.aplicacion.nombre_app }}</span>
                </div>
                  
                  <p class="text-[14px]">
                    {{ detallesCliente.tienda.aplicacion.membresia.nombre_membresia }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="rightDetalle">
            <button class="text-[14px]" :class="buttonClase">
              <span class="material-symbols-rounded">add_circle</span> Editar cliente
            </button>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>
