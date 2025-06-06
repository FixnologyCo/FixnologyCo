<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import 'dayjs/locale/es';
import Header from '@/Components/header/Header.vue';
import SidebarSuperAdmin from '@/Components/Sidebar/FixnologyCO/Sidebar.vue'
import Colors from '@/Composables/ModularColores';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';
import { useTema } from '@/Composables/useTema';

const { appName, bgClase, bgOpacity, focus, textoClase, borderClase, buttonFocus, hoverClase, hoverTexto, buttonClase, buttonSecundario } = Colors();

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

        <div class="indiceApps w-full flex justify-between items-center">
          <div class="indiceLeft w-[50%]">
            <h1 class="text-[30px] font-semibold text-mono-negro dark:text-mono-blanco">Lista de aplicaciones</h1>
            <p class="-mt-[10px] text-secundary-default dark:text-secundary-light">En este apartado estarán las
              aplicaciones de FixnologyCO</p>
          </div>
          <div class="indiceRight ">
            <button :class="[buttonClase]">Crear nueva app <span
                class="material-symbols-rounded">add_circle</span></button>
          </div>
        </div>

        <div class="encabezadoOpciones mt-10 flex items-center gap-2 w-full">
          <span class="text-secundary-default dark:text-secundary-light text-[17px]">Listas</span>
          <div class="line h-[2px] rounded-full w-full bg-secundary-light"></div>
          <div
            class="button flex justify-center items-center border-2 border-secundary-light rounded-full text-mono-negro dark:text-secundary-light">
            <span class="material-symbols-rounded text-[20px]">keyboard_arrow_down</span>
          </div>
        </div>

        <div class="tarjetaApp border border-secundary-light rounded-md p-4 max-w-[25%] mt-5">
          <div class="top flex justify-between">
            <div class="conjunto flex gap-3">
              <div class="icono h-[50px] w-[50px] rounded-lg" :class="[bgClase]"></div>
              <div class="titulo-contador">
                <h3 class="text-secundary-default dark:text-secundary-light font-semibold">Control de votaciones</h3>
                <p class="text-secundary-default dark:text-mono-blanco -mt-2 text-[28px] font-bold">1</p>
              </div>
            </div>

            <div class="opciones"><span class="material-symbols-rounded text-mono-negro dark:text-secundary-light">more_horiz</span></div>
          </div>
          <div class="line h-[1.5px] rounded-full w-full bg-secundary-light my-3"></div>
          <div class="descripciones">
            <p class="text-[14px] text-mono-negro dark:text-mono-blanco">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
          </div>

          <div class="tags">
            
          </div>
        </div>



      </div>
    </div>
  </div>
</template>
