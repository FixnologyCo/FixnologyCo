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
  getInicialesUsuario: Function,
  getEstadoClass: Function,
});
const selected = defineModel("selectedUserIds");
const emit = defineEmits(["openDetails"]);
const { getEstadoClass } = useEstadoClass();
</script>

<template>
  <TransitionGroup tag="div" name="list" class="cardsUsuarios">
    <div
      v-for="usuario in users"
      :key="usuario.id"
      class="contCard relative my-2 flex items-center gap-5 justify-between bg-mono-negro_opacity_medio backdrop-blur-xl p-3 rounded-[10px]"
    >
      <div
        class="grid place-content-center foto w-[75px] h-[75px] rounded-[10px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
      >
        <template v-if="getFotoUrlCompleta(usuario)">
          <div class="relative group w-[75px] h-[75px]">
            <div
              class="h-3 w-3 rounded-full absolute -top-1 -left-1"
              :class="
                usuario.tiene_sesion_activa === true
                  ? 'bg-semaforo-verde shadow-[0px_10px_20px] shadow-semaforo-verde'
                  : 'bg-semaforo-rojo shadow-[0px_10px_20px] shadow-semaforo-rojo'
              "
            ></div>
            <img
              :src="getFotoUrlCompleta(usuario)"
              class="rounded-[10px] border-2 w-full h-full object-cover shadow-lg"
            />
          </div>
        </template>

        <template v-else>
          <div class="relative group w-[75px] h-[75px]">
            <div
              class="h-3 w-3 rounded-full absolute -top-1 -left-1"
              :class="
                usuario.tiene_sesion_activa === true
                  ? 'bg-semaforo-verde shadow-[0px_10px_20px] shadow-semaforo-verde'
                  : 'bg-semaforo-rojo shadow-[0px_10px_20px] shadow-semaforo-rojo'
              "
            ></div>
            <p
              class="h-full w-full flex justify-center items-center text-[20px] font-semibold border-2 rounded-[10px]"
            >
              {{ getInicialesUsuario(usuario) }}
            </p>
          </div>
        </template>
      </div>
      <div class="detalles w-[40%]">
        <div class="nombre flex gap-1 items-center">
          <h2
            class="text-[20px] font-medium"
            v-html="
              highlightMatch(
                `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.segundo_nombre} ${usuario.perfil_usuario.primer_apellido} ${usuario.perfil_usuario.segundo_apellido}`
              )
            "
          ></h2>
          <div class="grid place-items-center" v-if="usuario.google_id === null">
            <span class="material-symbols-rounded text-[18px] text-gray-700"
              >verified_off</span
            >
          </div>
          <div
            class="grid place-items-center text-secundary-default dark:text-mono-blanco"
            v-else
          >
            <span class="material-symbols-rounded text-[18px] text-secondary"
              >verified</span
            >
          </div>
        </div>
        <div class="flex -mt-2 justify-between items-end">
          <p
            class="text-secundary-light text-[14px]"
            v-html="highlightMatch(`${usuario.perfil_usuario.correo}`)"
          ></p>
          <p
            class="text-secundary-light text-[14px]"
            v-html="
              highlightMatch(
                `${usuario.perfil_usuario.indicativo.codigo_pais} ${usuario.perfil_usuario.telefono}`
              )
            "
          ></p>
        </div>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-1">
            <div
              class="w-3 h-2 rounded-full"
              :class="getEstadoClass(usuario.perfil_usuario.estado.tipo_estado)"
            ></div>
            <span
              class="text-secundary-light text-[14px]"
              v-html="highlightMatch(`${usuario.perfil_usuario.estado.tipo_estado}`)"
            ></span>
          </div>
          <p
            class="text-secundary-light text-[14px]"
            v-html="
              highlightMatch(
                `${usuario.perfil_usuario.barrio_residencia}, ${usuario.perfil_usuario.ciudad_residencia}`
              )
            "
          ></p>
          <p
            class="text-secundary-light text-[14px]"
            v-html="
              highlightMatch(
                `${usuario.establecimiento_asignado.facturas[0].dias_restantes} Días`
              )
            "
          ></p>
        </div>
      </div>
      <p class="text-[50px] font-thin">|</p>
      <div
        class="grid place-content-center foto w-[60px] h-[60px] rounded-[10px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
      >
        <template v-if="getFotoEstablecimientoUrlCompleta(usuario)">
          <div class="relative group w-[60px] h-[60px]">
            <img
              :src="getFotoEstablecimientoUrlCompleta(usuario)"
              class="rounded-[10px] border shadowM w-full h-full object-cover shadow-lg"
            />
          </div>
        </template>

        <template v-else>
          <div class="relative group w-[60px] h-[60px]">
            <p
              class="h-full w-full flex justify-center items-center text-[20px] font-semibold border shadowM rounded-[10px]"
            >
              {{ getInicialesEstablecimiento(usuario) }}
            </p>
          </div>
        </template>
      </div>
      <div class="detalles w-[40%]">
        <div class="nombre flex gap-1 items-center">
          <h2
            class="text-[20px] font-medium"
            v-html="
              highlightMatch(`${usuario.establecimiento_asignado.nombre_establecimiento}`)
            "
          ></h2>
        </div>
        <div class="flex -mt-2 justify-between items-end">
          <p
            class="text-secundary-light text-[14px]"
            v-html="
              highlightMatch(`${usuario.establecimiento_asignado.token.token_activacion}`)
            "
          ></p>
          <p
            class="text-secundary-light text-[14px]"
            v-html="
              highlightMatch(`${usuario.establecimiento_asignado.tipo_establecimiento}`)
            "
          ></p>
        </div>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-1">
            <div
              class="w-3 h-2 rounded-full"
              :class="getEstadoClass(usuario.establecimiento_asignado.estado.tipo_estado)"
            ></div>
            <span
              class="text-secundary-light text-[14px]"
              v-html="
                highlightMatch(`${usuario.establecimiento_asignado.estado.tipo_estado}`)
              "
            ></span>
          </div>
          <p
            class="text-secundary-light text-[14px]"
            v-html="
              highlightMatch(
                `${usuario.establecimiento_asignado.aplicacion_web.nombre_app} - ${usuario.establecimiento_asignado.aplicacion_web.membresia.nombre_membresia}`
              )
            "
          ></p>

          <div
            v-if="
              usuario.establecimiento?.facturas &&
              usuario.establecimiento?.facturas.length > 0
            "
          >
            <p
              class="p-1 rounded-md text-[14px] font-bold"
              :class="
                getEstadoClass(
                  usuario.establecimiento_asignado?.facturas[0].estado.tipo_estado
                )
              "
              v-html="
                highlightMatch(
                  formatCOP(
                    `${usuario.establecimiento_asignado?.facturas[0].monto_total}`
                  )
                )
              "
            ></p>
          </div>

          <div v-else>
            <div v-if="usuario" class="text-sm text-gray-400 mt-2">
              {{ usuario?.perfil_empleado?.cargo }} -
              <strong class="text-primary font-semibold">
                {{ usuario.establecimiento_asignado?.nombre_establecimiento }}
              </strong>
            </div>
            <div v-else class="text-sm text-gray-500">Sin Información :c</div>
          </div>
        </div>
      </div>
      <p class="text-[50px] font-thin">|</p>

      <div class="flex justify-center items-center" v-if="isSelectionModeActive">
        <label class="relative flex justify-center items-center cursor-pointer group">
          <input
            class="peer sr-only"
            type="checkbox"
            :value="usuario.id"
            v-model="selected"
            @click.stop
          />
          <div
            class="w-5 h-5 rounded-md bg-white border-2 border-primary transition-all duration-300 ease-in-out peer-checked:bg-gradient-to-br from-primary to-secondary peer-checked:border-0 peer-checked:rotate-12 after:content-[''] after:absolute after:top-1/2 after:left-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-4 after:h-4 after:opacity-0 after:bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS13aWR0aD0iMyIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSIyMCA2IDkgMTcgNCAxMiI+PC9wb2x5bGluZT48L3N2Zz4=')] after:bg-contain after:bg-no-repeat peer-checked:after:opacity-100 after:transition-opacity after:duration-300 hover:shadow-[0_0_15px_rgba(168,85,247,0.5)]"
          ></div>
        </label>
      </div>
      <div class="" v-else>
        <BtnSecundario
          @click="isSelectionModeActive ? null : emit('openDetails', usuario)"
          class="material-symbols-rounded border-none hover:text-primary hover:-translate-y-1"
        >
          info</BtnSecundario
        >
      </div>
    </div></TransitionGroup
  >
</template>
<style scoped>
/* ✅ 2. Añade estas clases para la animación de la lista */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
.list-leave-active {
  position: absolute;
}
</style>
