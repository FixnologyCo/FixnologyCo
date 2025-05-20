<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-6 text-center">Restablecer Contraseña</h2>

      <form @submit.prevent="submit">
        <!-- Nueva Contraseña -->
        <div class="mb-4">
          <label class="block mb-1 font-medium">Nueva contraseña</label>
          <input
            type="password"
            v-model="form.password"
            class="w-full border px-3 py-2 rounded"
            placeholder="********"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Confirmar contraseña -->
        <div class="mb-4">
          <label class="block mb-1 font-medium">Confirmar contraseña</label>
          <input
            type="password"
            v-model="form.password_confirmation"
            class="w-full border px-3 py-2 rounded"
            placeholder="********"
          />
        </div>

        <!-- Botón -->
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
        >
          Restablecer
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  token: String,
  email: String,
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('password.update'), {
    onSuccess: () => {
      alert('¡Contraseña restablecida correctamente!')
    },
    onError: (errors) => {
      console.error(errors)
    }
  })
}
</script>
