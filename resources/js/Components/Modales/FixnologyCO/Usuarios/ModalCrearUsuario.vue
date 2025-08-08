<script setup>
import { defineProps, defineEmits, ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  establecimientosDisponibles: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["close"]);

// --- LÓGICA DE PASOS ---
const currentStep = ref(1);
const totalSteps = 2;

const progressPercentage = computed(() => {
  return ((currentStep.value - 1) / (totalSteps - 1)) * 100;
});

const form = useForm({
  tipo_usuario: "propietario",
  primer_nombre: "",
  primer_apellido: "",
  correo: "",
  numero_documento: "",
  password: "",
  establecimiento_id: null,
  cargo: "",
});

function nextStep() {
  form.clearErrors();
  const fieldsToValidate = [
    "primer_nombre",
    "primer_apellido",
    "correo",
    "numero_documento",
    "password",
  ];
  let hasErrors = false;
  fieldsToValidate.forEach((field) => {
    if (!form[field]) {
      form.setError(field, `El campo es requerido.`);
      hasErrors = true;
    }
  });

  if (!hasErrors && currentStep.value < totalSteps) {
    currentStep.value++;
  }
}

function prevStep() {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
}

// --- LÓGICA DE ENVÍO ---
const submit = () => {
  form.post(route("usuarios.store"), {
    preserveScroll: true,
    onSuccess: () => {
      emit("close");
      form.reset();
      currentStep.value = 1;
    },
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
          class="relative bg-gray-900 rounded-xl shadow-lg w-full max-w-lg text-gray-300 flex flex-col"
        >
          <!-- Barra de Progreso -->
          <div class="w-full bg-gray-800 rounded-t-xl h-1.5">
            <div
              class="bg-primary h-1.5 rounded-t-xl transition-all duration-500 ease-out"
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>

          <!-- Encabezado -->
          <div class="px-6 py-4 flex justify-between items-center flex-shrink-0">
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

          <!-- Cuerpo del Formulario con Transición -->
          <div class="p-6 overflow-hidden">
            <Transition name="slide-fade" mode="out-in">
              <div :key="currentStep" class="space-y-4">
                <!-- PASO 1: DATOS PERSONALES -->
                <div v-if="currentStep === 1">
                  <h4 class="font-semibold text-lg mb-4 text-primary">
                    Datos de la Cuenta
                  </h4>
                  <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label
                          for="primer_nombre"
                          class="block text-sm font-medium text-gray-400"
                          >Primer Nombre</label
                        >
                        <input
                          v-model="form.primer_nombre"
                          type="text"
                          id="primer_nombre"
                          class="input-field"
                        />
                        <div v-if="form.errors.primer_nombre" class="error-message">
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
                          class="input-field"
                        />
                        <div v-if="form.errors.primer_apellido" class="error-message">
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
                        class="input-field"
                      />
                      <div v-if="form.errors.correo" class="error-message">
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
                        class="input-field"
                      />
                      <div v-if="form.errors.numero_documento" class="error-message">
                        {{ form.errors.numero_documento }}
                      </div>
                    </div>
                    <div>
                      <label
                        for="password"
                        class="block text-sm font-medium text-gray-400"
                        >Contraseña</label
                      >
                      <input
                        v-model="form.password"
                        type="password"
                        id="password"
                        class="input-field"
                      />
                      <div v-if="form.errors.password" class="error-message">
                        {{ form.errors.password }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- PASO 2: TIPO DE USUARIO Y DATOS DE EMPLEADO -->
                <div v-if="currentStep === 2">
                  <h4 class="font-semibold text-lg mb-4 text-primary">Tipo de Usuario</h4>
                  <div class="space-y-3">
                    <label
                      class="flex items-center p-3 rounded-md transition-colors"
                      :class="
                        form.tipo_usuario === 'propietario'
                          ? 'bg-primary/10 border border-primary'
                          : 'bg-gray-800/50 border border-transparent hover:border-gray-700'
                      "
                    >
                      <input
                        type="radio"
                        v-model="form.tipo_usuario"
                        value="propietario"
                        class="form-radio text-primary focus:ring-primary bg-gray-700 border-gray-600"
                      />
                      <span class="ml-3">
                        <span class="font-semibold">Nuevo Propietario</span>
                        <p class="text-xs text-gray-400">
                          Creará su propio establecimiento y facturación.
                        </p>
                      </span>
                    </label>
                    <label
                      class="flex items-center p-3 rounded-md transition-colors"
                      :class="
                        form.tipo_usuario === 'empleado'
                          ? 'bg-primary/10 border border-primary'
                          : 'bg-gray-800/50 border border-transparent hover:border-gray-700'
                      "
                    >
                      <input
                        type="radio"
                        v-model="form.tipo_usuario"
                        value="empleado"
                        class="form-radio text-primary focus:ring-primary bg-gray-700 border-gray-600"
                      />
                      <span class="ml-3">
                        <span class="font-semibold">Empleado de Tienda Existente</span>
                        <p class="text-xs text-gray-400">
                          Será asignado a una tienda que ya existe.
                        </p>
                      </span>
                    </label>
                    <div
                      v-if="form.tipo_usuario === 'empleado'"
                      class="space-y-4 pt-4 border-t border-gray-700/50"
                    >
                      <div>
                        <label
                          for="establecimiento_id"
                          class="block text-sm font-medium text-gray-400"
                          >Asignar a Establecimiento</label
                        >
                        <select
                          v-model="form.establecimiento_id"
                          id="establecimiento_id"
                          class="input-field"
                        >
                          <option :value="null" disabled>Selecciona una tienda</option>
                          <option
                            v-for="est in establecimientosDisponibles"
                            :key="est.id"
                            :value="est.id"
                          >
                            {{ est.nombre_establecimiento }}
                          </option>
                        </select>
                        <div v-if="form.errors.establecimiento_id" class="error-message">
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
                          class="input-field"
                        />
                        <div v-if="form.errors.cargo" class="error-message">
                          {{ form.errors.cargo }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </Transition>
          </div>

          <!-- Pie del Modal -->
          <div
            class="px-6 py-4 bg-gray-800/50 flex justify-between items-center flex-shrink-0"
          >
            <button
              v-if="currentStep > 1"
              type="button"
              @click="prevStep"
              class="text-gray-300 rounded-md py-2 px-4 hover:bg-gray-700 transition-colors"
            >
              Atrás
            </button>
            <div v-else></div>
            <button
              v-if="currentStep < totalSteps"
              type="button"
              @click="nextStep"
              class="bg-primary text-white rounded-md shadow-sm py-2 px-4 hover:bg-opacity-90 transition-colors"
            >
              Siguiente
            </button>
            <button
              v-else
              type="submit"
              :disabled="form.processing"
              class="bg-green-600 text-white rounded-md shadow-sm py-2 px-4 hover:bg-green-500 disabled:opacity-50 transition-colors"
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
.input-field {
  @apply mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-white;
}
.error-message {
  @apply text-red-500 text-xs mt-1;
}

/* Transición para el modal completo */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.modal-slide-enter-active,
.modal-slide-leave-active {
  transition: all 0.3s ease-out;
}
.modal-slide-enter-from,
.modal-slide-leave-to {
  transform: translateY(20px);
  opacity: 0;
}

/* Transición para los pasos del formulario */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.25s ease-out;
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
