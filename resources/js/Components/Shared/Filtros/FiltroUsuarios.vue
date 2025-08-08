<script setup>
import { computed } from "vue";

// 1. Recibimos las opciones para los dropdowns y el número de resultados como props.
const props = defineProps({
  filtrosDisponibles: { type: Object, required: true },
  resultsCount: { type: Number, default: 0 },
});

// 2. Usamos defineModel para crear una comunicación de dos vías con el objeto 'filters' del padre.
const filters = defineModel("filters");

// 3. La lógica para saber si hay filtros activos y para resetear vive dentro de este componente.
const isAnyFilterActive = computed(() => {
  if (!filters.value) return false;
  const { estado, orden, aplicacion, membresia, ciudad } = filters.value;
  return (
    estado !== "all" ||
    orden !== "reciente" ||
    aplicacion !== "all" ||
    membresia !== "all" ||
    ciudad !== "all"
  );
});

// 4. Emitimos un evento 'reset' que el padre escuchará.
const emit = defineEmits(["reset"]);

function reset() {
  emit("reset");
}
</script>

<template>
  <div class="filtro w-full">
    <div class="header flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold text-gray-200">Filtros</h1>
      <div
        class="contador bg-gray-800 text-primary font-semibold rounded-full px-3 py-1 text-xs"
      >
        {{ resultsCount }} <span class="text-gray-400">Resultados</span>
      </div>
    </div>
    <div
      class="contenidoFiltro bg-gray-900/50 backdrop-blur-xl border border-gray-800 rounded-lg p-4 space-y-5"
    >
      <!-- Filtro por Estado -->
      <div>
        <label for="filtro-estado" class="block text-sm font-medium text-gray-400 mb-1"
          >Estado cliente</label
        >
        <select
          v-model="filters.estado"
          id="filtro-estado"
          class="w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm appearance-none px-3 py-2 text-white"
        >
          <option value="all">Todos los Estados</option>
          <option
            v-for="estado in filtrosDisponibles.estados"
            :key="estado"
            :value="estado"
          >
            {{ estado }}
          </option>
        </select>
      </div>

      <!-- Filtro por Orden -->
      <div>
        <label for="filtro-orden" class="block text-sm font-medium text-gray-400 mb-1"
          >Ordenar por</label
        >
        <select
          v-model="filters.orden"
          id="filtro-orden"
          class="w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm appearance-none px-3 py-2 text-white"
        >
          <option value="reciente">Más Recientes</option>
          <option value="antiguo">Más Antiguos</option>
          <option value="a-z">Nombre (A-Z)</option>
          <option value="z-a">Nombre (Z-A)</option>
        </select>
      </div>

      <!-- Filtro por Aplicación -->
      <div>
        <label for="filtro-app" class="block text-sm font-medium text-gray-400 mb-1"
          >Aplicación</label
        >
        <select
          v-model="filters.aplicacion"
          id="filtro-app"
          class="w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm appearance-none px-3 py-2 text-white"
        >
          <option value="all">Todas las Apps</option>
          <option v-for="app in filtrosDisponibles.aplicaciones" :key="app" :value="app">
            {{ app }}
          </option>
        </select>
      </div>

      <!-- Filtro por Membresía -->
      <div>
        <label for="filtro-membresia" class="block text-sm font-medium text-gray-400 mb-1"
          >Membresía</label
        >
        <select
          v-model="filters.membresia"
          id="filtro-membresia"
          class="w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm appearance-none px-3 py-2 text-white"
        >
          <option value="all">Todas las Membresías</option>
          <option v-for="mem in filtrosDisponibles.membresias" :key="mem" :value="mem">
            {{ mem }}
          </option>
        </select>
      </div>

      <!-- Filtro por Ciudad -->
      <div>
        <label for="filtro-ciudad" class="block text-sm font-medium text-gray-400 mb-1"
          >Ciudad</label
        >
        <select
          v-model="filters.ciudad"
          id="filtro-ciudad"
          class="w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm appearance-none px-3 py-2 text-white"
        >
          <option value="all">Todas las Ciudades</option>
          <option
            v-for="ciudad in filtrosDisponibles.ciudades"
            :key="ciudad"
            :value="ciudad"
          >
            {{ ciudad }}
          </option>
        </select>
      </div>

      <!-- Botón para restablecer los filtros -->
      <div v-if="isAnyFilterActive" class="pt-2">
        <button
          @click="reset"
          class="w-full py-2 px-4 text-sm font-medium text-primary bg-primary/10 rounded-md hover:bg-primary/20 transition-colors flex items-center justify-center gap-2"
        >
          <span class="material-symbols-rounded text-base">close</span>
          Restablecer Filtros
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Añade una flecha personalizada a los selectores para un look más moderno */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}
</style>
