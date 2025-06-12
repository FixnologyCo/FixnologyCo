<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { route } from 'ziggy-js';
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import Header from "@/Components/header/Header.vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { useTema } from "@/Composables/useTema";
const { modoOscuro } = useTema();

const { appName, bgClase, bgOpacity, textoClase, buttonFocus } = Colors();

const props = defineProps({
  auth: {
    type: Object,
    required: true,
  },
  clientes: {
    type: Array,
    default: () => [],
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
const user = ref(page.props.detallesCliente.id  )

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol

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
      </div>
    </div>
  </div>
</template>
