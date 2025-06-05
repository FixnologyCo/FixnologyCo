<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { useTema } from '@/Composables/useTema';

const { modoOscuro } = useTema();
const mensajeNotificacion = ref('')
const tipoNotificacion = ref(null)
const mostrarNotificacion = ref(false)

const mostrarMensaje = (mensaje, tipo) => {
  mensajeNotificacion.value = mensaje
  tipoNotificacion.value = tipo
  mostrarNotificacion.value = true

  setTimeout(() => {
    mostrarNotificacion.value = false
  }, 5000)
}

// 🔁 Observa los cambios en flash
watch(
  () => usePage().props.flash,
  (flash) => {
    if (flash.success) {
      mostrarMensaje(flash.success, 'success')
    } else if (flash.error) {
      mostrarMensaje(flash.error, 'error')
    }
  },
  { immediate: true }
)
</script>


<template>
    <div v-if="mostrarNotificacion && tipoNotificacion === 'success'"
        class="notificacion translate-y-8 absolute w-[max-content] left-0 right-0 top-6 ml-auto mr-auto rounded-md text-mono-blanco shadow-verde bg-semaforo-verde dark:bg-semaforo-verde_opacity">
        <div class="notificacion_body flex justify-center gap-3 items-center py-3 px-2">
          <div class="flex gap-2 items-center">
            <i class="material-symbols-rounded text-mono-blanco dark:text-semaforo-verde">check_circle</i>
            <p>{{ mensajeNotificacion }}</p>
          </div>
        </div>
        <div
          class="progreso_notificacion absolute left-1 bottom-1 h-1 scale-x-0 origin-left rounded-sm bg-mono-blanco dark:bg-semaforo-verde">
        </div>
      </div>
      <!-- ✅ Notificación de error -->
      <div v-if="mostrarNotificacion && tipoNotificacion === 'error'"
        class="notificacion translate-y-8 absolute w-[max-content] left-0 right-0 top-6 ml-auto mr-auto rounded-md bg-semaforo-rojo dark:bg-semaforo-rojo_opacity text-mono-blanco shadow-rojo">
        <div class="notificacion_body flex justify-center gap-3 items-center py-3 px-2">
          <div class="flex gap-2 items-center">
            <i class="material-symbols-rounded text-mono-blanco dark:text-semaforo-rojo">cancel</i>
            <p>{{ mensajeNotificacion }}</p>
          </div>
        </div>
        <div
          class="progreso_notificacion absolute left-1 bottom-1 h-1 scale-x-0 origin-left rounded-sm bg-mono-blanco dark:bg-semaforo-rojo">
        </div>
      </div>
</template>