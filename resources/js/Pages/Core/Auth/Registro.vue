<script>
export default {
  name: "Auth",
  components: {
    Head,
  },
  mounted() {
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
      window.history.pushState(null, "", window.location.href);
      alert("Debes iniciar sesión primero.");
    };
  },
};
</script>

<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { handleBlur, handleInput, limitesCaracteres } from "@/Utils/formateoInputs";
import { useTema } from "@/Composables/useTema";
import { route } from "ziggy-js";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import logoGoogle from "/resources/images/LogoGoogle.png";
import logoFixDark from "/resources/images/Logo_160px_dark.svg";
import logoFixWhite from "/resources/images/Logo_160px_white.svg";

defineProps({});

const form = useForm({
  primer_nombre: "",
  primer_apellido: "",
  id_tipo_documento: 1,
  numero_documento: "",
  password: "",
  password_confirm: "",
});

const submit = () => {
  form.post(route("register.auth"));
};

const { modoOscuro, animando, animarCambioTema } = useTema();
</script>

<template>
  <div class="">
    <Head title="Nuevo usuario" />
    <MensajesLayout />

    <header class="p-[50px] flex justify-between items-center">
      <div
        class="logo 2xl:flex 2xl:gap-3 2xl:items-center xl:flex xl:gap-2 xl:items-center flex items-center gap-2"
      >
        <div v-if="modoOscuro">
          <img
            width="50px"
            height="50px"
            class="rounded-xl"
            :src="logoFixWhite"
            alt="Fixnology"
          />
        </div>
        <div v-else>
          <img
            width="50px"
            height="50px"
            class="rounded-xl"
            :src="logoFixDark"
            alt="Fixnology"
          />
        </div>
        <div class="logo flex flex-col gap-1">
          <h1 class="text-[25px] font-semibold text-universal-naranja">Fixnology CO</h1>
          <p class="-mt-[8px] text-[14px] font-medium dark:text-mono-blanco">
            Empresa de software especializada
          </p>
        </div>
      </div>
      <div class="btnAcciones flex items-center gap-4">
        <button
          @click="animarCambioTema"
          class="flex items-center justify-center gap-2 h-[35px] w-[35px] rounded-full border border-secundary-light text-sm transition-all duration-500 ease-in-out relative overflow-hidden"
          :class="[
            modoOscuro ? 'text-mono-blanco' : 'text-mono-negro',
            animando ? 'scale-105 shadow-lg rotate-1' : '',
          ]"
        >
          <span
            class="material-symbols-rounded text-[20px] transition-transform duration-500"
            :class="{ 'animate-spin-slow': animando }"
          >
            {{ modoOscuro ? "light_mode" : "dark_mode" }}
          </span>
          <!-- destello -->
          <span
            v-if="animando"
            class="absolute inset-0 bg-white/10 backdrop-blur-sm animate-ping z-0 rounded-md"
          ></span>
        </button>
        <a :href="route('login.auth')" class="">
          <button
            class="dark:bg-mono-blanco py-4 px-6 rounded-full shadowM font-semibold"
          >
            Iniciar sesión
          </button>
        </a>
      </div>
    </header>

    <div class="flex justify-center items-center min-h-[83vh]">
      <div
        class="bg-mono-blanco shadow-lg dark:shadow-md dark:bg-mono-negro w-[800px] rounded-lg py-10 px-16"
      >
        <h2 class="text-[35px] font-bold text-center dark:text-mono-blanco">
          Únete a la familia Fix
        </h2>
        <p class="text-[18px] dark:text-mono-blanco -mt-2 text-center">
          Simplifique su negocio en línea, software profesional y avanzado
        </p>

        <div class="btn-inicio flex justify-center gap-2 mt-5">
          <a
            :href="route('google.login')"
            class="border border-secundary-light rounded-md py-2 gap-3 w-[70%] flex items-center justify-center hover:-translate-y-1 hover:bg-secundary-default"
          >
            <img width="20px" height="20px" :src="logoGoogle" alt="Logo google" />
            <span class="dark:text-mono-blanco"
              >Inicia sesión con tu cuenta de Google</span
            >
          </a>
        </div>

        <div class="relative mt-3 flex justify-center items-center gap-2">
          <div class="h-[1.5px] bg-slate-300 w-[30%]"></div>
          <p class="text-gray-400 text-center">
            O registrate con tus nombres, identificación y contraseña
          </p>
          <div class="h-[1.5px] bg-slate-300 w-[30%]"></div>
        </div>

        <form
          @submit.prevent="submit"
          class="2xl:mt-5 2xl:flex 2xl:flex-col 2xl:gap-2 xl:mt-4 xl:flex xl:flex-col xl:gap-2 mt-3 flex flex-col gap-3"
        >
          <div
            class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
          >
            <div class="2xl:w-[50%] xl:w-[50%] w-full">
              <div class="contador-label flex items-center justify-between">
                <p class="my-[5px] text-[14px] dark:text-mono-blanco">Primer nombre:</p>
                <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
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
                <p class="my-[5px] text-[14px] dark:text-mono-blanco">Primer apellido:</p>
                <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                  {{ form.primer_apellido.length }} /
                  {{ limitesCaracteres.primer_apellido }}
                </p>
              </div>

              <div
                class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                t
                :class="{ 'border-universal-naranja': form.errors.primer_apellido }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >format_italic</span
                >

                <input
                  type="text"
                  class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                  placeholder="Ej: Guarnizo"
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
          </div>

          <div
            class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
          >
            <div class="2xl:w-[100%] xl:w-[100%] w-full">
              <div class="contador-label flex items-center justify-between">
                <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                  Número de identificación:
                </p>
                <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                  {{ form.numero_documento.length }} /
                  {{ limitesCaracteres.numero_documento }}
                </p>
              </div>

              <div
                class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                t
                :class="{ 'border-universal-naranja': form.errors.numero_documento }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >pin</span
                >

                <input
                  type="number"
                  class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                  placeholder="Ej: 123456789"
                  v-model="form.numero_documento"
                  @blur="handleBlur(form, 'numero_documento')"
                  @input="(e) => handleInput(e, form, 'numero_documento')"
                />
              </div>
              <span
                v-if="form.errors.numero_documento"
                class="2xl:text-sm text-universal-naranja"
              >
                {{ form.errors.numero_documento }}
              </span>
            </div>
          </div>

          <div
            class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
          >
            <div class="2xl:w-[50%] xl:w-[50%] w-full">
              <div class="contador-label flex items-center justify-between">
                <p class="my-[5px] text-[14px] dark:text-mono-blanco">Contraseña:</p>
                <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                  {{ form.password.length }} /
                  {{ limitesCaracteres.password }}
                </p>
              </div>

              <div
                class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                t
                :class="{ 'border-universal-naranja': form.errors.password }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >password</span
                >

                <input
                  type="password"
                  class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                  placeholder="Mínimo 8 caracteres"
                  v-model="form.password"
                  @blur="handleBlur(form, 'password')"
                  @input="(e) => handleInput(e, form, 'password')"
                />
              </div>
              <span
                v-if="form.errors.password"
                class="2xl:text-sm text-universal-naranja"
              >
                {{ form.errors.password }}
              </span>
            </div>

            <div class="2xl:w-[50%] xl:w-[50%] w-full">
              <div class="contador-label flex items-center justify-between">
                <p class="my-[5px] text-[14px] dark:text-mono-blanco">
                  Repite tu contraseña:
                </p>
                <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-secundary-light">
                  {{ form.password_confirm.length }} /
                  {{ limitesCaracteres.password_confirm }}
                </p>
              </div>

              <div
                class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                t
                :class="{ 'border-universal-naranja': form.errors.password_confirm }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >password</span
                >

                <input
                  type="password"
                  class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                  placeholder="Debe ser igual a la anterior"
                  v-model="form.password_confirm"
                  @blur="handleBlur(form, 'password_confirm')"
                  @input="(e) => handleInput(e, form, 'password_confirm')"
                />
              </div>
              <span
                v-if="form.errors.password_confirm"
                class="2xl:text-sm text-universal-naranja"
              >
                {{ form.errors.password_confirm }}
              </span>
            </div>
          </div>
          <a
            href="https://api.whatsapp.com/send/?phone=573219631459&text=Vengo+desde+la+app%2C+quiero+activar+mi+token+por+favor.&type=phone_number&app_absent=0"
            class="2xl:my-3 text-[14px] text-right text-universal-azul_secundaria"
            >Contactanos para la activación</a
          >

          <button
            type="submit"
            class="flex items-center bg-universal-azul_secundaria px-4 py-2 rounded-md justify-center text-mono-blanco font-semibold shadowM hover:bg-universal-naranja"
          >
            Crear cuenta <span class="material-symbols-rounded bg-transparent">bolt</span>
          </button>

          <p class="text-[12px] mt-3 text-universal-naranja text-center">
            Versión Deimos 1.0.0
          </p>
        </form>
      </div>
    </div>
  </div>
</template>
