<script setup>
import BtnPrimario from "@/Components/Shared/buttons/btnPrimario.vue";
import BtnSecundario from "@/Components/Shared/buttons/btnSecundario.vue";
import { computed } from "vue";

defineProps({
  isOpen: { type: Boolean, required: true },
  title: { type: [String, null], required: true },
  message: { type: [String, null], required: true },
  icon: { type: [String, null], default: "help" },
  confirmText: { type: [String, null], default: "Confirmar" },
  cancelText: { type: [String, null], default: "Cancelar" },
  iconBgClass: { type: [String, null], default: "bg-gray-700" },
});

const emit = defineEmits(["close", "confirm"]);
</script>
<template>
  <Transition name="modal-fade" appear>
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-mono-negro_opacity_medio backdrop-blur-md"
    >
      <button
        type="button"
        @click="$emit('close')"
        class="absolute top-5 right-5 text-gray-400 hover:text-white focus:outline-none"
      ></button>
      <Transition name="modal-slide" appear>
        <div
          class="relative text-center bg-mono-negro rounded-2xl py-10 px-10 h-auto max-h-[80%] w-full max-w-[40%] border border-secundary-light text-mono-blanco overflow-auto scrollbar-custom"
        >
          <div class="flex items-center justify-center">
            <span
              :class="iconBgClass"
              class="material-symbols-rounded border p-5 mr-3 rounded-xl flex justify-center items-center"
              >{{ icon }}</span
            >
            <div class="text-left">
              <h4 class="font-semibold text-[30px] text-mono-blanco">
                {{ title }}
              </h4>
              <p class="text-[18px] text-secundary-light -mt-1 text-justify">
                {{ message }}
              </p>
            </div>
          </div>

          <div class="mt-8 flex gap-3 justify-center items-center flex-shrink-0">
            <BtnSecundario @click="$emit('close')" type="button" class="w-auto py-3 px-5">
              <p class="text-[14px]">Cancelar</p>
            </BtnSecundario>

            <BtnPrimario @click="emit('confirm')" type="submit" class="w-[70%]">
              {{ confirmText }}
            </BtnPrimario>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>
<style scoped>
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
