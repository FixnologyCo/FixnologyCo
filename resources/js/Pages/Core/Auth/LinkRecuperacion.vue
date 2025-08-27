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
import { useTema } from "@/Composables/useTema";
import { route } from "ziggy-js";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";

import logoFixDark from "/resources/images/Logo_160px_dark.svg";
import logoFixWhite from "/resources/images/Logo_160px_white.svg";
import InputTexto from "@/Components/Shared/inputs/InputTexto.vue";
import { handleBlur, handleInput, limitesCaracteres } from "@/Utils/formateoInputs";
import BtnPrimario from "@/Components/Shared/buttons/btnPrimario.vue";

defineProps({});

const form = useForm({
  correo: "",
});

const submit = () => {
  form.post(route("linkRecuperacion.auth"), {
    onFinish: () => form.resetAll(),
  });
};

const { modoOscuro, animando, animarCambioTema } = useTema();
</script>

<template>
  <div class="p-[50px]">
    <Head title="Nada se ha perdido" />
    <MensajesLayout />

    <header class="flex justify-between items-center">
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
          ¡Nada se ha perdido aún!
        </h2>
        <p class="text-[18px] dark:text-mono-blanco -mt-2 text-center">
          Ingresa tu correo electrónico vinculado a tu cuenta.
        </p>

        <form
          @submit.prevent="submit"
          class="2xl:mt-5 2xl:flex 2xl:flex-col 2xl:gap-2 xl:mt-4 xl:flex xl:flex-col xl:gap-2 mt-3 flex flex-col gap-3"
        >
          <div
            class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
          >
            <InputTexto
              v-model="form.correo"
              label="Ingresa correo vinculado:"
              icon="email"
              type="email"
              placeholder="Debe ser el mismo que registraste anteriormente"
              :maxLength="limitesCaracteres.email"
              :error="form.errors.correo"
              @blur="handleBlur(form, 'correo')"
              @input="(e) => handleInput(e, form, 'correo')"
            />
          </div>

          <a
            href="https://api.whatsapp.com/send/?phone=573219631459&text=No+puedo+acceder+a+mi+app.&type=phone_number&app_absent=0"
            class="2xl:my-3 text-[14px] text-right text-universal-azul_secundaria"
            >Tengo problemas con la cuenta</a
          >

          <BtnPrimario type="submit" class="w-[100%]"> ¡Enviar enlace! </BtnPrimario>

          <p class="text-[12px] mt-3 text-universal-naranja text-center">
            Versión Deimos 1.0.0
          </p>
        </form>
      </div>
    </div>
  </div>
</template>
