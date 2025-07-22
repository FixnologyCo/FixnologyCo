<script setup>
import { Head, usePage, router, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { defineProps, computed, ref } from "vue";
import SidebarSuperAdmin from "@/Components/Sidebar/FixnologyCO/Sidebar.vue";
import Header from "@/Components/header/Header.vue";
import Colors from "@/Composables/ModularColores";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import { formatFecha } from "@/Utils/date";
import { formatCOP } from "@/Utils/formateoMoneda";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useTiempo } from "@/Composables/useTiempo";
import useEstadoClass from "@/Composables/useEstado";
import { handleBlur, handleInput, limitesCaracteres } from "@/Utils/formateoInputs";

const authStore = useAuthStore();
defineOptions({
  layout: AuthenticatedLayout,
  inheritAttrs: false,
});

const form = useForm({
  ruta_foto: authStore.rutaFoto,
  primer_nombre: authStore.primerNombre || "No encontrado",
  segundo_nombre: authStore.segundoNombre || "No encontrado",
  primer_apellido: authStore.primerApellido || "No encontrado",
  segundo_apellido: authStore.segundoApellido || "",
  id_tipo_documento: authStore.tipoDocumento || "No encontrado",
  numero_documento: authStore.numero_documento || "No encontrado",
  indicativo: authStore.indicativo || 1,
  telefono: authStore.telefono || "No encontrado",
  email: authStore.email || "No encontrado",
  genero: authStore.genero || "No encontrado",
  direccion: authStore.direccionResidencia || "No encontrado",
  ciudad: authStore.ciudadResidencia || "No encontrado",
  barrio: authStore.barrioResidencia || "No encontrado",
});

const submit = () => {
  form.post(route("aplicacion.editarMiPerfil.actualizar"));
};

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

const aplicacion = authStore.aplicacion;
const rol = authStore.rol;

const inicialesNombreUsuario = computed(() => {
  const nombres = authStore.primerNombre || "";
  const apellidos = authStore.primerApellido || "";
  const firstNameInitial = nombres.split(" ")[0]?.charAt(0).toUpperCase() || "";
  const lastNameInitial = apellidos.split(" ")[0]?.charAt(0).toUpperCase() || "";
  return firstNameInitial + lastNameInitial;
});

const inicialesNombreTienda = computed(() => {
  const nombresTienda = authStore.nombreTienda || "";

  const nombreEstablecimiento =
    nombresTienda.split(" ")[0]?.charAt(0).toUpperCase() || "";

  return nombreEstablecimiento;
});
</script>

<template>
  <div>
    <MensajesLayout />
    <Head title="Editar mis datos" />

    <div class="flex">
      <SidebarSuperAdmin :auth="authStore">
        <div
          class="contenido px-3 max-h-[90vh] w-full overflow-auto scrollbar-custom contenido-principal"
        >
          <div class="options flex gap-1 items-center justify-center text-[14px] mb-10">
            <a
              :href="route('aplicacion.dashboard', { aplicacion, rol })"
              class="text-mono-negro dark:text-mono-blanco dark:hover:text-universal-azul flex items-center gap-1"
              :class="hoverTexto"
            >
              <span class="material-symbols-rounded text-[16px]">home</span>
              <p>Home</p>
            </a>
            <span
              class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco"
              >chevron_right</span
            >
            <a
              :href="route('aplicacion.miPerfil', { aplicacion, rol })"
              class="text-mono-negro dark:text-mono-blanco dark:hover:text-universal-azul flex items-center gap-1"
              :class="hoverTexto"
            >
              <span class="material-symbols-rounded text-[16px]">crown</span>
              <p>Mi perfil</p>
            </a>
            <span
              class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco"
              >chevron_right</span
            >
            <p class="font-bold text-mono-negro dark:text-mono-blanco">
              Editar datos personales
            </p>
          </div>
          <!-- <h1 class="text-[30px] dark:text-mono-blanco text-mono-negro">Mi perfil</h1> -->
          <form @submit.prevent="submit">
            <div class="editarMiPerfil w-full min-h-[78dvh]">
              <div
                class="rounded-[15px] cajaUsuario p-5 bg-mono-blanco_opacity dark:bg-secundary-opacity"
              >
                <div
                  class="mb-2 grid place-content-center foto w-[280px] h-[280px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                >
                  <template v-if="authStore.rutaFoto !== 'Sin foto'">
                    <div class="relative group w-[260px] h-[260px]">
                      <img
                        :src="authStore.rutaFoto"
                        class="rounded-[18px] w-full h-full object-cover shadow-lg"
                      />
                    </div>
                  </template>

                  <template v-else>
                    <div
                      :class="[bgClase]"
                      class="rounded-[18px] grid place-content-center w-[280px] h-[280px]"
                    >
                      <p class="text-[60px] font-semibold">
                        {{ inicialesNombreUsuario }}
                      </p>
                    </div>
                  </template>
                </div>

                <div class="infoBasica px-4">
                  <div class="flex justify-between items-center">
                    <h4 class="text-secundary-default dark:text-mono-blanco">
                      Editar mi usuario:
                    </h4>
                  </div>

                  <!-- nombres -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
                  >
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Primer nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.primer_nombre.length }} /
                          {{ limitesCaracteres.primer_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{ 'border-universal-naranja': form.errors.primer_nombre }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan"
                          v-model="form.primer_nombre"
                          @blur="handleBlur(form, 'primer_nombre')"
                          @input="(e) => handleInput(e, form, 'primer_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.primer_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.primer_nombre }}
                      </span>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Segundo nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.segundo_nombre.length }} /
                          {{ limitesCaracteres.segundo_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{
                          'border-universal-naranja': form.errors.segundo_nombre,
                        }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo"
                          v-model="form.segundo_nombre"
                          @blur="handleBlur(form, 'segundo_nombre')"
                          @input="(e) => handleInput(e, form, 'segundo_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.segundo_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.segundo_nombre }}
                      </span>
                    </div>
                  </div>

                  <!-- apellidos -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
                  >
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Primer apellido:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.primer_apellido.length }} /
                          {{ limitesCaracteres.primer_apellido }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{
                          'border-universal-naranja': form.errors.primer_apellido,
                        }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan"
                          v-model="form.primer_apellido"
                          @blur="handleBlur(form, 'primer_apellido')"
                          @input="(e) => handleInput(e, form, 'primer_apellido')"
                        />
                      </div>
                      <span
                        v-if="form.errors.primer_apellido"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.primer_apellido }}
                      </span>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Segundo apellido:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.segundo_apellido.length }} /
                          {{ limitesCaracteres.segundo_apellido }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{
                          'border-universal-naranja': form.errors.segundo_apellido,
                        }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo"
                          v-model="form.segundo_apellido"
                          @blur="handleBlur(form, 'segundo_apellido')"
                          @input="(e) => handleInput(e, form, 'segundo_apellido')"
                        />
                      </div>
                      <span
                        v-if="form.errors.segundo_apellido"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.segundo_apellido }}
                      </span>
                    </div>
                  </div>

                  <!-- telefonos -->
                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
                  >
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Primer nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.primer_nombre.length }} /
                          {{ limitesCaracteres.primer_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{ 'border-universal-naranja': form.errors.primer_nombre }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan"
                          v-model="form.primer_nombre"
                          @blur="handleBlur(form, 'primer_nombre')"
                          @input="(e) => handleInput(e, form, 'primer_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.primer_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.primer_nombre }}
                      </span>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Segundo nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.segundo_nombre.length }} /
                          {{ limitesCaracteres.segundo_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{
                          'border-universal-naranja': form.errors.segundo_nombre,
                        }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo"
                          v-model="form.segundo_nombre"
                          @blur="handleBlur(form, 'segundo_nombre')"
                          @input="(e) => handleInput(e, form, 'segundo_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.segundo_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.segundo_nombre }}
                      </span>
                    </div>
                  </div>

                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
                  >
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Primer nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.primer_nombre.length }} /
                          {{ limitesCaracteres.primer_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{ 'border-universal-naranja': form.errors.primer_nombre }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan"
                          v-model="form.primer_nombre"
                          @blur="handleBlur(form, 'primer_nombre')"
                          @input="(e) => handleInput(e, form, 'primer_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.primer_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.primer_nombre }}
                      </span>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Segundo nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.segundo_nombre.length }} /
                          {{ limitesCaracteres.segundo_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{
                          'border-universal-naranja': form.errors.segundo_nombre,
                        }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo"
                          v-model="form.segundo_nombre"
                          @blur="handleBlur(form, 'segundo_nombre')"
                          @input="(e) => handleInput(e, form, 'segundo_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.segundo_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.segundo_nombre }}
                      </span>
                    </div>
                  </div>

                  <div
                    class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
                  >
                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Primer nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.primer_nombre.length }} /
                          {{ limitesCaracteres.primer_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{ 'border-universal-naranja': form.errors.primer_nombre }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Juan"
                          v-model="form.primer_nombre"
                          @blur="handleBlur(form, 'primer_nombre')"
                          @input="(e) => handleInput(e, form, 'primer_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.primer_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.primer_nombre }}
                      </span>
                    </div>

                    <div class="2xl:w-[50%] xl:w-[50%] w-full">
                      <div class="contador-label flex items-center justify-between">
                        <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                          Segundo nombre:
                        </p>
                        <p
                          class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light"
                        >
                          {{ form.segundo_nombre.length }} /
                          {{ limitesCaracteres.segundo_nombre }}
                        </p>
                      </div>

                      <div
                        class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                        t
                        :class="{
                          'border-universal-naranja': form.errors.segundo_nombre,
                        }"
                      >
                        <span
                          class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                          >format_italic</span
                        >

                        <input
                          type="text"
                          class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                          placeholder="Ej: Guarnizo"
                          v-model="form.segundo_nombre"
                          @blur="handleBlur(form, 'segundo_nombre')"
                          @input="(e) => handleInput(e, form, 'segundo_nombre')"
                        />
                      </div>
                      <span
                        v-if="form.errors.segundo_nombre"
                        class="2xl:text-sm text-universal-naranja"
                      >
                        {{ form.errors.segundo_nombre }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div
                class="rounded-[15px] cajaheader flex justify-between items-center dark:bg-secundary-opacity bg-mono-blanco_opacity p-5"
              >
                <h4
                  class="text-[45px] font-medium text-secundary-default dark:text-mono-blanco"
                >
                  Estás editando tu perfil
                </h4>
                <a
                  :href="route('aplicacion.miPerfil.editarMiPerfil', { aplicacion, rol })"
                >
                  <button :class="bgClase" class="flex items-center gap-1 p-2 rounded-lg">
                    Estoy seguro, actualizar
                    <span class="material-symbols-rounded text-[20px]">check_circle</span>
                  </button>
                </a>
              </div>
              <div
                class="rounded-[15px] p-5 cajaTienda dark:bg-secundary-opacity bg-mono-blanco_opacity"
              >
                <div
                  class="mb-4 grid place-content-center foto w-[390px] h-[170px] rounded-[18px] bg-mono-blanco_opacity dark:bg-secundary-opacity backdrop-blur-lg"
                >
                  <template v-if="authStore.rutaFotoEstablecimiento !== 'Sin foto'">
                    <div class="relative w-[370px] h-[150px] group">
                      <img
                        :src="authStore.rutaFotoEstablecimiento"
                        class="rounded-[18px] w-full h-full object-cover shadow-lg"
                      />
                    </div>
                  </template>

                  <template v-else>
                    <div
                      class="w-[370px] h-[150px] rounded-[18px] grid place-content-center"
                      :class="[bgClase]"
                    >
                      <p class="text-[60px] font-semibold">{{ inicialesNombreTienda }}</p>
                    </div>
                  </template>
                </div>
                <div class="flex justify-between items-center">
                  <h4 class="text-secundary-default dark:text-mono-blanco">
                    Editar mi establecimiento
                  </h4>
                </div>
              </div>
            </div>
          </form>
        </div></SidebarSuperAdmin
      >
    </div>
  </div>
</template>
