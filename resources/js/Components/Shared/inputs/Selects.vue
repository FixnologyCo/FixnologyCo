<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from "vue";

const model = defineModel();

const props = defineProps({
  options: { type: Array, required: true },
  placeholder: { type: String, default: "Selecciona una opción" },
  label: { type: String, required: true },
  error: { type: String, default: null },
});

const emit = defineEmits(["blur"]);

// --- ESTADO DEL COMPONENTE ---
const isActive = ref(false);
const searchTerm = ref("");
const selectWrapper = ref(null); // Ref para el botón/contenedor principal
const dropdownContent = ref(null); // Ref para el panel de opciones teletransportado
const dropdownStyle = ref({}); // Estilos para posicionar el dropdown

// --- LÓGICA DE FILTRADO Y VISUALIZACIÓN ---
const filteredOptions = computed(() => {
  if (!props.options) return [];
  if (!searchTerm.value) return props.options;
  return props.options.filter(
    (option) =>
      option &&
      option.text &&
      String(option.text).toLowerCase().includes(searchTerm.value.toLowerCase())
  );
});

const selectedOptionText = computed(() => {
  if (!props.options || model.value == null) return props.placeholder;
  const selectedOption = props.options.find(
    (option) => option && option.id === model.value
  );
  return selectedOption ? selectedOption.text : props.placeholder;
});

// --- LÓGICA DE POSICIONAMIENTO DEL DROPDOWN ---
function updateDropdownPosition() {
  if (selectWrapper.value) {
    const rect = selectWrapper.value.getBoundingClientRect();
    dropdownStyle.value = {
      position: "fixed", // Usamos 'fixed' para un posicionamiento más fiable con Teleport
      left: `${rect.left}px`,
      top: `${rect.bottom + 8}px`,
      width: `${rect.width}px`,
    };
  }
}

// --- MANEJO DE ACCIONES ---
function selectOption(option) {
  model.value = option.id;
  isActive.value = false;
  searchTerm.value = "";
}

function closeDropdown() {
  isActive.value = false;
}

// ✅ LÓGICA MEJORADA PARA CERRAR AL HACER CLIC FUERA
function handleClickOutside(event) {
  if (
    selectWrapper.value &&
    !selectWrapper.value.contains(event.target) &&
    dropdownContent.value &&
    !dropdownContent.value.contains(event.target)
  ) {
    closeDropdown();
  }
}

watch(isActive, (newValue) => {
  if (newValue) {
    nextTick(() => {
      updateDropdownPosition();
      // Añadimos el listener cuando el dropdown se abre
      document.addEventListener("mousedown", handleClickOutside);
    });
  } else {
    // Quitamos el listener cuando se cierra
    document.removeEventListener("mousedown", handleClickOutside);
  }
});

onMounted(() => {
  window.addEventListener("resize", updateDropdownPosition);
  window.addEventListener("scroll", updateDropdownPosition, true); // También al hacer scroll
});
onUnmounted(() => {
  window.removeEventListener("resize", updateDropdownPosition);
  window.removeEventListener("scroll", updateDropdownPosition, true);
  document.removeEventListener("mousedown", handleClickOutside);
});

const inputContainerClass = computed(() => {
  return [
    "w-full p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px]",
    props.error ? "border-semaforo-rojo" : "border-secundary-light",
  ];
});
</script>

<template>
  <div class="w-full">
    <div class="contador-label flex items-center justify-between">
      <label class="my-[5px] text-[14px] dark:text-mono-blanco">{{ label }}</label>
      <p class="text-[10px] text-secundary-light">{{ options.length }} disponibles</p>
    </div>

    <!-- ✅ Se elimina @focusout de aquí -->
    <div ref="selectWrapper" class="relative" tabindex="0">
      <div
        :class="inputContainerClass"
        class="select-btn cursor-pointer px-2 py-1 bg-transparent border rounded-md border-secundary-light flex items-center justify-between"
        @click="isActive = !isActive"
      >
        <div class="flex gap-2 items-center">
          <span class="material-symbols-rounded text-[20px] text-primary">stack</span>
          <span>{{ selectedOptionText }}</span>
        </div>
        <span
          class="material-symbols-rounded transition-transform"
          :class="{ 'rotate-180': isActive }"
          >expand_more</span
        >
      </div>

      <Teleport to="body">
        <Transition name="fade">
          <div
            ref="dropdownContent"
            v-if="isActive"
            class="content cursor-pointer bg-mono-negro text-mono-blanco p-2 rounded-md border border-secundary-light z-50"
            :style="dropdownStyle"
          >
            <div class="search relative">
              <span
                class="material-symbols-rounded absolute left-2 top-1/2 -translate-y-1/2 text-gray-400"
                >search</span
              >
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar..."
                class="w-full bg-gray-800/50 rounded-md pl-9 pr-2 py-1.5 text-sm focus:border-primary focus:ring-primary border-gray-700"
              />
            </div>
            <ul
              v-if="filteredOptions.length > 0"
              class="options overflow-auto scrollbar-custom max-h-48 my-1 pr-1"
            >
              <li
                v-for="option in filteredOptions"
                :key="option.id"
                @click="selectOption(option)"
                class="hover:bg-mono-blanco_opacity p-1.5 flex items-center rounded-md"
                :class="{ 'bg-primary text-white': option.id === model }"
              >
                {{ option.text }}
              </li>
            </ul>
            <p v-else class="p-4 text-center text-sm text-gray-500">
              No se encontraron resultados
            </p>
          </div>
        </Transition>
      </Teleport>
    </div>

    <span v-if="error" class="text-sm text-universal-naranja">{{ error }}</span>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
