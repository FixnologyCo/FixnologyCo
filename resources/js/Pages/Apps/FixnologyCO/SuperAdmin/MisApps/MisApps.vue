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

</script>

<template>

  <Head title="Mis aplicaciones" />
  <MensajesLayout />

  <div class="flex">
    <SidebarSuperAdmin :auth="auth" />

    <div class="w-full">
      <Header :auth="auth" :foto_base64="foto_base64" />

      <div class="contenido p-3">
       



      </div>
    </div>
  </div>
</template>
