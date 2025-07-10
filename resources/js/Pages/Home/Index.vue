<script setup>
import { Head } from "@inertiajs/vue3";
import { route } from "ziggy-js";

import { onMounted } from "vue";

onMounted(() => {
  // Escucha en el canal privado 'actualizaciones' el evento 'Estado.Actualizado'
  window.Echo.private("actualizaciones").listen(".Estado.Actualizado", (e) => {
    console.log("¡Evento recibido en tiempo real!");
    console.log(e.mensaje); // "¡El estado de algo ha cambiado!"
    alert("Mensaje del servidor: " + e.mensaje);
  });
});

const testBroadcast = () => {
  // Envía un evento de prueba al servidor
  fetch(route("test-broadcast"))
    .then((response) => response.text())
    .then((data) => {
      console.log(data); // "¡Evento enviado!"
      alert(data);
    })
    .catch((error) => console.error("Error al enviar el evento:", error));
};
</script>

<template>
  <Head title="Bienvenido a Fixnology CO" />
  <div>
    <header class="p-4 flex justify-between items-center">
      <div
        class="logo 2xl:flex 2xl:gap-3 2xl:items-center xl:flex xl:gap-2 xl:items-center flex items-center gap-2"
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
      <div class="btnAcciones flex items-center gap-4">
        <button class="btn-taurus">
          <a :href="route('login.auth')" class="">Inicia sesión</a>
        </button>
        <button class="rounded-lg p-1">
          <a :href="route('register.auth')" class="">Registrarme</a>
        </button>
      </div>
    </header>

    <div class="flex w-full">
    <div class="mx-5 bg-mono-blanco p-2" >
      <h1>Dashboard White:</h1>

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
     <div class="mx-5 bg-mono-negro text-mono-blanco p-2" >
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
