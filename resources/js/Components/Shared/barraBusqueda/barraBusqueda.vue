<script setup>
const searchTerm = defineModel("searchTerm");

const props = defineProps({
  placeholder: { type: String, default: "" },
});
</script>
<template>
  <div
    class="w-full p-[8px] flex items-center gap-[8px] transition-all rounded-[8px] border-[1px]"
  >
    <span class="material-symbols-rounded text-[20px] pl-[5px]">filter_center_focus</span>
    <input
      type="search"
      name="search"
      :placeholder="placeholder"
      id=""
      v-model="searchTerm"
      class="w-full outline-none font-normal bg-transparent dark:text-mono-blanco placeholder:text-[14px]"
    />
    <!-- <p>|</p>
    <div class="flex gap-1 text-[12px] hover:text-primary cursor-pointer">
      <span class="material-symbols-rounded text-[16px]">ar_on_you</span>
      <p>Escanear</p>
    </div> -->
  </div>
</template>
