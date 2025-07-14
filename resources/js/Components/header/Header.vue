<script setup>
import { ref, onMounted, onUnmounted, computed, } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Colors from '@/Composables/ModularColores';
import { useTema } from "@/Composables/useTema";
const { modoOscuro, animando, animarCambioTema } = useTema();



const { NombreApp, bgClase, textoClase, focus, buttonLink, hoverTexto, hoverClase } = Colors();

const props = defineProps({
    auth: { type: Object, required: true },
    foto_base64: String,
});

const page = usePage();

const usuario = props.auth
const usuarioAuth = usuario?.user?.perfil_usuario

const rolUsuario = usuario.user.rol.tipo_rol 
const aplicacionUsuario = usuario.user.tienda[0].aplicacion_web.nombre_app
const nombreTiendaUsuario = usuario.user.tienda[0].nombre_establecimiento 

const aplicacion = aplicacionUsuario || "Sin app";
const nombre_tienda = nombreTiendaUsuario || "Sin tienda";
const rol = rolUsuario  || "Sin rol";

const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);
const dashboardRoute = computed(() => new URL(route('aplicacion.dashboard', { aplicacion, rol }), window.location.origin).pathname);
const clientesFixRoute = computed(() => new URL(route('aplicacion.clientesFix', { aplicacion, rol }), window.location.origin).pathname);
const configuracionesRoute = computed(() => new URL(route('aplicacion.configuraciones', { aplicacion, rol }), window.location.origin).pathname);
// const historialRoute = computed(() => new URL(route('aplicacion.historial', { aplicacion, rol }), window.location.origin).pathname);

const iconosPorComponente = {
    Dashboard: 'dashboard',
    MisApps: 'apps',
    GestorUsuarios: 'group',
    DetallesUsuario: 'digital_wellbeing',
    Productos: 'inventory',
    Reportes: 'bar_chart',
    Historial: 'history',
    Configuraciones: 'settings',
    EditarMiPerfil: 'account_circle',
}

const componente = usePage().component.split('/').pop();

function separarCamelCase(texto) {
    const conEspacios = texto.replace(/([a-z])([A-Z])/g, '$1 $2');
    return conEspacios.charAt(0).toUpperCase() + conEspacios.slice(1).toLowerCase();
}

const ruta = separarCamelCase(componente);
const icono = iconosPorComponente[componente] || 'description'


const initials = computed(() => {
    const nombres = usuarioAuth.primer_nombre || '';
    const apellidos = usuarioAuth.primer_apellido || '';


    const firstNameInitial = nombres.split(' ')[0]?.charAt(0).toUpperCase() || '';
    const lastNameInitial = apellidos.split(' ')[0]?.charAt(0).toUpperCase() || '';

    return firstNameInitial + lastNameInitial;
});

</script>


<template>
    <header
        class="header px-3 py-2 flex items-center justify-between gap-3 bg-mono-blanco shadow-md dark:bg-mono-negro">
        <div class="left w-[20%] rounded-md">
            <div class="infoTienda flex items-center gap-2">
                <div class="">
                    <div class="flex items-center" v-if="auth && auth.user">
                        <span class="material-symbols-rounded align-middle mr-1 " :class="[textoClase]">
                            {{ icono }}
                        </span>
                        <h1 class="text-[25px] font-semibold text-mono-negro dark:text-mono-blanco">{{ ruta }}</h1>
                    </div>
                    <div v-else>
                        <p class="text-mono-negro dark:text-mono-blanco">Cargando información del usuario...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-2 items-center">
            <button @click="animarCambioTema"
                class="flex items-center justify-center gap-2 h-[35px] w-[35px] rounded-full  border border-secundary-light text-sm transition-all duration-500 ease-in-out relative overflow-hidden"
                :class="[
                    modoOscuro ? 'text-mono-blanco' : 'text-mono-negro',
                    animando ? 'scale-105 shadow-lg rotate-1' : ''
                ]">

                <span class="material-symbols-rounded text-[20px] transition-transform duration-500"
                    :class="{ 'animate-spin-slow': animando }">
                    {{ modoOscuro ? 'light_mode' : 'dark_mode' }}
                </span>
                <!-- destello -->
                <span v-if="animando"
                    class="absolute inset-0 bg-white/10 backdrop-blur-sm animate-ping z-0 rounded-md"></span>
            </button>

            <!-- <a :href="route('aplicacion.historial', { aplicacion, rol })">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
                    :class="[currentRoute === historialRoute ? [bgClase] : 'bg-transparent' , hoverClase]">
                    <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
                        history
                    </span>
                </div>
            </a> -->


            <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer" :class="[hoverClase]">
                <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
                    help
                </span>
            </div>


            <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
                   :class="[hoverClase]">
                    <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
                        notifications
                    </span>
                </div>
            </a>
            <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center cursor-pointer"
                    :class="[currentRoute === configuracionesRoute ? [bgClase] : 'bg-transparent', hoverClase]">
                    <span class="material-symbols-rounded text-[20px] dark:text-mono-blanco">
                        settings
                    </span>
                </div>
            </a>

            <div class="flex gap-1 items-center">

                <template v-if="foto_base64">
                    <img :src="foto_base64" class="border-2 rounded-[50px] w-[40px] h-[40px] object-cover" />
                </template>

                <template v-else>
                    <div class="user bg-universal-naranja shadow shadow-universal-naranja text-mono-blanco h-[35px] w-[35px] rounded-full overflow-hidden flex items-center justify-center"
                        :class="[bgClase]">
                        <span class="text-[12px] font-bold">
                            {{ initials }}
                        </span>
                    </div>
                </template>
                <div class="usuario">
                    <div v-if="usuario && usuario">
                        <h3 class="font-semibold text-[13px] text-mono-negro dark:text-mono-blanco"> {{
                            usuarioAuth.primer_nombre }} {{ usuarioAuth.primer_apellido }}
                        </h3>
                        <p class="-mt-[5px] text-[12px] font-medium text-mono-negro dark:text-secundary-light">
                            {{ rolUsuario || 'Sin rol' }}
                        </p>
                    </div>
                    <div v-else>
                        <p class="text-mono-negro dark:text-mono-blanco">Cargando información del usuario...</p>
                    </div>
                </div>
            </div>



        </div>
    </header>
</template>
