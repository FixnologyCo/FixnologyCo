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
import logoGit from "/resources/images/LogoGitHub.svg";

defineProps({});

const form = useForm({
  primer_nombre: "",
  primer_apellido: "",
  id_tipo_documento: 1,
  numero_identificacion: "",
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
    <Head title="Inicia Sesion" />
    <MensajesLayout />

    <header class="p-[50px] flex justify-between items-center">
      <div
        class="logo 2xl:flex 2xl:gap-3 2xl:items-center xl:flex xl:gap-2 xl:items-center flex items-center gap-2"
      >
        <div
          class="gota 2xl:h-7 2xl:w-10 xl:h-6 xl:w-9 h-5 w-8 rounded-full shadow-universal-naranja bg-universal-naranja"
        ></div>
        <div class="logo">
          <h1 class="text-[20px] font-semibold dark:text-mono-blanco">Fixnology CO</h1>
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

        <button class="dark:bg-mono-blanco py-4 px-6 rounded-full shadowM font-semibold">
          <a :href="route('register.auth')" class="">Registrarme</a>
        </button>
      </div>
    </header>

    <div class="flex justify-center items-center">
      <div
        class="bg-mono-blanco shadow-lg dark:shadow-sm dark:bg-bg-empty w-[800px] rounded-lg py-2 px-10"
      >
        <h2 class="text-[35px] font-bold text-center dark:text-mono-blanco">
          Únete a la familia Fix
        </h2>
        <p class="text-[18px] dark:text-mono-blanco -mt-2 text-center">
          Simplifique su negocio en línea, software profesional y avanzado
        </p>

        <div class="btn-inicio flex justify-center gap-2 mt-5">
          <button
            class="border border-secundary-light rounded-md py-2 gap-3 w-[70%] flex items-center justify-center hover:-translate-y-1 hover:bg-secundary-default"
          >
            <img width="20px" height="20px" :src="logoGoogle" alt="Logo google" />
            <span class="dark:text-mono-blanco"
              >Inicia sesión con tu cuenta de Google</span
            >
          </button>
          <button
            class="border border-secundary-light rounded-md py-2 gap-3 w-[10%] flex items-center justify-center hover:-translate-y-1 hover:bg-secundary-default"
          >
            <img width="20px" height="20px" :src="logoGit" alt="Logo google" />
          </button>
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
                  {{ form.numero_identificacion.length }} /
                  {{ limitesCaracteres.numero_identificacion }}
                </p>
              </div>

              <div
                class="w-[100%] p-[3px] flex items-center gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-ligh"
                t
                :class="{ 'border-universal-naranja': form.errors.numero_identificacion }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >pin</span
                >

                <input
                  type="number"
                  class="2xl:w-full focus:outline-none focus:border-none font-normal bg-transparent dark:text-mono-blanco"
                  placeholder="Ej: 123456789"
                  v-model="form.numero_identificacion"
                  @blur="handleBlur(form, 'numero_identificacion')"
                  @input="(e) => handleInput(e, form, 'numero_identificacion')"
                />
              </div>
              <span
                v-if="form.errors.numero_identificacion"
                class="2xl:text-sm text-universal-naranja"
              >
                {{ form.errors.numero_identificacion }}
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

    <div
      class="hidden bg-mono-blanco dark:bg-mono-negro text-secundary-default dark:text-mono-blanco sm:bg-green-500 md:bg-yellow-500 lg:bg-red-500 xl:bg-mono-blanco xl:dark:bg-mono-negro 2xl:bg-mono-blanco 2xl:dark:bg-mono-negro flex justify-center items-center"
    >
      <main
        class="2xl:w-[100%] 2xl:p-[80px] 2xl:gap-16 xl:w-[100%] xl:px-[80px] xl:gap-14 min-h-[100vh] flex items-center justify-center w-[100%] p-[40px] gap-14"
      >
        <div class="left 2xl:w-[70%] xl:w-[65%] max-w-[600px]">
          <div class="options flex gap-1 items-center text-[14px] mt-4">
            <a
              :href="route('home.index')"
              class="hover:text-universal-azul flex items-center"
            >
              <span class="material-symbols-rounded text-[18px]">chevron_left</span>
              <p>Home</p>
            </a>
          </div>
          <div
            class="logo 2xl:flex 2xl:gap-3 2xl:items-center xl:flex xl:gap-2 xl:items-center flex items-center gap-2 justify-center"
          >
            <div
              class="gota 2xl:h-7 2xl:w-10 xl:h-6 xl:w-9 h-5 w-8 rounded-full shadow-universal-naranja bg-universal-naranja"
            ></div>
            <div class="logo">
              <h1 class="text-[20px] font-semibold">Fixnology CO</h1>
              <p class="-mt-[8px] text-[14px] font-medium">
                Empresa de software especializada
              </p>
            </div>
          </div>

          <div class="welcome">
            <h2
              class="2xl:text-[35px] d 2xl:mt-[20px] xl:text-[35px] d xl:mt-[20px] font-bold text-[22px] mt-3 text-center"
            >
              Bienvenido Nuevamente 👋
            </h2>
            <p class="2xl:text-[20px] xl:text-[20px] text-[15px] text-center px-8">
              Hoy es un excelente día para producir, inicia sesión y sácale el jugo a tu
              App.
            </p>
          </div>

          <!-- ✅ FORMULARIO DE LOGIN -->
          <form @submit.prevent="submit" class="mt-5 flex flex-col gap-5">
            <!-- ✅ Campo Usuario -->
            <div class="w-[100%]">
              <p class="my-[5px] text-[14px]">Usuario:</p>
              <div
                class="w-[100%] transition-all rounded-[5px] border-[1px] border-secundary-light p-[3px] flex items-center gap-[8px]"
                :class="{ 'border-universal-naranja': form.errors.numero_documento_ct }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >people</span
                >
                <input
                  type="text"
                  v-model="form.numero_documento_ct"
                  class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                  placeholder="Ingresa tu usuario establecido."
                />
              </div>

              <span
                v-if="form.errors.numero_documento_ct"
                class="text-universal-naranja text-sm"
              >
                {{ form.errors.numero_documento_ct }}
              </span>
            </div>

            <!-- ✅ Campo Contraseña -->
            <div class="w-[100%]">
              <p class="my-[5px] text-[14px]">Contraseña:</p>
              <div
                class="w-[100%] transition-all rounded-[5px] border-[1px] border-secundary-light p-[3px] flex items-center gap-[8px]"
                :class="{ 'border-universal-naranja': form.errors.primer_nombre }"
              >
                <span
                  class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]"
                  >password</span
                >
                <input
                  type="password"
                  v-model="form.primer_nombre"
                  class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                  placeholder="Ingresa tu contraseña."
                />
              </div>
              <span
                v-if="form.errors.primer_nombre"
                class="text-universal-naranja text-sm"
              >
                {{ form.errors.primer_nombre }}
              </span>
            </div>

            <a
              :href="route('linkRecuperacion.auth')"
              class="text-universal-azul text-right"
              >Olvidé mi contraseña</a
            >

            <button type="submit" class="btn-taurus text-mono-blanco">
              Iniciar sesión
              <span class="material-symbols-rounded bg-transparent">bolt</span>
            </button>

            <p class="mt-4 text-center">
              ¿No tienes una cuenta?,
              <a :href="route('register.auth')" class="text-universal-azul"
                >Registrate aquí</a
              >.
            </p>

            <p class="text-[12px] text-center text-universal-azul">
              Versión Deimos 1.0.0
            </p>
          </form>
        </div>
      </main>
    </div>
  </div>
</template>
