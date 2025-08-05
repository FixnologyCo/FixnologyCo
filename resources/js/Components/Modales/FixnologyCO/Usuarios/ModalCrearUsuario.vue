<script setup>
import { defineProps, defineEmits, ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  // Recibimos la lista de establecimientos ya transformada
  establecimientosDisponibles: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["close"]);

// Usamos el helper 'useForm' de Inertia para manejar el estado del formulario
const form = useForm({
  tipo_usuario: "propietario", // Valor por defecto
  primer_nombre: "",
  primer_apellido: "",
  correo: "",
  numero_documento: "",
  password: "",
  establecimiento_id: null,
  cargo: "",
});

const submit = () => {
  form.post(route("usuarios.store"), {
    preserveScroll: true,
    onSuccess: () => {
      emit("close"); // Cierra el modal si el registro es exitoso
      form.reset(); // Limpia el formulario
    },
    // onError se maneja automáticamente mostrando los errores de validación
  });
};
</script>

<template>
  <Transition name="modal-fade" appear>
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm"
      @click.self="$emit('close')"
    >
      <Transition name="modal-slide" appear>
        <form
          @submit.prevent="submit"
          class="relative bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl text-gray-300 overflow-hidden"
        >
          <!-- Encabezado -->
          <div
            class="px-6 py-4 border-b border-gray-800 flex justify-between items-center"
          >
            <h3 class="text-xl font-semibold text-gray-100 flex items-center">
              <span class="material-symbols-rounded align-middle mr-2 text-primary"
                >person_add</span
              >
              Crear Nuevo Usuario
            </h3>
            <button
              type="button"
              @click="$emit('close')"
              class="text-gray-400 hover:text-white focus:outline-none"
            >
              <span class="material-symbols-rounded align-middle">close</span>
            </button>
          </div>

          <!-- Cuerpo del Formulario -->
          <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <!-- Selector de Tipo de Usuario -->
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-2"
                >Tipo de Usuario</label
              >
              <div class="flex gap-4">
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.tipo_usuario"
                    value="propietario"
                    class="form-radio text-primary focus:ring-primary"
                  />
                  <span class="ml-2">Nuevo Propietario</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.tipo_usuario"
                    value="empleado"
                    class="form-radio text-primary focus:ring-primary"
                  />
                  <span class="ml-2">Empleado de Tienda Existente</span>
                </label>
              </div>
            </div>

            <!-- Campos Comunes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="primer_nombre" class="block text-sm font-medium text-gray-400"
                  >Primer Nombre</label
                >
                <input
                  v-model="form.primer_nombre"
                  type="text"
                  id="primer_nombre"
                  class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                />
                <div v-if="form.errors.primer_nombre" class="text-red-500 text-xs mt-1">
                  {{ form.errors.primer_nombre }}
                </div>
              </div>
              <div>
                <label
                  for="primer_apellido"
                  class="block text-sm font-medium text-gray-400"
                  >Primer Apellido</label
                >
                <input
                  v-model="form.primer_apellido"
                  type="text"
                  id="primer_apellido"
                  class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                />
                <div v-if="form.errors.primer_apellido" class="text-red-500 text-xs mt-1">
                  {{ form.errors.primer_apellido }}
                </div>
              </div>
            </div>
            <div>
              <label for="correo" class="block text-sm font-medium text-gray-400"
                >Correo Electrónico</label
              >
              <input
                v-model="form.correo"
                type="email"
                id="correo"
                class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary"
              />
              <div v-if="form.errors.correo" class="text-red-500 text-xs mt-1">
                {{ form.errors.correo }}
              </div>
            </div>
            <div>
              <label
                for="numero_documento"
                class="block text-sm font-medium text-gray-400"
                >Número de Documento</label
              >
              <input
                v-model="form.numero_documento"
                type="text"
                id="numero_documento"
                class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary"
              />
              <div v-if="form.errors.numero_documento" class="text-red-500 text-xs mt-1">
                {{ form.errors.numero_documento }}
              </div>
            </div>
            <div>
              <label for="password" class="block text-sm font-medium text-gray-400"
                >Contraseña</label
              >
              <input
                v-model="form.password"
                type="password"
                id="password"
                class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary"
              />
              <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">
                {{ form.errors.password }}
              </div>
            </div>

            <!-- Campos Específicos para Empleado -->
            <div v-if="form.tipo_usuario === 'empleado'" class="space-y-4">
              <hr class="border-gray-700" />
              <div>
                <label
                  for="establecimiento_id"
                  class="block text-sm font-medium text-gray-400"
                  >Asignar a Establecimiento</label
                >
                <select
                  v-model="form.establecimiento_id"
                  id="establecimiento_id"
                  class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                >
                  <option :value="null" disabled>Selecciona una tienda</option>
                  <!-- Esto ahora funcionará porque el backend envía 'nombre' -->
                  <option
                    v-for="est in establecimientosDisponibles"
                    :key="est.id"
                    :value="est.id"
                  >
                    {{ est.nombre_establecimiento }}
                  </option>
                </select>
                <div
                  v-if="form.errors.establecimiento_id"
                  class="text-red-500 text-xs mt-1"
                >
                  {{ form.errors.establecimiento_id }}
                </div>
              </div>
              <div>
                <label for="cargo" class="block text-sm font-medium text-gray-400"
                  >Cargo del Empleado</label
                >
                <input
                  v-model="form.cargo"
                  type="text"
                  id="cargo"
                  class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                />
                <div v-if="form.errors.cargo" class="text-red-500 text-xs mt-1">
                  {{ form.errors.cargo }}
                </div>
              </div>
            </div>
          </div>

          <!-- Pie del Modal -->
          <div class="px-6 py-4 bg-gray-800 text-right">
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-primary text-white rounded-md shadow-sm py-2 px-4 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
            >
              Crear Usuario
            </button>
          </div>
        </form>
      </Transition>
    </div>
  </Transition>
</template>

<style scoped>
/* ... (tus estilos de transición) ... */
</style>
