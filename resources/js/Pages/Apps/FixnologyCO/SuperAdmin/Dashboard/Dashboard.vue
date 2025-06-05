<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import 'dayjs/locale/es';
import Header from '@/Components/header/Header.vue';
import SidebarSuperAdmin from '@/Components/Sidebar/FixnologyCO/Sidebar.vue'
import Colors from '@/Composables/ModularColores';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';
import { useTema } from '@/Composables/useTema';
const { modoOscuro } = useTema();

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
  foto_base64: {
    type: String,
    default: ''
  }
})

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

</script>

<template>

  <Head :title="`Bienvenido ${user.nombres_ct || 'Dashboard'}`" />
  <MensajesLayout />

  <div class="flex">
    <SidebarSuperAdmin :auth="auth" :cantidad-apps="cantidadApps" :cantidad-clientes-rol1="cantidadClientesRol1"
      :usuarios-rol4="usuariosRol4" />

    <div class="w-full">
      <Header :auth="auth" :foto_base64="foto_base64" />

      <div class="contenido p-3">
        <div class="" >
          <p class="text-[20px] text-mono-negro dark:text-mono-blanco">{{ saludo }}, {{ user.nombres_ct }}</p>
          <p class="text-[14px] -mt-[5px] text-mono-negro dark:text-mono-blanco">{{ nombreDia }} {{ dia }} de {{ mes }} {{ anio }}, {{ hora }}</p>
        </div>



      </div>
    </div>
  </div>
</template>
