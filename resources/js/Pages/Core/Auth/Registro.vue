<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import { useTema } from "@/Composables/useTema";
import { route } from "ziggy-js";
import MensajesLayout from "@/Layouts/MensajesLayout.vue";
import InputTexto from "@/Components/Shared/inputs/InputTexto.vue";
import logoGoogle from "/resources/images/LogoGoogle.png";
import logoFixDark from "/resources/images/Logo_160px_dark.svg";
import logoFixWhite from "/resources/images/Logo_160px_white.svg";
import { handleBlur, handleInput, limitesCaracteres } from "@/Utils/formateoInputs";
import BtnPrimario from "@/Components/Shared/buttons/btnPrimario.vue";
import ConfirmacionesPop from "@/Components/Modales/Confirmaciones/ConfirmacionesPop.vue";
import Selects from "@/Components/Shared/inputs/Selects.vue";

const props = defineProps({
  appsDisponibles: {
    type: Array,
    default: null,
  },
});

const form = useForm({
  primer_nombre: "",
  primer_apellido: "",
  numero_documento: "",
  password: "",
  password_confirm: "",
  aplicacion_web_id: "",
});

const submit = () => {
  form.post(route("register.auth"));
};

const { modoOscuro, animando, animarCambioTema } = useTema();

function closeConfirmationModal() {
  confirmationState.value.isOpen = false;
}

function handleConfirm() {
  confirmationState.value.onConfirm();
  closeConfirmationModal();
}

const confirmationState = ref({
  isOpen: false,
  title: "",
  message: "",
  icon: "",
  iconBgClass: "",
  confirmText: "",
  onConfirm: () => {},
});

function openConfirmationModal({
  title,
  message,
  onConfirm,
  icon = "help",
  iconBgClass = "bg-gray-700",
  confirmText = "Confirmar",
}) {
  confirmationState.value = {
    isOpen: true,
    title,
    message,
    icon,
    iconBgClass,
    confirmText,
    onConfirm,
  };
}

const handlePopState = () => {
  window.history.pushState(null, "", window.location.href);
  openConfirmationModal({
    title: "¡Acción Requerida!",
    message: "Debes iniciar sesión primero.",
    icon: "lock",
    iconBgClass: "bg-semaforo-rojo",
    confirmText: "Entendido",
    onConfirm: closeConfirmationModal,
  });
};

onMounted(() => {
  window.history.pushState(null, "", window.location.href);
  window.addEventListener("popstate", handlePopState);
});

onUnmounted(() => {
  window.removeEventListener("popstate", handlePopState);
});
</script>

<template>
  <div class="">
    <Head title="Nuevo usuario" />
    <MensajesLayout />

    <header class="p-[50px] flex justify-between items-center">
      <a :href="route('home.index')" class="">
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
      </a>
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
            <InputTexto
              v-model="form.primer_nombre"
              label="Primer nombre:"
              icon="format_italic"
              type="text"
              placeholder="Ingresa tu nombre"
              :maxLength="limitesCaracteres.nombresUsuario"
              :error="form.errors.primer_nombre"
              @blur="handleBlur(form, 'primer_nombre')"
              @input="(e) => handleInput(e, form, 'primer_nombre')"
            />

            <InputTexto
              v-model="form.primer_apellido"
              label="Primer apellido:"
              icon="format_italic"
              type="text"
              placeholder="Ingresa tu apellido"
              :maxLength="limitesCaracteres.nombresUsuario"
              :error="form.errors.primer_apellido"
              @blur="handleBlur(form, 'primer_apellido')"
              @input="(e) => handleInput(e, form, 'primer_apellido')"
            />
          </div>

          <div
            class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
          >
            <InputTexto
              v-model="form.numero_documento"
              label="Número de identificación:"
              icon="pin"
              type="number"
              placeholder="Ingresa tu documento de identidad"
              :maxLength="limitesCaracteres.numero_documento"
              :error="form.errors.numero_documento"
              @blur="handleBlur(form, 'numero_documento')"
              @input="(e) => handleInput(e, form, 'numero_documento')"
            />

            <Selects
              v-model="form.aplicacion_web_id"
              :options="props.appsDisponibles"
              :error="form.errors.aplicacion_web_id"
              label="App de interes:"
              placeholder="Selecciona tu app"
              id="aplicacion_web_id"
            />
          </div>

          <div
            class="2xl:flex 2xl:flex-row 2xl:justify-between 2xl:items-center 2xl:gap-2 xl:flex xl:flex-row xl:justify-between xl:items-center xl:gap-2 gap-3 flex flex-col items-center"
          >
            <InputTexto
              v-model="form.password"
              label="Crea una contraseña:"
              icon="password"
              type="password"
              placeholder="Crea una contraseña. (Qué recuerdes)"
              :maxLength="limitesCaracteres.password"
              :error="form.errors.password"
              @blur="handleBlur(form, 'password')"
              @input="(e) => handleInput(e, form, 'password')"
            />

            <InputTexto
              v-model="form.password_confirm"
              label="Confirma tu contraseña:"
              icon="password"
              type="password"
              placeholder="Ingresa la contraseña anterior"
              :maxLength="limitesCaracteres.password_confirm"
              :error="form.errors.password_confirm"
              @blur="handleBlur(form, 'password_confirm')"
              @input="(e) => handleInput(e, form, 'password_confirm')"
            />
          </div>
          <a
            href="https://api.whatsapp.com/send/?phone=573219631459&text=Vengo+desde+la+app%2C+quiero+activar+mi+token+por+favor.&type=phone_number&app_absent=0"
            class="2xl:my-3 text-[14px] text-right text-universal-naranja hover:text-universal-azul_secundaria"
            >Contactanos para la activación</a
          >

          <BtnPrimario type="submit" class="w-[100%]"> ¡Empecemos! </BtnPrimario>

          <p class="text-[12px] mt-3 text-universal-naranja text-center">
            Versión Deimos 1.0.0
          </p>
        </form>
      </div>
    </div>
  </div>
  <ConfirmacionesPop
    :is-open="confirmationState.isOpen"
    :title="confirmationState.title"
    :message="confirmationState.message"
    :icon="confirmationState.icon"
    :icon-bg-class="confirmationState.iconBgClass"
    :confirm-text="confirmationState.confirmText"
    @close="closeConfirmationModal"
    @confirm="handleConfirm"
  />
</template>
