<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { handleBlur, handleInput, limitesCaracteres } from "@/Utils/formateoInputs";
import { useTema } from "@/Composables/useTema";
import { route } from "ziggy-js";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import logoFixDark from "/resources/images/Logo_160px_dark.svg";
import logoFixWhite from "/resources/images/Logo_160px_white.svg";

const props = defineProps({
  token: String,
  correo: String,
});

const form = useForm({
  password: "",
  password_confirmation: "",
  token: props.token,
  correo: props.correo, // <-- CORREGIDO
});

const submit = () => {
  form.post(route("password.update"));
};

const { modoOscuro, animando, animarCambioTema } = useTema();
</script>

<template>
  <div class="">
    <Head title="¡Volviste!" />
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

    <div class="flex justify-center items-center min-h-[80vh]">
      <div
        class="bg-mono-blanco shadow-lg dark:shadow-md dark:bg-bg-empty w-[800px] rounded-lg py-10 px-16"
      >
        <h2 class="text-[35px] font-bold text-center dark:text-mono-blanco">
          ¡Felicidades, estas de vuelta!
        </h2>
        <p class="text-[18px] dark:text-mono-blanco -mt-2 text-center">
          Te dije que no se habia perdido nada :D
        </p>

        <div class="relative mt-3 flex justify-center items-center gap-2">
          <div class="h-[1.5px] bg-slate-300 w-[20%]"></div>
          <p class="text-gray-400 text-center">
            Ingresa una nueva contraseña de minimo 4 caracteres
          </p>
          <div class="h-[1.5px] bg-slate-300 w-[20%]"></div>
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
                  {{ form.password_confirmation.length }} /
                  {{ limitesCaracteres.password_confirm }}
                </p>
              </div>

              <div
                class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                t
                :class="{ 'border-universal-naranja': form.errors.password_confirmation }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >password</span
                >

                <input
                  type="password"
                  class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                  placeholder="Debe ser igual a la anterior"
                  v-model="form.password_confirmation"
                  @blur="handleBlur(form, 'password_confirmation')"
                  @input="(e) => handleInput(e, form, 'password_confirmation')"
                />
              </div>
              <span
                v-if="form.errors.password_confirmation"
                class="2xl:text-sm text-universal-naranja"
              >
                {{ form.errors.password_confirmation }}
              </span>
            </div>
          </div>

          <button
            type="submit"
            class="mt-2 flex items-center bg-universal-azul_secundaria px-4 py-2 rounded-md justify-center text-mono-blanco font-semibold shadowM hover:bg-universal-naranja"
          >
            Restablecer contraseña<span class="material-symbols-rounded bg-transparent"
              >bolt</span
            >
          </button>

          <p class="text-[12px] mt-3 text-universal-naranja text-center">
            Versión Deimos 1.0.0
          </p>
        </form>
      </div>
    </div>
  </div>
</template>
