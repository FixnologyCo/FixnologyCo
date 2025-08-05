<script setup>
import { defineProps, defineEmits } from "vue";
import { router } from "@inertiajs/vue3";
import { formatFecha } from "@/Utils/date";

// 1. Recibimos la lista de usuarios eliminados como una prop
const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  usuariosEnPapelera: {
    type: Array,
    default: () => [],
  },
});

defineEmits(["close"]);

// 2. Función para restaurar un usuario
function restaurarUsuario(usuario) {
  if (
    confirm(
      `¿Estás seguro de que quieres restaurar a ${usuario.perfil_usuario.primer_nombre}?`
    )
  ) {
    router.post(
      route("usuarios.restore", { id: usuario.id }),
      {},
      {
        preserveScroll: true, // Evita que la página salte
        onSuccess: () => {
          // Opcional: podrías emitir un evento para notificar al padre que la lista cambió
        },
      }
    );
  }
}

// 3. Función para eliminar un usuario permanentemente
function eliminarPermanentemente(usuario) {
  if (
    confirm(
      `¡ADVERTENCIA! Esta acción es irreversible y eliminará todos los datos de ${usuario.perfil_usuario.primer_nombre}. ¿Continuar?`
    )
  ) {
    router.delete(route("usuarios.forceDestroy", { id: usuario.id }), {
      preserveScroll: true,
    });
  }
}
</script>

<template>
  <Transition name="modal-fade" appear>
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm"
      @click.self="$emit('close')"
    >
      <Transition name="modal-slide" appear>
        <div
          class="relative bg-gray-900 rounded-xl shadow-lg w-full max-w-4xl text-gray-300 overflow-hidden"
        >
          <!-- Encabezado -->
          <div
            class="px-6 py-4 border-b border-gray-800 flex justify-between items-center"
          >
            <h3 class="text-xl font-semibold text-gray-100 flex items-center">
              <span class="material-symbols-rounded align-middle mr-2 text-primary"
                >delete</span
              >
              Papelera de Usuarios
            </h3>
            <button
              @click="$emit('close')"
              class="text-gray-400 hover:text-white focus:outline-none"
            >
              <span class="material-symbols-rounded align-middle">close</span>
            </button>
          </div>

          <!-- Contenido y Tabla -->
          <div class="p-6 max-h-[70vh] overflow-y-auto">
            <div v-if="usuariosEnPapelera.length > 0">
              <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs text-gray-400 uppercase bg-gray-800">
                  <tr>
                    <th scope="col" class="px-4 py-3">Nombre</th>
                    <th scope="col" class="px-4 py-3">Correo</th>
                    <th scope="col" class="px-4 py-3">Fecha de Eliminación</th>
                    <th scope="col" class="px-4 py-3 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="usuario in usuariosEnPapelera"
                    :key="usuario.id"
                    class="border-b border-gray-800 hover:bg-gray-800/50"
                  >
                    <td class="px-4 py-3 font-medium text-gray-200">
                      {{ usuario.perfil_usuario.primer_nombre }}
                      {{ usuario.perfil_usuario.primer_apellido }}
                    </td>
                    <td class="px-4 py-3">{{ usuario.perfil_usuario.correo }}</td>
                    <td class="px-4 py-3">{{ formatFecha(usuario.deleted_at) }}</td>
                    <td class="px-4 py-3 flex justify-end gap-2">
                      <button
                        @click="restaurarUsuario(usuario)"
                        class="font-medium text-green-500 hover:underline"
                      >
                        Restaurar
                      </button>
                      <button
                        @click="eliminarPermanentemente(usuario)"
                        class="font-medium text-red-500 hover:underline"
                      >
                        Eliminar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- Mensaje si la papelera está vacía -->
            <div v-else class="text-center py-10 text-gray-500">
              <span class="material-symbols-rounded text-5xl">recycling</span>
              <p class="mt-2">La papelera está vacía.</p>
            </div>
          </div>

          <!-- Pie del Modal -->
          <div class="px-6 py-3 bg-gray-800 text-right">
            <button
              @click="$emit('close')"
              class="bg-primary text-white rounded-md shadow-sm py-2 px-4 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              Cerrar
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<style scoped>
:deep(.modal-fade-enter-active),
:deep(.modal-fade-leave-active) {
  transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
:deep(.modal-fade-enter-from),
:deep(.modal-fade-leave-to) {
  opacity: 0;
}
:deep(.modal-slide-enter-active),
:deep(.modal-slide-leave-active) {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
:deep(.modal-slide-enter-from),
:deep(.modal-slide-leave-to) {
  opacity: 0;
  transform: translateY(30px) scale(0.97);
}
</style>
