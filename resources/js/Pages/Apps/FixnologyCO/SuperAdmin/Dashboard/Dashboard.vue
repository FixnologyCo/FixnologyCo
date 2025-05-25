<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import 'dayjs/locale/es';
import Header from '@/Components/header/Header.vue';
import SidebarSuperAdmin from '@/Components/Sidebar/FixnologyCO/Sidebar.vue'
import Colors from '@/Composables/ModularColores';

const { appName, bgClase, textoClase, buttonFocus } = Colors();

const props = defineProps({
  auth: {
    type: Object,
    required: true
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
  },
  cantidadApps: {
    type: Number,
    default: 0
  },
  cantidadClientesRol1: {
    type: Number,
    default: 0
  },
  usuariosRol4: {
    type: Array,
    default: () => [],
  },
})

// Acceder a los datos de Inertia, incluyendo flash
const { flash } = usePage().props;

// Definir variables reactivas para notificaciones
const mensajeNotificacion = ref('');
const tipoNotificacion = ref(null);
const mostrarNotificacion = ref(false);

// Función para mostrar notificaciones
const mostrarMensaje = (mensaje, tipo) => {
  mensajeNotificacion.value = mensaje;
  tipoNotificacion.value = tipo;
  mostrarNotificacion.value = true;
  setTimeout(() => {
    mostrarNotificacion.value = false;
  }, 5000);
};

onMounted(() => {
  if (usePage().props.flash && usePage().props.flash.success) {
    mostrarMensaje(usePage().props.flash.success, 'success');
  } else if (usePage().props.flash.error) {
    mostrarMensaje(usePage().props.flash.error, 'error');
  }
});



const user = props.auth.user
const auth = usePage().props.auth;

const saludo = ref('');

let fecha = new Date();
let horas = fecha.getHours();

if (horas > 12) {
  horas -= 12;
} else if (horas === 0) {
  horas = 12;
}


if (fecha.getHours() < 12) {
  saludo.value = "¡Buenos días!";
} else if (fecha.getHours() < 18) {
  saludo.value = "¡Buenas tardes!";
} else {
  saludo.value = "¡Buenas noches!";
}
</script>

<template>

  <Head :title="`Bienvenido ${user.nombres_ct || 'Dashboard'}`" />


  <div class="flex">
    <SidebarSuperAdmin :auth="auth" :cantidad-apps="cantidadApps" :cantidad-clientes-rol1="cantidadClientesRol1" :usuarios-rol4="usuariosRol4"/>

    <div class="w-full">
      <Header :auth="auth" />

      <div class="contenido px-4">
        <p class="text-[25px] my-3">{{ saludo }}, {{ user.nombres_ct }}</p>

      </div>
    </div>
  </div>
</template>
