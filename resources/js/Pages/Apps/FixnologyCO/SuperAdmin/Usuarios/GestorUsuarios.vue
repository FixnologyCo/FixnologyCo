<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted, computed } from "vue";
import "dayjs/locale/es";
import Header from "@/Components/header/Header.vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import Colors from "@/Composables/ModularColores";
const {
  appName,
  bgClase,
  bgOpacity,
  focus,
  textoClase,
  borderClase,
  buttonFocus,
  hoverClase,
  hoverTexto,
  buttonClase,
  buttonSecundario,
} = Colors();

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";


const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
});
import useEstadoClass from "@/Composables/useEstado";
const { getEstadoClass } = useEstadoClass();

import { formatFecha, formatFechaShort } from "@/Utils/date";

function obtenerIniciales(nombre, apellido) {
  const inicial1 = nombre?.charAt(0) || "";
  const inicial2 = apellido?.charAt(0) || "";
  return (inicial1 + inicial2).toUpperCase();
}



const seleccionado = ref(false);
const auth = usePage().props.auth;

const activeTab = ref(0);
const tabs = [{ label: "Clientes Fix" }, { label: "Empleados" }];

function irADetalle(id) {
  router.get(
    route("aplicacion.detallesUsuarios.id", {
      aplicacion: props.aplicacion,
      rol: props.rol,
      id: id,
    })
  );
}
</script>

<template>
  <div>
    <Head title="Gestor de usuarios" />
  <MensajesLayout />

 <div class="flex">
    <SidebarSuperAdmin :authStore >
     <div class="w-full">
  
      <div class="contenido p-3">
        <div class="mb-6">
          <nav class="-mb-px flex space-x-4">
            <button
              v-for="(tab, index) in tabs"
              :key="index"
              @click="activeTab = index"
              :class="[
                'text-[12px] font-medium px-4 py-2',
                activeTab === index
                  ? textoClase + ' ' + borderClase
                  : 'text-secundary-default dark:text-secundary-light ' + hoverTexto,
              ]"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <div v-if="activeTab === 0">
          <div class="headerClientes flex items-center justify-between">
            <div class="total flex items-center gap-2">
              <div
                class="icon bg-secundary-light p-2 rounded-md grid place-content-center text-mono-negro"
              >
                <span class="material-symbols-rounded">diversity_1</span>
              </div>
              <p class="text-[30px] font-semibold text-mono-blanco">
                {{ clientesFix }}
                <span class="text-secundary-light text-[14px]">Total clientes</span>
              </p>
            </div>

            <div class="end flex gap-5">
              <div class="filtro-vista flex gap-4">
                <button
                  class="text-[14px] rounded-md border p-2 flex items-center text-mono-blanco"
                >
                  <span class="material-symbols-rounded text-[20px]">filter_alt</span>
                  Filtros
                </button>
              </div>

              <button class="text-[14px]" :class="buttonClase">
                <span class="material-symbols-rounded">add_circle</span> Agregar cliente
              </button>
            </div>
          </div>
          <!-- formulario -->
          <div class="div text-mono-blanco mt-5">
            <div class="overflow-x-auto">
              <table class="w-full border-collapse" id="tabla">
                <thead class="rounded-x">
                  <tr class="border border-secundary-light">
                    <th class="p-2 text-left">
                      <label class="inline-flex items-center cursor-pointer">
                        <input
                          type="checkbox"
                          v-model="seleccionado"
                          class="sr-only peer"
                        />
                        <div
                          class="w-4 h-4 border border-secundary-light rounded-full transition-all duration-200"
                          :class="[
                            'peer-checked:border-gray-700',
                            seleccionado ? bgClase : 'bg-mono-negro',
                          ]"
                        ></div>
                      </label>
                    </th>

                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >format_italic</span
                        >
                        <span>Nombres</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >phone</span
                        >
                        <span>Teléfono</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >email</span
                        >
                        <span>Correo email</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >store</span
                        >
                        <span>Tienda</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >android</span
                        >
                        <span>Aplicación</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >event</span
                        >
                        <span>Fecha registro</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >hdr_weak</span
                        >
                        <span>Estado</span>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody class="" v-for="clienteFix in clientesFix" :key="clienteFix.id">
                  <tr
                    class="hover:bg-secundary-opacity cursor-pointer"
                    @click="irADetalle(clienteFix.id)"
                  >
                    <th class="p-2 text-left">
                      <label class="inline-flex items-center cursor-pointer">
                        <input
                          type="checkbox"
                          v-model="seleccionado"
                          class="sr-only peer"
                        />
                        <div
                          class="w-4 h-4 border border-secundary-light rounded-full transition-all duration-200"
                          :class="[
                            'peer-checked:border-gray-700',
                            seleccionado ? bgClase : 'bg-mono-negro',
                          ]"
                        ></div>
                      </label>
                    </th>
                    <td class="text-[14px] flex items-center gap-2 p-2">
                      <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                        :class="clienteFix.foto_cliente ? '' : bgClase"
                      >
                        <img
                          v-if="clienteFix.foto_cliente"
                          :src="clienteFix.foto_cliente"
                          alt="Foto"
                          class="w-10 h-10 rounded-full object-cover border"
                        />
                        <span v-else>
                          {{
                            obtenerIniciales(
                              clienteFix.nombres_ct,
                              clienteFix.apellidos_ct
                            )
                          }}
                        </span>
                      </div>
                      <p>
                        {{ clienteFix.nombres_ct || "No encontrado" }}
                        {{ clienteFix.apellidos_ct }}
                      </p>
                    </td>
                    <td class="text-[14px] p-2">{{ clienteFix.telefono_ct }}</td>
                    <td class="text-[14px] p-2">{{ clienteFix.email_ct }}</td>
                    <td class="text-[14px] p-2">{{ clienteFix.nombre_tienda }}</td>
                    <td class="text-[14px] p-2">{{ clienteFix.nombre_app }}</td>
                    <td class="text-[14px] p-2">
                      <span class="p-2 rounded-lg font-semibold">{{
                        formatFechaShort(clienteFix.fecha_creacion)
                      }}</span>
                    </td>
                    <td class="text-[14px] p-2">
                      <span
                        class="p-2 rounded-lg font-semibold"
                        :class="[getEstadoClass(clienteFix.estado_token)]"
                        >{{ clienteFix.estado_token }}</span
                      >
                    </td>
                  </tr>
                </tbody>
              </table>
              <div
                id="mensaje-no-resultados"
                class="hidden text-center mt-10 font-bold text-[35px] text-secundary-light fade-in"
              >
                No hay coincidencias.
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 1">
          <div class="headerEmpledos flex items-center justify-between">
            <div class="total flex items-center gap-2">
              <div
                class="icon bg-secundary-light p-2 rounded-md grid place-content-center text-mono-negro"
              >
                <span class="material-symbols-rounded">engineering</span>
              </div>
              <p class="text-[30px] font-semibold text-mono-blanco">
                {{ empleadosFix}}
                <span class="text-secundary-light text-[14px]">Total empleados</span>
              </p>
            </div>

            <div class="end flex gap-5">
              <div class="filtro-vista flex gap-4">
                <button
                  class="text-[14px] rounded-md border p-2 flex items-center text-mono-blanco"
                >
                  <span class="material-symbols-rounded text-[20px]">filter_alt</span>
                  Filtros
                </button>
              </div>

              <button class="text-[14px]" :class="buttonClase">
                <span class="material-symbols-rounded">add_circle</span> Agregar empleado
              </button>
            </div>
          </div>

          <!-- formulario -->
          <div class="div text-mono-blanco mt-5">
            <div class="overflow-x-auto">
              <table class="w-full border-collapse" id="tabla">
                <thead class="rounded-x">
                  <tr class="border border-secundary-light">
                    <th class="p-2 text-left">
                      <label class="inline-flex items-center cursor-pointer">
                        <input
                          type="checkbox"
                          v-model="seleccionado"
                          class="sr-only peer"
                        />
                        <div
                          class="w-4 h-4 border border-secundary-light rounded-full transition-all duration-200"
                          :class="[
                            'peer-checked:border-gray-700',
                            seleccionado ? bgClase : 'bg-mono-negro',
                          ]"
                        ></div>
                      </label>
                    </th>

                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >format_italic</span
                        >
                        <span>Nombres</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >phone</span
                        >
                        <span>Teléfono</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >email</span
                        >
                        <span>Correo email</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >store</span
                        >
                        <span>Tienda</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >android</span
                        >
                        <span>Aplicación</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >event</span
                        >
                        <span>Fecha registro</span>
                      </div>
                    </th>
                    <th class="p-2 text-left">
                      <div class="flex items-center gap-1 text-[13px]">
                        <span
                          class="material-symbols-rounded text-[20px]"
                          :class="[textoClase]"
                          >hdr_weak</span
                        >
                        <span>Estado</span>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody
                  class=""
                  v-for="empleadoFix in empleadosFix"
                  :title="empleadoFix.id"
                  :key="empleadoFix.id"
                >
                  <tr
                    class="hover:bg-secundary-opacity cursor-pointer"
                    @click="irADetalle(empleadoFix.id)"
                  >
                    <th class="p-2 text-left">
                      <label class="inline-flex items-center cursor-pointer">
                        <input
                          type="checkbox"
                          v-model="seleccionado"
                          class="sr-only peer"
                        />
                        <div
                          class="w-4 h-4 border border-secundary-light rounded-full transition-all duration-200"
                          :class="[
                            'peer-checked:border-gray-700',
                            seleccionado ? bgClase : 'bg-mono-negro',
                          ]"
                        ></div>
                      </label>
                    </th>
                    <td class="text-[14px] flex items-center gap-2 p-2">
                      <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                        :class="empleadoFix.foto_cliente ? '' : bgClase"
                      >
                        <img
                          v-if="empleadoFix.foto_cliente"
                          :src="empleadoFix.foto_cliente"
                          alt="Foto"
                          class="w-10 h-10 rounded-full object-cover border-2"
                        />
                        <span v-else>
                          {{
                            obtenerIniciales(
                              empleadoFix.nombres_ct,
                              empleadoFix.apellidos_ct
                            )
                          }}
                        </span>
                      </div>
                      <p>
                        {{ empleadoFix.nombres_ct || "No encontrado" }}
                        {{ empleadoFix.apellidos_ct }}
                      </p>
                    </td>
                    <td class="text-[14px] p-2">{{ empleadoFix.telefono_ct }}</td>
                    <td class="text-[14px] p-2">{{ empleadoFix.email_ct }}</td>
                    <td class="text-[14px] p-2">{{ empleadoFix.nombre_tienda }}</td>
                    <td class="text-[14px] p-2">{{ empleadoFix.nombre_app }}</td>
                    <td class="text-[14px] p-2">
                      <span class="p-2 rounded-lg font-semibold">{{
                        formatFechaShort(empleadoFix.fecha_creacion)
                      }}</span>
                    </td>
                    <td class="text-[14px] p-2">
                      <span
                        class="p-2 rounded-lg font-semibold"
                        :class="[getEstadoClass(empleadoFix.estado_token)]"
                        >{{ empleadoFix.estado_token }}</span
                      >
                    </td>
                  </tr>
                </tbody>
              </table>
              <div
                id="mensaje-no-resultados"
                class="hidden text-center mt-10 font-bold text-[35px] text-secundary-light fade-in"
              >
                No hay coincidencias.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    </SidebarSuperAdmin>

   
  </div>
  
  </div>


  
</template>
