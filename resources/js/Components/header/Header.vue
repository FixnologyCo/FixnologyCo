<script setup>
import { ref, onMounted, onUnmounted, computed, } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Colors from '@/Composables/ModularColores';

const { NombreApp, bgClase, textoClase, focus, buttonLink, hover } = Colors();

const props = defineProps({
    auth: { type: Object, required: true },
});

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
        <div class="date w-[23%] flex gap-3 items-center">
            <ul class="flex items-center gap-2">
                <li :class="[currentRoute === dashboardRoute ? [bgClase] : 'bg-transparent']"
                    class="hover:bg-secundary-opacity py-1 px-2 text-[14px] rounded-full cursor-pointer">
                    <a :href="route('aplicacion.dashboard', { aplicacion, rol })">Home</a>
                </li>
                <li :class="[currentRoute === aplicacionesdRoute ? [bgClase] : 'bg-transparent']"
                    class="hover:bg-secundary-opacity py-1 px-2 rounded-full cursor-pointer">
                    <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">Apps</a>
                </li>
                <li :class="[currentRoute === clientesFixRoute ? [bgClase] : 'bg-transparent']"
                    class="hover:bg-secundary-opacity py-1 px-2 rounded-full cursor-pointer">
                    <a :href="route('aplicacion.clientesFix', { aplicacion, rol })">Usuarios</a>
                </li>
            </ul>
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


            <div class="user h-[40px] w-[40px] rounded-full overflow-hidden flex items-center justify-center"
                :class="[bgClase]">
                <span class="text-md font-bold">
                    {{ initials }}
                </span>
            </div>
            <div class="usuario">
                <div v-if="auth && auth.user">
                    <h3 class="font-semibold"> {{ auth.user.nombres_ct }} </h3>
                    <p class="-mt-[5px] text-secundary-light text-[13px] font-medium">
                        {{ auth.user.rol?.tipo_rol || 'Sin rol' }}
                    </p>
                </div>
                <div v-else>
                    <p>Cargando información del usuario...</p>
                </div>
            </div>
            <span class="material-symbols-rounded text-[20px]">keyboard_arrow_down</span>
        </div>
    </header>
</template>
