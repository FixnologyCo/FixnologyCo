<script>
export default {
  name: 'Auth',
  components: {
    Head,
  },
  mounted() {
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () {
      window.history.pushState(null, '', window.location.href);
      alert('Debes iniciar sesión primero.');
    };
  }
}

</script>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';

const form = useForm({
  numero_documento_ct: '',
  contrasenia_ct: ''
})

const submit = () => {
  form.post(route('login.auth'), {
    onFinish: () => form.reset('contrasenia_ct'),
  })
}

</script>

<template>
  <div>

    <Head title="Inicia Sesion" />
    <MensajesLayout />


    <div class="
    bg-mono-negro
            sm:bg-green-500 
            md:bg-yellow-500
            lg:bg-red-500 
            xl:bg-mono-negro 
            2xl:bg-mono-negro
            flex justify-center items-center
    ">
      <main class="
       2xl:w-[100%] 2xl:p-[80px] 2xl:gap-16
      xl:w-[100%] xl:px-[80px] xl:gap-14
      min-h-[100vh] flex items-center justify-center w-[100%] p-[40px] gap-14
      ">
        <div class="
        left 
        2xl:w-[70%]
        xl:w-[65%]
        max-w-[600px]
        ">
          <div class="options flex gap-1 items-center text-[14px] mt-4">
            <a :href="route('home.index')" class="hover:text-universal-azul flex items-center">
              <span class="material-symbols-rounded text-[18px]">chevron_left</span>
              <p>Home</p>
            </a>
          </div>
          <div class="logo
          2xl:flex 2xl:gap-3 2xl:items-center
          xl:flex xl:gap-2 xl:items-center
          flex items-center gap-2 justify-center
          ">
            <div class="gota
             2xl:h-7 2xl:w-10
            xl:h-6 xl:w-9
            h-5 w-8 rounded-full shadow-universal-naranja bg-universal-naranja
            "></div>
            <div class="logo">
              <h1 class="text-[20px] font-semibold">Fixnology CO</h1>
              <p class="-mt-[8px] text-[14px] font-medium">Empresa de software especializada</p>
            </div>
          </div>

          <div class="welcome">

            <h2 class="
            2xl:text-[35px] d 2xl:mt-[20px]
            xl:text-[35px] d xl:mt-[20px]
            font-bold text-[22px] mt-3 text-center
            
            ">Bienvenido Nuevamente 👋</h2>
            <p class="
            2xl:text-[20px]
            xl:text-[20px]
            text-[15px]
            text-center
            px-8
            ">Hoy es un excelente día para producir, inicia sesión y sácale el jugo a tu App.</p>
          </div>



          <!-- ✅ FORMULARIO DE LOGIN -->
          <form @submit.prevent="submit" class="mt-5 flex flex-col gap-5">
            <!-- ✅ Campo Usuario -->
            <div class="w-[100%]">
              <p class="my-[5px] text-[14px]">Usuario:</p>
              <div
                class="w-[100%] transition-all rounded-[5px] border-[1px] border-secundary-light p-[3px] flex items-center gap-[8px]"
                :class="{ 'border-universal-naranja': form.errors.numero_documento_ct }">
                <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">people</span>
                <input type="text" v-model="form.numero_documento_ct"
                  class="w-full focus:outline-none focus:border-none font-normal bg-mono-negro text-blanco"
                  placeholder="Ingresa tu usuario establecido." />
              </div>
              <span v-if="form.errors.contrasenia_ct" class="text-universal-naranja text-sm">
                {{ form.errors.contrasenia_ct }}
              </span>
              <span v-if="form.errors.numero_documento_ct" class="text-universal-naranja text-sm">
                {{ form.errors.numero_documento_ct }}
              </span>
            </div>

            <!-- ✅ Campo Contraseña -->
            <div class="w-[100%]">
              <p class="my-[5px] text-[14px]">Contraseña:</p>
              <div
                class="w-[100%] transition-all rounded-[5px] border-[1px] border-secundary-light p-[3px] flex items-center gap-[8px]"
                :class="{ 'border-universal-naranja': form.errors.contrasenia_ct }">
                <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">password</span>
                <input type="password" v-model="form.contrasenia_ct"
                  class="w-full focus:outline-none focus:border-none font-normal bg-mono-negro text-blanco"
                  placeholder="Ingresa tu contraseña." />
              </div>
              <span v-if="form.errors.contrasenia_ct" class="text-universal-naranja text-sm">
                {{ form.errors.contrasenia_ct }}
              </span>
            </div>

            <a :href="route('linkRecuperacion.auth')" class="text-universal-azul text-right">Olvidé mi contraseña</a>

            <!-- ✅ BOTÓN DE INICIAR SESIÓN -->
            <button type="submit" class="btn-taurus">
              Iniciar sesión
              <span class="material-symbols-rounded bg-transparent">bolt</span>
            </button>


            <p class="
            mt-4
            text-center
            ">¿No tienes una cuenta?, <a :href="route('register.auth')" class="
                text-universal-azul
                ">Registrate aquí</a>.</p>

            <p class="
             text-[12px]
             text-center
            text-universal-azul 
            ">Versión Deimos 1.0.0</p>
          </form>
        </div>


      </main>
    </div>
  </div>
</template>
