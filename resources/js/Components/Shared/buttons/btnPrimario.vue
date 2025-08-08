<template>
  <button class="w-full flex items-center justify-center gap-1 py-2.5 px-5 text-[14px] font-semibold bg-primary shadow-[2px_8px_20px] shadow-primary rounded-md 
    hover:bg-secondary 
    hover:shadow-secondary 
    hover:-translate-y-1 
    active:bg-transparent 
    active:border
    active:text-primary
    active:shadow-none
    active:scale-90">
    <slot></slot>
  </button>
</template>
