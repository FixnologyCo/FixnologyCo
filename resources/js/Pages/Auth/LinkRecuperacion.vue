<script>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue'
import { route } from 'ziggy-js';

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

const page = usePage();
</script>

<script setup>

const props = defineProps({
  success: String
})

const mensajeNotificacion = ref('')
const tipoNotificacion = ref(null)
const mostrarNotificacion = ref(false)

onMounted(() => {
  if (usePage().props.flash && usePage().props.flash.success) {
    mostrarMensaje(usePage().props.flash.success, 'success');
  } else if (usePage().props.flash.error) {
    mostrarMensaje(usePage().props.flash.error, 'error');
  }
});


const mostrarMensaje = (mensaje, tipo) => {
  mensajeNotificacion.value = mensaje
  tipoNotificacion.value = tipo
  mostrarNotificacion.value = true

  setTimeout(() => {
    mostrarNotificacion.value = false
  }, 5000)
}

const form = useForm({
  correo_vinculado: ''
})



const submit = () => {
  form.post(route('validacionUsuario.post'), {
    onSuccess: () => {
      mostrarMensaje('Link enviado a tu correo', 'success')

    },
    onError: () => {
      mostrarMensaje('Error al recuperar tu cuenta, verifica los datos.', 'error')
    }
  })
}

</script>

<template>
  <div>

    <Head title="Recupera tu cuenta FixnologyCO" />

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
            <a :href="route('login.auth')" class="hover:text-universal-azul flex items-center">
              <span class="material-symbols-rounded text-[18px]">chevron_left</span>
              <p>Inicia sesión</p>
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
            
            ">Vamos a a recuperar tu cuenta</h2>
            <p class="
            2xl:text-[20px]
            xl:text-[20px]
            text-[15px]
            text-center
            px-7
            ">Por favor ingresa tu correo electrónico vinculado a tu usuario</p>
          </div>



          <!-- ✅ FORMULARIO DE LOGIN -->
          <form @submit.prevent="submit" class="mt-5 flex flex-col gap-5">
            <!-- ✅ Campo Usuario -->
            <div class="w-[100%]">
              <p class="my-[5px] text-[14px]">Correo electrónico:</p>
              <div
                class="w-[100%] transition-all rounded-[5px] border-[1px] border-secundary-light p-[3px] flex items-center gap-[8px]"
                :class="{ 'border-universal-naranja': form.errors.correo_vinculado }">
                <span class="material-symbols-rounded text-universal-naranja text-[20px] pl-[5px]">email</span>
                <input type="text" v-model="form.correo_vinculado"
                  class="w-full focus:outline-none focus:border-none font-normal bg-mono-negro text-blanco"
                  placeholder="Ingresa tu correo vinculado." />
              </div>
             
              <span v-if="form.errors.correo_vinculado" class="text-universal-naranja text-sm">
                {{ form.errors.correo_vinculado }}
              </span>
            </div>

            <!-- ✅ BOTÓN DE INICIAR SESIÓN -->
            <button type="submit" class="btn-taurus">
              Validar usuario
              <span class="material-symbols-rounded bg-transparent">bolt</span>
            </button>


            <p class="
            mt-4
            text-center
            ">¿Tienes problemas?, <a href="https://api.whatsapp.com/send/?phone=573219631459&text=Buen+día,+vengo+desde+la+app%2C+necesito+ayuda+con+mi+cuenta.&type=phone_number&app_absent=0" class="
                text-universal-azul
                ">Contactanos</a>.</p>

            <p class="
             text-[12px]
             text-center
            text-universal-azul 
            ">Versión Deimos 1.0.0</p>
          </form>
        </div>


      </main>
      <div v-if="mostrarNotificacion && tipoNotificacion === 'success'"
        class="notificacion translate-y-8 absolute w-[max-content] left-0 right-0 top-6 ml-auto mr-auto rounded-md bg-semaforo-verde_opacity text-mono-blanco shadow-semaforo-verde">
        <div class="notificacion_body flex justify-center gap-3 items-center py-3 px-2">
          <div class="flex gap-2 items-center">
            <i class="material-symbols-rounded text-semaforo-verde">check_circle</i>
            <p>{{ mensajeNotificacion }}</p>
          </div>
        </div>
        <div
          class="progreso_notificacion absolute left-1 bottom-1 h-1 scale-x-0 origin-left rounded-sm bg-semaforo-verde">
        </div>
      </div>
      <!-- ✅ Notificación de error -->
      <div v-if="mostrarNotificacion && tipoNotificacion === 'error'"
        class="notificacion translate-y-8 absolute w-[max-content] left-0 right-0 top-6 ml-auto mr-auto rounded-md bg-semaforo-rojo_opacity text-mono-blanco shadow-semaforo-verde">
        <div class="notificacion_body flex justify-center gap-3 items-center py-3 px-2">
          <div class="flex gap-2 items-center">
            <i class="material-symbols-rounded text-semaforo-rojo">cancel</i>
            <p>{{ mensajeNotificacion }}</p>
          </div>
        </div>
        <div
          class="progreso_notificacion absolute left-1 bottom-1 h-1 scale-x-0 origin-left rounded-sm bg-semaforo-rojo">
        </div>
      </div>
    </div>
  </div>
</template>
