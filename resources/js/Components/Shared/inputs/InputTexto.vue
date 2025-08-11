<script setup>
import { computed } from 'vue';

// 1. defineModel() es la forma moderna de crear un v-model.
//    Recibirá y actualizará el valor desde el componente padre.
const model = defineModel();

// 2. Definimos todas las propiedades que el componente necesita.
const props = defineProps({
  label: { type: String, required: true },
  icon: { type: String, default: 'edit' },
  placeholder: { type: String, default: '' },
  maxLength: { type: Number, required: true },
  error: { type: String, default: null },
  type: { type: String, default: 'text' } // Para poder usarlo con 'password', 'email', etc.
});

// 3. El componente emite un evento 'blur' que el padre puede escuchar.
const emit = defineEmits(['blur']);

// 4. Propiedad computada para manejar el estilo del borde dinámicamente.
const inputContainerClass = computed(() => {
  return [
    'w-full p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px]',
    props.error ? 'border-universal-naranja' : 'border-secundary-light'
  ];
});
</script>

<template>
  <div class="w-full">
    <div class="contador-label flex items-center justify-between">
      <label class="my-[5px] text-[14px] dark:text-mono-blanco">{{ label }}</label>
      <p class="text-[10px] text-secundary-light">
        {{ (model || '').length }} / {{ maxLength }}
      </p>
    </div>

    <div :class="inputContainerClass">
      <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">{{ icon }}</span>
      <input
        :type="type"
        :placeholder="placeholder"
        v-model="model"
        @blur="emit('blur')"
        class="w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
      />
    </div>
    
    <span v-if="error" class="text-sm text-universal-naranja">
      {{ error }}
    </span>
  </div>
</template>
