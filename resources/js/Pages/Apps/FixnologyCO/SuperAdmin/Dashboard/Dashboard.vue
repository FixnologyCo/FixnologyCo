<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import 'dayjs/locale/es';
import Header from '@/Components/header/Header.vue';
import SidebarSuperAdmin from '@/Components/Sidebar/FixnologyCO/Sidebar.vue'
import Colors from '@/Composables/ModularColores';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';

const { appName, bgClase, bgOpacity, textoClase, buttonFocus } = Colors();

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

const nombreDia = ref('');
const dia = ref('');
const mes = ref('');
const anio = ref('');
const hora = ref('');
const saludo = ref('');

function actualizarFechaHora() {
  const fecha = new Date();
  dia.value = fecha.getDate();

  const nombreDias = [
    "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"
  ];
  const monthNamesClock = [
    "enero", "febrero", "marzo", "abril", "mayo", "junio",
    "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
  ];
  mes.value = monthNamesClock[fecha.getMonth()];
  nombreDia.value = nombreDias[fecha.getDay()];
  anio.value = fecha.getFullYear();

  let horas = fecha.getHours();
  const minutos = fecha.getMinutes().toString().padStart(2, "0");
  const segundos = fecha.getSeconds().toString().padStart(2, "0");
  const periodo = horas >= 12 ? "pm" : "am";

  if (horas > 12) {
    horas -= 12;
  } else if (horas === 0) {
    horas = 12;
  }
  hora.value = `${horas}:${minutos}:${segundos} ${periodo}`;
}

let clockInterval = null;
onMounted(() => {
  actualizarFechaHora();
  clockInterval = setInterval(actualizarFechaHora, 1000);
});
onUnmounted(() => {
  clearInterval(clockInterval);
});

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

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol

const dashboardRoute = computed(() => new URL(route('aplicacion.dashboard', { aplicacion, rol }), window.location.origin).pathname);
const obtenerIniciales = (usuario) => {
  const nombres = usuario.nombres_ct || '';
  const apellidos = usuario.apellidos_ct || '';

  const firstNameInitial = nombres.split(' ')[0]?.charAt(0).toUpperCase() || '';
  const lastNameInitial = apellidos.split(' ')[0]?.charAt(0).toUpperCase() || '';

  return firstNameInitial + lastNameInitial;
};
const currentPage = ref(1);
const perPage = 4;

const totalPages = computed(() => {
  return Math.ceil(props.usuariosRol4.length / perPage);
});

const paginatedUsuarios = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return props.usuariosRol4.slice(start, start + perPage);
});

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};
</script>

<template>

  <Head :title="`Bienvenido ${user.nombres_ct || 'Dashboard'}`" />
  <MensajesLayout />

  <div class="flex">
    <SidebarSuperAdmin :auth="auth" :cantidad-apps="cantidadApps" :cantidad-clientes-rol1="cantidadClientesRol1"
      :usuarios-rol4="usuariosRol4" />

    <div class="w-full px-3">
      <Header :auth="auth" />

      <div class="contenido">
        <div class="">
          <p class="text-[20px]">{{ saludo }}, {{ user.nombres_ct }}</p>
          <p class="text-[14px] -mt-[5px]">{{ nombreDia }} {{ dia }} de {{ mes }} {{ anio }}, {{ hora }}</p>
        </div>

       

      </div>
    </div>
  </div>
</template>
