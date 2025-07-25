<script setup>
import { Head, usePage, router, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { defineProps, computed, ref } from "vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Header from "@/Components/header/Header.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import useEstadoClass from "@/Composables/useEstado";
import { handleBlur, handleInput, limitesCaracteres } from "@/Utils/formateoInputs";
import logoGoogle from "/resources/images/LogoGoogle.png";

const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
  inheritAttrs: false,
});

const props = defineProps({
  indicativos: Array,
  tipoDocumentos: Array,
})
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

const { getEstadoClass } = useEstadoClass();
const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

// Los refs para los inputs y previsualizaciones siguen siendo necesarios
const previsualizarFotoUsuario = ref(null);
const inputFotoUsuario = ref(null);
const previsualizarFotoTienda = ref(null);
const inputFotoTienda = ref(null);


// --- FUNCIONES UNIFICADAS ---

/**
 * Dispara el click en el input de archivo correcto.
 * @param {('usuario' | 'tienda')} tipo - Define a qué input hacer click.
 */
const seleccionarFoto = (tipo) => {
  if (tipo === 'usuario') {
    inputFotoUsuario.value?.click();
  } else if (tipo === 'tienda') {
    inputFotoTienda.value?.click();
  }
};

/**
 * Función principal que maneja la selección, previsualización y subida del archivo.
 * @param {Event} event - El evento del input de archivo.
 * @param {('usuario' | 'tienda')} tipo - El tipo de foto que se está subiendo.
 */
const manejarSubidaDeFoto = (event, tipo) => {
  const file = event.target.files[0];
  if (!file) return;

  // 1. Asigna el archivo al formulario
  form.photo = file;

  // 2. Previsualiza la imagen
  const reader = new FileReader();
  reader.onload = (e) => {
    // Decide qué ref de previsualización actualizar
    if (tipo === 'usuario') {
      previsualizarFotoUsuario.value = e.target.result;
    } else {
      previsualizarFotoTienda.value = e.target.result;
    }
  };
  reader.readAsDataURL(file);

  // 3. Elige la ruta correcta y sube el archivo
  const routeName = tipo === 'usuario'
    ? 'aplicacion.miPerfil.actualizarFotoPerfil'
    : 'aplicacion.miPerfil.actualizarFotoTienda';

  form.post(route(routeName, { aplicacion: aplicacion, rol: rol }), {
    preserveScroll: true,
    onSuccess: () => {
      
      form.reset('photo');
    }
  });
};

const inicialesNombreTienda = computed(() => {
  const nombresTienda = authStore.nombreTienda || "";

  const nombreEstablecimiento =
    nombresTienda.split(" ")[0]?.charAt(0).toUpperCase() || "";

  return nombreEstablecimiento;
});


const form = useForm({
  ruta_foto: authStore.rutaFoto,
  photo: null,
  primer_nombre: authStore.primerNombre || "No encontrado",
  segundo_nombre: authStore.segundoNombre || "No encontrado",
  primer_apellido: authStore.primerApellido || "No encontrado",
  segundo_apellido: authStore.segundoApellido || "",
  tipo_documento_id: authStore.tipoDocumento_id,
  numero_documento: authStore.numero_documento || "No encontrado",
  indicativo_id: authStore.indicativo_id,
  telefono: authStore.telefono || "No encontrado",
  email: authStore.email || "No encontrado",
  genero: authStore.genero || "No encontrado",
  direccion: authStore.direccionResidencia || "No encontrado",
  ciudad: authStore.ciudadResidencia || "No encontrado",
  barrio: authStore.barrioResidencia || "No encontrado",
  ruta_foto_establecimiento: authStore.rutaFotoEstablecimiento,
  nombre_tienda: authStore.nombreTienda,
  telefono_tienda: authStore.telefonoEstablecimiento,
  email_tienda: authStore.emailEstablecimiento,
  tipo_tienda: authStore.tipoEstablecimiento,
  direccion_tienda: authStore.direccionEstablecimiento || "No encontrado",
  ciudad_tienda: authStore.ciudadEstablecimiento || "No encontrado",
  barrio_tienda: authStore.barrioEstablecimiento || "No encontrado",
});

const inicialesNombreUsuario = computed(() => {
  const nombres = authStore.primerNombre || "";
  const apellidos = authStore.primerApellido || "";
  const firstNameInitial = nombres.split(" ")[0]?.charAt(0).toUpperCase() || "";
  const lastNameInitial = apellidos.split(" ")[0]?.charAt(0).toUpperCase() || "";
  return firstNameInitial + lastNameInitial;
});


</script>

<template>
  <div>

    <Head title="Editar mis datos" />

    <div class="flex">
      <SidebarSuperAdmin :auth="authStore">
        <MensajesLayout />
        <div class="contenido px-3 max-h-[90vh] w-full overflow-auto scrollbar-custom contenido-principal">
          <div class="options flex gap-1 items-center justify-center text-[14px] mb-10">
            <a :href="route('aplicacion.dashboard', { aplicacion, rol })"
              class="text-mono-negro dark:text-mono-blanco dark:hover:text-universal-azul flex items-center gap-1"
              :class="hoverTexto">
              <span class="material-symbols-rounded text-[16px]">home</span>
              <p>Home</p>
            </a>
            <span
              class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco">chevron_right</span>
            <a :href="route('aplicacion.miPerfil', { aplicacion, rol })"
              class="text-mono-negro dark:text-mono-blanco dark:hover:text-universal-azul flex items-center gap-1"
              :class="hoverTexto">
              <span class="material-symbols-rounded text-[16px]">crown</span>
              <p>Mi perfil</p>
            </a>
            <span
              class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco">chevron_right</span>
            <p class="font-bold text-mono-negro dark:text-mono-blanco">
              Editar datos personales
            </p>
          </div>
          <!-- <h1 class="text-[30px] dark:text-mono-blanco text-mono-negro">Mi perfil</h1> -->

          <form @submit.prevent="submit">
            <div class="editarMiPerfil w-full min-h-[78dvh]">
              <div class="rounded-[15px] cajaUsuario p-5 bg-mono-blanco_opacity dark:bg-secundary-opacity">
                <div class="w-full h-[130px] rounded-md opacity-50" :class="authStore.bgColor"></div>

                <div class="flex gap-2 items-center">
                  <div
                    class="-mt-20 ml-5 mb-2 grid place-content-center foto w-[180px] h-[180px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg">
                    <template v-if="authStore.fotoUrlCompletaUsuario">
                      <div class="relative group w-[160px] h-[160px]">

                        <img :src="authStore.fotoUrlCompletaUsuario" class="rounded-[18px] w-full h-full object-cover shadow-lg" />

                        <div
                          class="absolute inset-0 bg-black/40 rounded-[18px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">

                          <button @click="seleccionarFoto('usuario')"
                            class="cursor-pointer bg-white p-3 rounded-full shadow-md hover:bg-gray-200 transition"
                            title="Cambiar foto">
                            <span class="material-symbols-rounded text-2xl" :class="[textoClase]">edit</span>
                          </button>

                        </div>
                      </div>
                    </template>

                    <template v-else>
                      <div :class="bgClase"
                        class="relative flex justify-center rounded-[18px] items-center group w-[160px] h-[160px]">
                        <p class="text-[60px] font-semibold">{{ inicialesNombreUsuario }}</p>
                        <div
                          class="absolute inset-0 bg-black/40 rounded-[18px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">

                          <button @click="seleccionarFoto('usuario')"
                            class="cursor-pointer bg-white p-3 rounded-full shadow-md hover:bg-gray-200 transition"
                            title="Añadir foto">
                            <span class="material-symbols-rounded text-2xl" :class="[textoClase]">add_a_photo</span>
                          </button>

                        </div>
                      </div>
                    </template>

                    <input ref="inputFotoUsuario" type="file" accept="image/*"
                      @change="manejarSubidaDeFoto($event, 'usuario')" class="hidden" />
                  </div>

                  <div class="nombreTimeReal">
                    <p class="text-[32px] font-medium flex items-center gap-1">{{ authStore.primerNombre + ' ' +
                      authStore.segundoNombre +
                      ' ' + authStore.primerApellido + ' ' + authStore.segundoApellido }}
                    <div class="grid place-items-center" v-if="authStore.google_id === null">
                      <span class="material-symbols-rounded  text-gray-700">verified_off</span>
                    </div>
                    <div class="text-secundary-default dark:text-mono-blanco" v-else>
                      <span
                        class="grid place-items-center material-symbols-rounded  text-universal-azul_secundaria">verified</span>
                    </div>
                    </p>

                    <div class="flex justify-between items-center -mt-1">
                      <p class="text-[18 px] text-secundary-light font-normal flex items-center gap-1"><span
                          class="material-symbols-rounded" :class="[textoClase]">key</span> {{
                            authStore.google_id || authStore.idUsuario }}</p>

                      <div class="flex items-center gap-1">
                        <div class="w-3 h-3 rounded-full" :class="getEstadoClass(authStore.estadoUsuario)"></div>
                        <span class="text-secundary-default dark:text-mono-blanco">{{
                          authStore.estadoUsuario
                        }}</span>
                      </div>

                    </div>
                  </div>
                </div>

                <div class="infoBasica px-4">
                  <div class="flex justify-between items-center">
                    <h4 class="text-secundary-default dark:text-mono-blanco">
                      Editar mi usuario:
                    </h4>
                  </div>

                  <!-- nombres -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between  2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Primer nombre:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.primer_nombre.length }} /
                          {{ limitesCaracteres.primer_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{ 'border-universal-naranja': form.errors.primer_nombre }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.primer_nombre" @blur="handleBlur(form, 'primer_nombre')"
                          @input="(e) => handleInput(e, form, 'primer_nombre')" />
                      </div>
                      <span v-if="form.errors.primer_nombre" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.primer_nombre }}
                      </span>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Segundo nombre:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.segundo_nombre.length }} /
                          {{ limitesCaracteres.segundo_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{
                          'border-universal-naranja': form.errors.segundo_nombre,
                        }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo" v-model="form.segundo_nombre"
                          @blur="handleBlur(form, 'segundo_nombre')"
                          @input="(e) => handleInput(e, form, 'segundo_nombre')" />
                      </div>
                      <span v-if="form.errors.segundo_nombre" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.segundo_nombre }}
                      </span>
                    </div>
                  </div>

                  <!-- apellidos -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between mt-2 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Primer apellido:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.primer_apellido.length }} /
                          {{ limitesCaracteres.primer_apellido }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{
                          'border-universal-naranja': form.errors.primer_apellido,
                        }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.primer_apellido"
                          @blur="handleBlur(form, 'primer_apellido')"
                          @input="(e) => handleInput(e, form, 'primer_apellido')" />
                      </div>
                      <span v-if="form.errors.primer_apellido" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.primer_apellido }}
                      </span>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Segundo apellido:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.segundo_apellido.length }} /
                          {{ limitesCaracteres.segundo_apellido }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{
                          'border-universal-naranja': form.errors.segundo_apellido,
                        }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo" v-model="form.segundo_apellido"
                          @blur="handleBlur(form, 'segundo_apellido')"
                          @input="(e) => handleInput(e, form, 'segundo_apellido')" />
                      </div>
                      <span v-if="form.errors.segundo_apellido" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.segundo_apellido }}
                      </span>
                    </div>
                  </div>

                  <!-- telefonos -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between mt-2  2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Indicativo:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ indicativos.length }} Disponibles
                        </p>
                      </div>
                      <div class="desplegar-estado">
                        <div class="flex items-center">
                          <div class="custom-select w-full">
                            <select v-model="form.indicativo_id"
                              class="2xl:p-2 border border-secundary-light rounded-md">
                              <option value="" disabled>Disponibles: {{ indicativos.length }}</option>
                              <option v-for="indicativo in indicativos" :key="indicativo.id" :value="indicativo.id">
                                {{ indicativo.pais }} ({{ indicativo.codigo_pais }})
                              </option>
                            </select>
                            <div class="select-arrow"></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Número de teléfono:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.telefono.length }} /
                          {{ limitesCaracteres.telefono }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{
                          'border-universal-naranja': form.errors.telefono,
                        }">
                        <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">pin</span>

                        <input type="number"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo" v-model="form.telefono" @blur="handleBlur(form, 'telefono')"
                          @input="(e) => handleInput(e, form, 'telefono')" />
                      </div>
                      <span v-if="form.errors.telefono" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.telefono }}
                      </span>
                    </div>
                  </div>

                  <!-- documento id -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between mt-2 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Tipo documento:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ tipoDocumentos.length }} Disponibles
                        </p>
                      </div>
                      <div class="desplegar-estado">
                        <div class="flex items-center">
                          <div class="custom-select w-full">
                            <select v-model="form.tipo_documento_id"
                              class="2xl:p-2 border border-secundary-light rounded-md">
                              <option value="" disabled>Disponibles: {{ tipoDocumentos.length }}</option>

                              <option v-for="tipoDocumento in tipoDocumentos" :key="tipoDocumento.id"
                                :value="tipoDocumento.id">
                                {{ tipoDocumento.documento_legal }}
                              </option>
                            </select>
                            <div class="select-arrow"></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Número documento:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.numero_documento.length }} /
                          {{ limitesCaracteres.numero_documento }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{
                          'border-universal-naranja': form.errors.numero_documento,
                        }">
                        <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">pin</span>

                        <input type="number"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo" v-model="form.numero_documento"
                          @blur="handleBlur(form, 'numero_documento')"
                          @input="(e) => handleInput(e, form, 'numero_documento')" />
                      </div>
                      <span v-if="form.errors.numero_documento" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.numero_documento }}
                      </span>
                    </div>
                  </div>

                  <!-- direccion -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between mt-2 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[33.3%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Dirección recidencia:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.direccion.length }} /
                          {{ limitesCaracteres.direccion }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        :class="{ 'border-universal-naranja': form.errors.direccion }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.direccion" @blur="handleBlur(form, 'direccion')"
                          @input="(e) => handleInput(e, form, 'direccion')" />
                      </div>
                      <span v-if="form.errors.direccion" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.direccion }}
                      </span>
                    </div>

                    <div class="2xl:w-[33.3%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Ciudad recidencia:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.ciudad.length }} /
                          {{ limitesCaracteres.direccion }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        :class="{ 'border-universal-naranja': form.errors.ciudad }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.ciudad" @blur="handleBlur(form, 'ciudad')"
                          @input="(e) => handleInput(e, form, 'ciudad')" />
                      </div>
                      <span v-if="form.errors.ciudad" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.ciudad }}
                      </span>
                    </div>

                    <div class="2xl:w-[33.3%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Barrio recidencia:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.barrio.length }} /
                          {{ limitesCaracteres.direccion }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        :class="{ 'border-universal-naranja': form.errors.barrio }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.barrio" @blur="handleBlur(form, 'barrio')"
                          @input="(e) => handleInput(e, form, 'barrio')" />
                      </div>
                      <span v-if="form.errors.barrio" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.barrio }}
                      </span>
                    </div>

                  </div>

                  <div class="" v-if="authStore.google_id">
                    <div class="btn-inicio flex justify-center gap-2 mt-5">
                      <span class="rounded-md py-2 gap-3 w-[100%] flex items-center justify-center">
                        <img width="20px" height="20px" :src="logoGoogle" alt="Logo google" />
                        <span class="dark:text-mono-blanco">Cuenta vinculada con Google</span>
                      </span>
                    </div>
                  </div>
                  <div class="" v-else>
                    <div class="btn-inicio flex justify-center gap-2 mt-5">
                      <a :href="route('google.login')"
                        class="border border-secundary-light rounded-md py-2 gap-3 w-[100%] flex items-center justify-center hover:-translate-y-1 hover:bg-secundary-default">
                        <img width="20px" height="20px" :src="logoGoogle" alt="Logo google" />
                        <span class="dark:text-mono-blanco">Vincular con tu cuenta de Google</span>
                      </a>
                    </div>

                  </div>

                  <div class="btn-inicio flex justify-center gap-2 mt-5">
                    <a :href="route('google.login')"
                      class="border border-semaforo-rojo_opacity text-semaforo-rojo_opacity rounded-md p-1 gap-3 w-[100%] flex items-center justify-center hover:text-semaforo-rojo hover:border-semaforo-rojo">
                      <span class="material-symbols-rounded text-[18px]">delete</span>
                      <span class="">Eliminar cuenta</span>
                    </a>
                  </div>

                </div>
              </div>

              <div
                class="rounded-[15px] cajaheader flex justify-between items-center dark:bg-secundary-opacity bg-mono-blanco_opacity p-5">
                <h4 class="text-[45px] font-medium text-secundary-default dark:text-mono-blanco">
                  Estás editando tu perfil
                </h4>
                <a :href="route('aplicacion.miPerfil.editarMiPerfil', { aplicacion, rol })">
                  <button :class="bgClase" class="flex items-center gap-1 p-2 rounded-lg">
                    Estoy seguro, actualizar
                    <span class="material-symbols-rounded text-[20px]">check_circle</span>
                  </button>
                </a>
              </div>

              <div class="rounded-[15px] p-5 cajaTienda dark:bg-secundary-opacity bg-mono-blanco_opacity">
                <div class="w-full h-[130px] rounded-md opacity-50" :class="authStore.bgColor"></div>

                <div class="flex gap-2 items-center">
                  <div
                    class="-mt-20 ml-5 mb-2 grid place-content-center foto w-[180px] h-[180px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg">
                    <template v-if="authStore.fotoUrlCompletaEstablecimiento">
                      <div class="relative group w-[160px] h-[160px]">

                        <img :src="authStore.fotoUrlCompletaEstablecimiento" class="rounded-[18px] w-full h-full object-cover shadow-lg" />

                        <div
                          class="absolute inset-0 bg-black/40 rounded-[18px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">

                          <button @click="seleccionarFoto('tienda')"
                            class="cursor-pointer bg-white p-3 rounded-full shadow-md hover:bg-gray-200 transition"
                            title="Cambiar foto">
                            <span class="material-symbols-rounded text-2xl" :class="[textoClase]">edit</span>
                          </button>

                        </div>
                      </div>
                    </template>

                    <template v-else>
                      <div :class="bgClase"
                        class="relative flex justify-center rounded-[18px] items-center group w-[160px] h-[160px]">
                        <p class="text-[60px] font-semibold">{{ inicialesNombreTienda }}</p>
                        <div
                          class="absolute inset-0 bg-black/40 rounded-[18px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">

                          <button @click="seleccionarFoto('tienda')"
                            class="cursor-pointer bg-white p-3 rounded-full shadow-md hover:bg-gray-200 transition"
                            title="Añadir foto">
                            <span class="material-symbols-rounded text-2xl" :class="[textoClase]">add_a_photo</span>
                          </button>

                        </div>
                      </div>
                    </template>

                    <input ref="inputFotoTienda" type="file" accept="image/*"
                      @change="manejarSubidaDeFoto($event, 'tienda')" class="hidden" />
                  </div>

                  <div class="nombreTimeReal">
                    <p class="text-[32px] font-medium flex items-center gap-1">{{ authStore.nombreTienda }}
                    <div class="grid place-items-center" v-if="authStore.google_id === null">
                      <span class="material-symbols-rounded  text-gray-700">verified_off</span>
                    </div>
                    <div class="text-secundary-default dark:text-mono-blanco" v-else>
                      <span
                        class="grid place-items-center material-symbols-rounded  text-universal-azul_secundaria">verified</span>
                    </div>
                    </p>

                    <div class="flex justify-between gap-5 items-center -mt-1">
                      <p class="text-[18 px] text-secundary-light font-normal flex items-center gap-1"><span
                          class="material-symbols-rounded" :class="[textoClase]">key</span> {{
                            authStore.tokenTienda || authStore.tienda_id }}</p>

                      <div class="flex items-center gap-1">
                        <div class="w-3 h-3 rounded-full" :class="getEstadoClass(authStore.estadoTienda)"></div>
                        <span class="text-secundary-default dark:text-mono-blanco">{{
                          authStore.estadoTienda
                        }}</span>
                      </div>

                    </div>
                  </div>
                </div>

                <div class="infoBasica px-4">
                  <div class="flex justify-between items-center">
                    <h4 class="text-secundary-default dark:text-mono-blanco">
                      Editar mi establecimiento:
                    </h4>
                  </div>

                  <!-- nombres -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between mt-2 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Nombre establecimiento:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.nombre_tienda.length }} /
                          {{ limitesCaracteres.nombre_tienda }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{ 'border-universal-naranja': form.errors.nombre_tienda }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.nombre_tienda" @blur="handleBlur(form, 'nombre_tienda')"
                          @input="(e) => handleInput(e, form, 'nombre_tienda')" />
                      </div>
                      <span v-if="form.errors.nombre_tienda" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.nombre_tienda }}
                      </span>
                    </div>
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Tipo establecimiento:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.tipo_tienda.length }} /
                          {{ limitesCaracteres.tipo_tienda }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{ 'border-universal-naranja': form.errors.tipo_tienda }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">category</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.tipo_tienda" @blur="handleBlur(form, 'tipo_tienda')"
                          @input="(e) => handleInput(e, form, 'tipo_tienda')" />
                      </div>
                      <span v-if="form.errors.tipo" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.tipo_tienda }}
                      </span>
                    </div>
                  </div>


                  <!-- telefonos -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between mt-2 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Número de teléfono:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.telefono.length }} /
                          {{ limitesCaracteres.telefono }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{
                          'border-universal-naranja': form.errors.telefono,
                        }">
                        <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">pin</span>

                        <input type="number"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo" v-model="form.telefono" @blur="handleBlur(form, 'telefono')"
                          @input="(e) => handleInput(e, form, 'telefono')" />
                      </div>
                      <span v-if="form.errors.telefono" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.telefono }}
                      </span>
                    </div>
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Email corporativo:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.email_tienda.length }} /
                          {{ limitesCaracteres.email }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t :class="{
                          'border-universal-naranja': form.errors.email_tienda,
                        }">
                        <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">email</span>

                        <input type="email"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo" v-model="form.email_tienda"
                          @blur="handleBlur(form, 'email_tienda')"
                          @input="(e) => handleInput(e, form, 'email_tienda')" />
                      </div>
                      <span v-if="form.errors.email_tienda" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.email_tienda }}
                      </span>
                    </div>
                  </div>



                  <!-- direccion -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between mt-2 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center">
                    <div class="2xl:w-[33.3%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Dirección:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.direccion_tienda.length }} /
                          {{ limitesCaracteres.direccion }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        :class="{ 'border-universal-naranja': form.errors.direccion_tienda }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.direccion_tienda"
                          @blur="handleBlur(form, 'direccion_tienda')"
                          @input="(e) => handleInput(e, form, 'direccion_tienda')" />
                      </div>
                      <span v-if="form.errors.direccion_tienda" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.direccion_tienda }}
                      </span>
                    </div>

                    <div class="2xl:w-[33.3%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Ciudad:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.ciudad_tienda.length }} /
                          {{ limitesCaracteres.direccion }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        :class="{ 'border-universal-naranja': form.errors.ciudad_tienda }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.ciudad_tienda" @blur="handleBlur(form, 'ciudad_tienda')"
                          @input="(e) => handleInput(e, form, 'ciudad_tienda')" />
                      </div>
                      <span v-if="form.errors.ciudad_tienda" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.ciudad_tienda }}
                      </span>
                    </div>

                    <div class="2xl:w-[33.3%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Barrio:
                        </p>
                        <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                          {{ form.barrio_tienda.length }} /
                          {{ limitesCaracteres.direccion }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        :class="{ 'border-universal-naranja': form.errors.barrio_tienda }">
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">format_italic</span>

                        <input type="text"
                          class="2xl:w-full outline-none border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan" v-model="form.barrio_tienda" @blur="handleBlur(form, 'barrio_tienda')"
                          @input="(e) => handleInput(e, form, 'barrio_tienda')" />
                      </div>
                      <span v-if="form.errors.barrio_tienda" class="2xl:text-sm text-universal-naranja">
                        {{ form.errors.barrio_tienda }}
                      </span>
                    </div>

                  </div>
                </div>
              </div>
            </div>



          </form>
        </div>
      </SidebarSuperAdmin>
    </div>
  </div>
</template>
