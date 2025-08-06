<script setup>
import BtnSecundario from "./btnSecundario.vue";

const viewMode = defineModel("viewMode");
</script>

<template>
  <div>
    <!-- 2. Los botones ahora leen y actualizan el 'viewMode' que viene del padre. -->

    <BtnSecundario
      @click="viewMode = 'table'"
      class="material-symbols-rounded border hover:-translate-y-1"
      :class="
        viewMode === 'table'
          ? 'bg-secondary border-secondary text-mono-blanco hover:border-secondary hover:text-mono-blanco'
          : 'border-transparent'
      "
    >
      table</BtnSecundario
    >
    <button
      @click="viewMode = 'list'"
      class="material-symbols-rounded p-2 text-[18px] rounded-xl transition-colors"
      :class="
        viewMode === 'list'
          ? 'bg-primary text-white'
          : 'border border-transparent hover:border-primary'
      "
    >
      lists
    </button>
    <button
      @click="viewMode = 'grid'"
      class="material-symbols-rounded p-2 text-[18px] rounded-xl transition-colors"
      :class="
        viewMode === 'grid'
          ? 'bg-primary text-white'
          : 'border border-transparent hover:border-primary'
      "
    >
      grid_view
    </button>
  </div>
</template>
