<script setup>
import { ref, onMounted, onUnmounted, computed, } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Colors from '@/Composables/ModularColores';

const { NombreApp, bgClase, textoClase, focus, buttonLink, hover } = Colors();

const props = defineProps({
    auth: { type: Object, required: true },
    foto_base64: String,
});


const fotoBase64 = props.foto_base64;
import axios from 'axios'

const onFileChange = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('foto', file)

  try {
    await axios.post(route('usuario.actualizar.foto'), formData)
    console.log('Foto actualizada')

    // 👇 Lógica para recargar los datos del usuario
    await router.reload({
      only: ['auth'], // o el nombre del prop que estés pasando desde el backend
    })
  } catch (error) {
    console.error('Error al subir la foto:', error)
  }

}
const page = usePage();
const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol
// Normaliza las rutas para que la comparación funcione
const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);
const dashboardRoute = computed(() => new URL(route('aplicacion.dashboard', { aplicacion, rol }), window.location.origin).pathname);
const clientesFixRoute = computed(() => new URL(route('aplicacion.clientesFix', { aplicacion, rol }), window.location.origin).pathname);
const configuracionesRoute = computed(() => new URL(route('aplicacion.configuraciones', { aplicacion, rol }), window.location.origin).pathname);

const componente = usePage().component.split('/').pop();

function separarCamelCase(texto) {
    return texto.replace(/([a-z])([A-Z])/g, '$1 $2');
}

const ruta = separarCamelCase(componente);


const initials = computed(() => {
    const nombres = props.auth.user?.nombres_ct || '';
    const apellidos = props.auth.user?.apellidos_ct || '';


    const firstNameInitial = nombres.split(' ')[0]?.charAt(0).toUpperCase() || '';
    const lastNameInitial = apellidos.split(' ')[0]?.charAt(0).toUpperCase() || '';

    return firstNameInitial + lastNameInitial;
});

const inicialesNombreTienda = computed(() => {
    const nombreTienda = props.auth.user?.tienda?.nombre_tienda || '';

    const inicialTienda = nombreTienda.split(' ')[0]?.charAt(0).toUpperCase() || '';
    return inicialTienda;
});

const diasRestantes = computed(() => props.auth.user?.tienda?.pagos_membresia?.dias_restantes ?? 'sin días');

</script>

<template>
    <header class="header py-2 flex items-center justify-between gap-3">
        <div class="left w-[20%] rounded-md">
            <div class="infoTienda flex items-center gap-2">
                <div class="">
                    <div class="flex items-center" v-if="auth && auth.user">
                        <h1 class="text-[25px] font-semibold">{{ ruta }}</h1>
                    </div>
                    <div v-else>
                        <p>Cargando información del usuario...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-2 items-center">
            <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
                    :class="[currentRoute === configuracionesRoute ? [bgClase] : 'bg-transparent']">
                    <span class="material-symbols-rounded text-[20px]">
                        help
                    </span>
                </div>
            </a>
            <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
                    :class="[currentRoute === configuracionesRoute ? [bgClase] : 'bg-transparent']">
                    <span class="material-symbols-rounded text-[20px]">
                        notifications
                    </span>
                </div>
            </a>
            <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
                    :class="[currentRoute === configuracionesRoute ? [bgClase] : 'bg-transparent']">
                    <span class="material-symbols-rounded text-[20px]">
                        settings
                    </span>
                </div>
            </a>

            <div class="flex gap-1">
                
                <template v-if="foto_base64">
                        <img :src="foto_base64" class="rounded-[50px] w-[35px] h-[35px] object-cover" />
                    </template>

                    <template v-else>
                        <div class="user h-[35px] w-[35px] rounded-full overflow-hidden flex items-center justify-center"
                            :class="[bgClase]">
                            <span class="text-[12px] font-bold">
                                {{ initials }}
                            </span>
                        </div>
                    </template>
                <div class="usuario">
                    <div v-if="auth && auth.user">
                        <h3 class="font-semibold text-[13px]"> {{ auth.user.nombres_ct }} </h3>
                        <p class="-mt-[5px] text-secundary-light text-[12px] font-medium">
                            {{ auth.user.rol?.tipo_rol || 'Sin rol' }}
                        </p>
                    </div>
                    <div v-else>
                        <p>Cargando información del usuario...</p>
                    </div>
                </div>
            </div>



        </div>
    </header>
</template>
