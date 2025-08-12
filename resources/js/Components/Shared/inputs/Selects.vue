<script setup>
import { ref, computed } from "vue";

const model = defineModel();

const props = defineProps({
  options: {
    type: Array,
    required: true,
  },
  placeholder: {
    type: String,
    default: "Selecciona una opción",
  },
  label: { type: String, required: true },
  error: { type: String, default: null },
});

const isActive = ref(false);
const searchTerm = ref("");

// ✅ LÓGICA DE FILTRADO MÁS ROBUSTA
const filteredOptions = computed(() => {
  // Si no hay opciones, devuelve un array vacío para evitar errores.
  if (!props.options) {
    return [];
  }

  if (!searchTerm.value) {
    return props.options;
  }

  return props.options.filter((option) => {
    // Se asegura de que la opción sea un objeto y tenga la propiedad 'text' antes de filtrar.
    return (
      option &&
      option.text &&
      String(option.text).toLowerCase().includes(searchTerm.value.toLowerCase())
    );
  });
});

// ✅ PROPIEDAD COMPUTADA MÁS SEGURA
const selectedOptionText = computed(() => {
  if (!props.options || model.value == null) {
    return props.placeholder;
  }
  const selectedOption = props.options.find(
    (option) => option && option.id === model.value
  );
  return selectedOption ? selectedOption.text : props.placeholder;
});

function selectOption(option) {
  model.value = option.id;
  isActive.value = false;
  searchTerm.value = "";
}
</script>

<template>
  <div class="wrapper w-full outline-none cursor-pointer relative" tabindex="0">
    <div class="contador-label flex items-center justify-between">
      <label class="my-[5px] text-[14px] dark:text-mono-blanco">{{ label }}</label>
      <p class="text-[10px] text-secundary-light">
        {{ (model || "").length }} disponibles
      </p>
    </div>
    <div
      class="select-btn px-2 py-1 bg-transparent border rounded-md border-secundary-light flex items-center justify-between"
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
    <div
      class="content w-full bg-mono-negro_opacity_medio p-2 mt-2 rounded-md border border-secundary-light"
      :class="isActive === true ? 'block' : 'hidden'"
    >
      <div class="search">
        <span class="material-symbols-rounded">search</span>
        <input
          v-model="searchTerm"
          spellcheck="false"
          type="text"
          placeholder="Buscar..."
        />
      </div>
      <ul
        v-if="filteredOptions.length > 0"
        class="options overflow-auto scrollbar-custom max-h-48 my-1 pr-1"
      >
        <li
          class="hover:bg-mono-blanco_opacity p-1.5 flex items-center rounded-md"
          v-for="option in filteredOptions"
          :key="option.id"
          @click="selectOption(option)"
          :class="{ 'bg-primary text-white': option.id === model }"
        >
          {{ option.text }}
        </li>
      </ul>
      <p v-else class="p-4 text-center text-sm text-gray-500">
        No se encontraron resultados
      </p>
    </div>
  </div>
  <span v-if="error" class="text-sm text-universal-naranja">
    {{ error }}
  </span>
</template>
ite
