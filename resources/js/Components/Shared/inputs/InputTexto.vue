<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const isAuthenticated = computed(() => !!page.props.auth.user);

const model = defineModel();

const props = defineProps({
  label: { type: String, required: true },
  icon: { type: String, default: "edit" },
  placeholder: { type: String, default: "" },
  maxLength: { type: Number, required: true },
  error: { type: String, default: null },
  type: { type: String, default: "text" },
});

const emit = defineEmits(["blur", "input"]);

const inputContainerClass = computed(() => {
  return [
    "w-full p-[3px] flex items-center gap-[8px] transition-all rounded-[8px] border-[1px]",
    props.error ? "border-semaforo-rojo" : "border-secundary-light",
  ];
});
</script>

<template>
  <div class="w-full">
    <div class="contador-label flex items-center justify-between">
      <label
        class="my-[5px] text-[14px]"
        :class="[
          model.length >= 1 ? 'text-semaforo-verde' : 'text-secundary-light',
          error ? 'text-semaforo-rojo' : 'text-secundary-light',
        ]"
        >{{ label }}</label
      >
      <p
        class="text-[10px]"
        :class="[
          model.length === maxLength ? 'text-semaforo-rojo' : 'text-secundary-light',
        ]"
      >
        {{ (model || "").length }} / {{ maxLength }}
      </p>
    </div>

    <div
      :class="[
        inputContainerClass,
        model.length >= 1 ? 'border-semaforo-verde' : 'border-secundary-light',
      ]"
    >
      <span
        class="material-symbols-rounded"
        :class="[
          'text-[20px] pl-[5px]',
          isAuthenticated ? 'text-primary' : '',
          model.length >= 1 ? 'text-semaforo-verde' : 'text-universal-naranja',
          error ? 'text-semaforo-rojo' : '',
        ]"
        >{{ icon }}</span
      >
      <input
        :type="type"
        :placeholder="placeholder"
        v-model="model"
        @blur="emit('blur')"
        @input="emit('input', $event)"
        class="w-full outline-none font-normal bg-transparent dark:text-mono-blanco placeholder:text-[14px]"
      />
    </div>

    <span
      v-if="error"
      :class="[model.length >= 1 ? 'hidden' : '']"
      class="text-sm flex items-center mt-1 gap-1 text-universal-naranja"
    >
      <span class="material-symbols-rounded text-[14px]">info</span> {{ error }}
    </span>
  </div>
</template>
