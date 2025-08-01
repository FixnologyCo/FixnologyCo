<template>
  <Transition name="modal-fade" appear>
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm"
      @click.self="$emit('close')"
    >
      <Transition name="modal-slide" appear>
        <div
          v-if="user"
          class="relative bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl text-gray-300 overflow-hidden"
        >
          <div
            class="px-6 py-4 border-b border-gray-800 flex justify-between items-center"
          >
            <h3 class="text-xl font-semibold text-gray-100 flex items-center">
              <span class="material-symbols-rounded align-middle mr-2 text-primary"
                >person</span
              >
              Detalles del Usuario
            </h3>
            <button
              @click="$emit('close')"
              class="text-gray-400 hover:text-white focus:outline-none"
            >
              <span class="material-symbols-rounded align-middle">close</span>
            </button>
          </div>
          <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <h4 class="font-semibold text-lg mb-4 text-primary flex items-center">
                <span class="material-symbols-rounded align-middle mr-2">badge</span>
                Información Personal
              </h4>
              <div class="flex items-center gap-6 mb-6">
                <div
                  class="relative w-24 h-24 rounded-full overflow-hidden shadow-md border-2 border-primary"
                >
                  <img
                    :src="user.perfil_usuario.ruta_foto"
                    alt="Foto de perfil"
                    class="object-cover w-full h-full"
                  />
                </div>
                <div>
                  <p class="text-lg font-bold text-gray-100">
                    {{ user.perfil_usuario.primer_nombre }}
                    {{ user.perfil_usuario.primer_apellido }}
                  </p>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                </div>
              </div>
              <p class="mb-2">
                <strong class="text-gray-400">Documento:</strong>
                {{ user.numero_documento }}
              </p>
              <p class="mb-2">
                <strong class="text-gray-400">Teléfono:</strong>
                {{ user.perfil_usuario.indicativo.codigo_pais }}
                {{ user.perfil_usuario.telefono }}
              </p>
              <p class="mb-2">
                <strong class="text-gray-400">Residencia:</strong>
                {{ user.perfil_usuario.direccion_residencia }},
                {{ user.perfil_usuario.barrio_residencia }}
              </p>
            </div>
            <div>
              <h4 class="font-semibold text-lg mb-4 text-primary flex items-center">
                <span class="material-symbols-rounded align-middle mr-2">work</span>
                Información del Empleado
              </h4>
              <p class="mb-2">
                <strong class="text-gray-400">Establecimiento:</strong>
                {{ user.establecimientos?.nombre_establecimiento }}
              </p>
              <p class="mb-2">
                <strong class="text-gray-400">Cargo:</strong>
                {{ user.perfil_empleado.cargo }}
              </p>
              <p class="mb-2">
                <strong class="text-gray-400">Salario:</strong> ${{
                  new Intl.NumberFormat("es-CO").format(user.perfil_empleado.salario_base)
                }}
              </p>
              <p class="mb-2">
                <strong class="text-gray-400">Tipo de Contrato:</strong>
                {{ user.perfil_empleado.tipo_contrato }}
              </p>
              <p class="mb-2">
                <strong class="text-gray-400">Fecha de Ingreso:</strong>
                {{
                  new Date(user.perfil_empleado.fecha_ingreso).toLocaleDateString("es-CO")
                }}
              </p>
              <p>
                <strong class="text-gray-400">Estado:</strong>
                <span
                  class="px-2 py-1 text-xs font-medium rounded-full"
                  :class="getEstadoClass(user.perfil_empleado.estado.tipo_estado)"
                  >{{ user.perfil_empleado.estado.tipo_estado }}</span
                >
              </p>
            </div>
          </div>
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

<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
  user: {
    type: Object,
    // Ya no es requerido, puede ser null cuando el modal está cerrado
  },
  isOpen: {
    type: Boolean,
    required: true,
  },
});

defineEmits(["close"]);

const getEstadoClass = (estado) => {
  if (estado === "Activo") return "bg-green-700 text-green-300";
  if (estado === "Inactivo" || estado === "Retirado") return "bg-red-700 text-red-300";
  if (estado === "Suspendido") return "bg-yellow-700 text-yellow-300";
  return "bg-gray-700 text-gray-300";
};
</script>

<style scoped>
/* AHORA ESTO FUNCIONARÁ */
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
