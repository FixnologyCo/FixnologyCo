<script setup>
const searchTerm = defineModel("searchTerm");
</script>
<template>
  <div
    class="flex items-center gap-2 border p-1 rounded-md bg-mono-negro_opacity_medio backdrop-blur-xl w-[70%]"
  >
    <span class="material-symbols-rounded text-[18px]">explore</span>
    <input
      type="search"
      name="search"
      placeholder="Buscar usuario"
      id=""
      v-model="searchTerm"
      class="bg-transparent outline-none w-full"
    />
    <!-- <p>|</p>
    <div class="flex gap-1 text-[12px] hover:text-primary cursor-pointer">
      <span class="material-symbols-rounded text-[16px]">ar_on_you</span>
      <p>Escanear</p>
    </div> -->
  </div>
</template>
