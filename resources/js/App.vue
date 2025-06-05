<template>
    <div>
      <notifications />
      <slot />
    </div>
  </template>
  
  <script>
  import Notifications from './Components/DeteccionInternet/Notifications.vue';
  
  export default {
    components: {
      Notifications, 
    }
  }
  </script>

  <script setup>
import { onMounted } from 'vue'
import { useTema } from '@/Composables/useTema'

const { detectarTemaInicial, escucharCambiosSistema } = useTema()

onMounted(() => {
  detectarTemaInicial()
  escucharCambiosSistema()
})
});
  </script>