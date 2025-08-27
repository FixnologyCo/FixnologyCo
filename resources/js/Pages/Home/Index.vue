<script setup>
import BtnPrimario from "@/Components/Shared/buttons/btnPrimario.vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { route } from "ziggy-js";
import { useTema } from "@/Composables/useTema";
import logoFixDark from "/resources/images/Logo_160px_dark.svg";
import logoFixWhite from "/resources/images/Logo_160px_white.svg";
const { modoOscuro, animando, animarCambioTema } = useTema();

const props = defineProps({
  usuario: {
    type: Object,
    default: null,
  },
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth.user);

const aplicacion = computed(
  () => props.usuario?.establecimiento_asignado?.aplicacion_web?.nombre_app
);
const rol = computed(() => props.usuario?.perfil_usuario?.rol?.tipo_rol);
const primerNombre = computed(() => props.usuario?.perfil_usuario?.primer_nombre);
</script>

<template>
  <div class="p-[50px]">
    <Head title="Bienvenido a Fixnology CO" />

    <header class="p-4 flex justify-between items-center">
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
        <div v-if="!isAuthenticated" class="flex items-center gap-4">
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
              <span
                v-if="animando"
                class="absolute inset-0 bg-white/10 backdrop-blur-sm animate-ping z-0 rounded-md"
              ></span>
            </button>

            <a :href="route('register.auth')" class="">
              <button
                class="dark:bg-mono-blanco py-4 px-6 rounded-full shadowM font-semibold"
              >
                Registrarme
              </button>
            </a>
          </div>
        </div>

        <div v-else class="flex items-center gap-4">
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
            <span
              v-if="animando"
              class="absolute inset-0 bg-white/10 backdrop-blur-sm animate-ping z-0 rounded-md"
            ></span>
          </button>

          <a
            v-if="aplicacion && rol"
            :href="
              route('aplicacion.dashboard', {
                aplicacion: aplicacion,

                rol: rol,
              })
            "
            class="btn-taurus"
          >
            <button
              class="dark:bg-mono-blanco py-4 px-6 rounded-full shadowM font-semibold"
              type="submit"
            >
              {{ primerNombre }}, ¡regresa a tu app!
            </button>
          </a>
        </div>
      </div>
    </header>

    <div class="flex w-full">
      <div class="mx-5 bg-mono-blanco p-2">
        <h1>Dashboard White:</h1>

        <h1>Monocromaticos:</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-mono-negro"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-mono-blanco border"></div>
        </div>
        <div class="w-[200px] h-[200px] rounded-xl bg-primary"></div>

        <h1>Universales:</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-bg-empty"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-naranja"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-naranja_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-azul"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-azul_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-azul_secundaria"></div>
        </div>

        <h1>Semaforos</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-verde"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-verde_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-amarillo"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-amarillo_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-rojo"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-rojo_opacity"></div>
        </div>

        <h1>Secundarios</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-secundary-default"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-secundary-light"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-secundary-opacity"></div>
        </div>
      </div>

      <div class="mx-5 bg-mono-negro text-mono-blanco p-2">
        <h1 class="">Dashboard Black:</h1>

        <h1>Monocromaticos:</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-mono-negro"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-mono-blanco border"></div>
        </div>

        <h1>Universales:</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-bg-empty"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-naranja"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-naranja_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-azul"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-azul_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-universal-azul_secundaria"></div>
        </div>

        <h1>Semaforos</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-verde"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-verde_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-amarillo"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-amarillo_opacity"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-rojo"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-semaforo-rojo_opacity"></div>
        </div>

        <h1>Secundarios</h1>

        <div class="paleta flex gap-2 flex-wrap">
          <div class="w-[200px] h-[200px] rounded-xl bg-secundary-default"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-secundary-light"></div>

          <div class="w-[200px] h-[200px] rounded-xl bg-secundary-opacity"></div>
        </div>
      </div>
    </div>
  </div>
</template>
