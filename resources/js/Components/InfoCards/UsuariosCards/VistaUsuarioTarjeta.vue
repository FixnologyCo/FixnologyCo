<script setup>
import {
  getInicialesEstablecimiento,
  getInicialesUsuario,
} from "@/Utils/inicialesNombres";
import {
  getFotoEstablecimientoUrlCompleta,
  getFotoUrlCompleta,
} from "@/Utils/ImagenUsuarios";
import { formatCOP } from "@/Utils/formateoMoneda";
import useEstadoClass from "@/Composables/useEstado";
import BtnSecundario from "@/Components/Shared/buttons/btnSecundario.vue";
defineProps({
  users: Array,
  isSelectionModeActive: Boolean,
  selectedUserIds: Array,
  highlightMatch: Function,
  getInitials: Function,
});
const selected = defineModel("selectedUserIds");
const emit = defineEmits(["openDetails"]);
const { getEstadoClass } = useEstadoClass();
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div
      v-for="usuario in users"
      :key="usuario.id"
      class="contCard relative bg-mono-negro_opacity_medio backdrop-blur-xl p-3 rounded-[10px] flex flex-col gap-3 transition-all duration-300"
    >
      <div v-if="isSelectionModeActive" class="absolute top-2 left-2 z-20">
        <input
          type="checkbox"
          :value="usuario.id"
          v-model="selected"
          @click.stop
          class="form-checkbox h-5 w-5 rounded bg-gray-800 border-gray-600 text-primary focus:ring-primary"
        />
      </div>
      <div
        @click="isSelectionModeActive ? null : emit('openDetails', usuario)"
        class="w-full h-full flex flex-col items-center text-center gap-3"
        :class="{ 'cursor-pointer': !isSelectionModeActive }"
      >
        <!-- Contenido de la tarjeta adaptado para la vista de cuadrícula -->
        <div class="relative flex-shrink-0">
          <div
            class="w-3.5 h-3.5 absolute -top-1 -left-1 z-10 border-2 border-gray-900 rounded-full"
            :class="
              usuario.tiene_sesion_activa ? 'bg-semaforo-verde' : 'bg-semaforo-rojo'
            "
          ></div>
          <img
            v-if="usuario.perfil_usuario.ruta_foto"
            :src="usuario.perfil_usuario.ruta_foto"
            alt="Foto"
            class="w-24 h-24 rounded-full object-cover shadow-lg"
          />
          <div
            v-else
            class="w-24 h-24 rounded-full bg-primary flex items-center justify-center font-bold text-3xl"
          >
            {{ getInicialesUsuario(usuario) }}
          </div>
        </div>
        <div class="datosPersonales w-full">
          <h3
            class="font-semibold dark:text-mono-blanco text-lg"
            v-html="
              highlightMatch(
                `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.primer_apellido}`
              )
            "
          ></h3>
          <p
            class="text-sm text-gray-400"
            v-html="highlightMatch(usuario.perfil_usuario.correo)"
          ></p>
        </div>
      </div>
    </div>
  </div>
</template>
