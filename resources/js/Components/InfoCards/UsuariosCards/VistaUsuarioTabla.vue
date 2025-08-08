<!-- <script setup>
const selectedUserIds = defineModel("selectedUserIds");
</script>

<template>
  <table class="w-full text-sm text-left text-gray-300">
    <thead class="text-xs text-gray-200 uppercase bg-gray-700/50">
      <tr>
        <th v-if="isSelectionModeActive" class="px-2 py-3 w-4">Seleccionar</th>
        <th scope="col" class="px-6 py-3">Usuario</th>
        <th scope="col" class="px-6 py-3">Contacto</th>
        <th scope="col" class="px-6 py-3">Rol</th>
        <th scope="col" class="px-6 py-3">Establecimiento</th>
        <th scope="col" class="px-6 py-3">
          <span class="sr-only">Acciones</span>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="usuario in filteredUsers"
        :key="usuario.id"
        @click="openUserDetailsModal(usuario)"
        class="border-b border-gray-700 hover:bg-gray-800/50 cursor-pointer"
      >
        <td v-if="isSelectionModeActive" class="px-2">
          <input
            type="checkbox"
            :value="usuario.id"
            v-model="selectedUserIds"
            @click.stop
            class="form-checkbox rounded bg-gray-700 border-gray-60 t-primary focus:ring-primary"
          />
        </td>
        <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
          <div class="flex items-center gap-3">
            <img
              v-if="usuario.perfil_usuario.ruta_foto"
              :src="usuario.perfil_usuario.ruta_foto"
              alt="Foto"
              class="w-10 h-10 rounded-full object-cover"
            />
            <div
              v-else
              class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-bold"
            >
              {{ getInicialesUsuario(usuario) }}
            </div>
            <span
              v-html="
                highlightMatch(
                  `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.primer_apellido}`
                )
              "
            ></span>
          </div>
        </td>
        <td class="px-6 py-4" v-html="highlightMatch(usuario.perfil_usuario.correo)"></td>
        <td
          class="px-6 py-4"
          v-html="highlightMatch(usuario.perfil_usuario.rol.tipo_rol)"
        ></td>
        <td
          class="px-6 py-4"
          v-html="
            highlightMatch(usuario.establecimiento_asignado?.nombre_establecimiento)
          "
        ></td>
        <td>{{ getInicialesEstablecimiento(usuario) }}</td>
        <td class="px-6 py-4 text-right">
          <button class="material-symbols-rounded p-1 text-gray-400 hover:text-primary">
            more_vert
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</template> -->

<script setup>
import { getInicialesUsuario } from "@/Utils/inicialesNombres";

defineProps({
  users: Array,
  isSelectionModeActive: Boolean,
  selectedUserIds: Array,
  highlightMatch: Function,
  getInitials: Function,
});
const selected = defineModel("selectedUserIds");
const emit = defineEmits(["openDetails"]);
</script>

<template>
  <table class="w-full text-sm text-left text-gray-300">
    <thead class="text-xs text-gray-200 uppercase bg-gray-700/50 sticky top-0">
      <tr>
        <th v-if="isSelectionModeActive" class="px-4 py-3 w-4"></th>
        <th scope="col" class="px-6 py-3">Usuario</th>
        <th scope="col" class="px-6 py-3">Rol</th>
        <th scope="col" class="px-6 py-3">Establecimiento</th>
        <th scope="col" class="px-6 py-3"><span class="sr-only">Acciones</span></th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="usuario in users"
        :key="usuario.id"
        class="border-b border-gray-700 hover:bg-gray-800/50"
        :class="{ 'cursor-pointer': !isSelectionModeActive }"
        @click="isSelectionModeActive ? null : emit('openDetails', usuario)"
      >
        <td v-if="isSelectionModeActive" class="px-4">
          <input
            type="checkbox"
            :value="usuario.id"
            v-model="selected"
            @click.stop
            class="form-checkbox rounded bg-gray-700 border-gray-600 text-primary focus:ring-primary"
          />
        </td>
        <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
          <div class="flex items-center gap-3">
            <img
              v-if="usuario.perfil_usuario.ruta_foto"
              :src="usuario.perfil_usuario.ruta_foto"
              alt="Foto"
              class="w-10 h-10 rounded-full object-cover"
            />
            <div
              v-else
              class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-bold"
            >
              {{ getInicialesUsuario(usuario) }}
            </div>
            <div>
              <div
                v-html="
                  highlightMatch(
                    `${usuario.perfil_usuario.primer_nombre} ${usuario.perfil_usuario.primer_apellido}`
                  )
                "
              ></div>
              <div
                class="text-xs text-gray-400"
                v-html="highlightMatch(usuario.perfil_usuario.correo)"
              ></div>
            </div>
          </div>
        </td>
        <td
          class="px-6 py-4"
          v-html="highlightMatch(usuario.perfil_usuario.rol.tipo_rol)"
        ></td>
        <td
          class="px-6 py-4"
          v-html="
            highlightMatch(usuario.establecimiento_asignado?.nombre_establecimiento)
          "
        ></td>
        <td class="px-6 py-4 text-right">
          <button
            @click.stop="emit('openDetails', usuario)"
            class="material-symbols-rounded p-1 text-gray-400 hover:text-primary"
          >
            more_vert
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</template>
