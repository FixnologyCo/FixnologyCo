<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props = defineProps({
  token: String,
})

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

const form = useForm({
  email: '',
  password: '',
  password_confirmation: '',
  token: props.token,
})

const submit = () => {
  form.post(route('password.update'), {
    onSuccess: () => {
      mostrarMensaje('Contraseña actualizada correctamente', 'success')
    },
    onError: () => {
      mostrarMensaje('Error al actualizar la contraseña', 'error')
    }
  })
}
</script>

<template>
  <Head title="Restablecer contraseña" />
  <div class="flex flex-col items-center justify-center min-h-screen bg-mono-oscuro px-4 text-white">
    <h2 class="text-2xl font-bold mb-6">Restablece tu contraseña</h2>

    <form @submit.prevent="submit" class="w-full max-w-md space-y-5">
      <!-- Correo -->
      <div>
        <label class="block text-sm mb-1">Correo electrónico</label>
        <input
          type="email"
          v-model="form.email"
          class="w-full px-4 py-2 border rounded bg-mono-negro text-white focus:outline-none"
          :class="{ 'border-red-500': form.errors.email }"
        />
        <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
      </div>

      <!-- Nueva contraseña -->
      <div>
        <label class="block text-sm mb-1">Nueva contraseña</label>
        <input
          type="password"
          v-model="form.password"
          class="w-full px-4 py-2 border rounded bg-mono-negro text-white focus:outline-none"
          :class="{ 'border-red-500': form.errors.password }"
        />
        <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
      </div>

      <!-- Confirmar contraseña -->
      <div>
        <label class="block text-sm mb-1">Confirmar contraseña</label>
        <input
          type="password"
          v-model="form.password_confirmation"
          class="w-full px-4 py-2 border rounded bg-mono-negro text-white focus:outline-none"
          :class="{ 'border-red-500': form.errors.password_confirmation }"
        />
        <span v-if="form.errors.password_confirmation" class="text-red-500 text-sm">
          {{ form.errors.password_confirmation }}
        </span>
      </div>

      <!-- Botón -->
      <button
        type="submit"
        class="btn-taurus w-full flex items-center justify-center gap-2"
        :disabled="form.processing"
      >
        Restablecer contraseña
        <span class="material-symbols-rounded bg-transparent">lock_reset</span>
      </button>

      <!-- Notificación -->
      <div v-if="mostrarNotificacion" :class="{
        'text-green-500': tipoNotificacion === 'success',
        'text-red-500': tipoNotificacion === 'error'
      }" class="text-center text-sm mt-2">
        {{ mensajeNotificacion }}
      </div>
    </form>
  </div>
</template>
