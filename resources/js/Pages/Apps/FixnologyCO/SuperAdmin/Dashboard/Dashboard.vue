<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import 'dayjs/locale/es';
import SaludoOpciones from '@/Components/header/SaludoOpciones.vue';
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
const clientes = ref(props.clientes);
</script>

<template>

  <Head :title="`Bienvenido ${user.nombres_ct || 'Dashboard'}`" />

  <div class="w-full h-[100%] px-[40px] py-[20px]">
    <SaludoOpciones :auth="auth" />

<div :class="bgClase">
</div>
<p :class="textoClase">Bienvenido a {{ appName }}</p>

<button :class="buttonFocus">Acción</button>
  </div>


</template>
