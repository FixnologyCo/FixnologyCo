<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Colors from '@/Composables/ModularColores';

const { NombreApp, bgClase, bgOpacity, textoClase, focus, buttonLink, hover } = Colors();

const props = defineProps({
    auth: Object,
    cantidadApps: {
        type: Number,
        default: 0
    },
    cantidadClientesRol1: {
        type: Number,
        default: 0
    },
    usuariosRol4: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const nombre_tienda = props.auth?.user?.tienda?.nombre_tienda || 'Sin tienda';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol

// Normaliza las rutas para que la comparación funcione
const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);
const dashboardRoute = computed(() => new URL(route('aplicacion.dashboard', { aplicacion, rol }), window.location.origin).pathname);
const clientesFixRoute = computed(() => new URL(route('aplicacion.clientesFix', { aplicacion, rol }), window.location.origin).pathname);
const configuracionesRoute = computed(() => new URL(route('aplicacion.configuraciones', { aplicacion, rol }), window.location.origin).pathname);
// const multisucursalRoute = computed(() => new URL(route('aplicacion.multisucursales', { aplicacion, rol }), window.location.origin).pathname);
// const overviewRoute = computed(() => new URL(route('aplicacion.overviews', { aplicacion, rol }), window.location.origin).pathname);
// const gastosRoute = computed(() => new URL(route('aplicacion.gastos', { aplicacion, rol }), window.location.origin).pathname);
// const usuariosRoute = computed(() => new URL(route('aplicacion.usuarios', { aplicacion, rol }), window.location.origin).pathname);
// const reservasRoute = computed(() => new URL(route('aplicacion.reservas', { aplicacion, rol }), window.location.origin).pathname);
// const inventarioRoute = computed(() => new URL(route('aplicacion.inventarios', { aplicacion, rol }), window.location.origin).pathname);
// const infoProductosRoute = computed(() => new URL(route('aplicacion.infoProductos', { aplicacion, rol }), window.location.origin).pathname);
// const productosRoute = computed(() => new URL(route('aplicacion.crearProductos', { aplicacion, rol }), window.location.origin).pathname);
// const infoCategoriasRoute = computed(() => new URL(route('aplicacion.infoCategorias', { aplicacion, rol }), window.location.origin).pathname);
// const categoriasRoute = computed(() => new URL(route('aplicacion.crearCategorias', { aplicacion, rol }), window.location.origin).pathname);
// const codigoBarrasRoute = computed(() => new URL(route('aplicacion.codigoBarras', { aplicacion, rol }), window.location.origin).pathname);
// const generadorQrsRoute = computed(() => new URL(route('aplicacion.generadorQrs', { aplicacion, rol }), window.location.origin).pathname);
// const ordenTrabajoRoute = computed(() => new URL(route('aplicacion.ordenTrabajos', { aplicacion, rol }), window.location.origin).pathname);
// const resenasRoute = computed(() => new URL(route('aplicacion.resenas', { aplicacion, rol }), window.location.origin).pathname);
const diasRestantes = computed(() => props.auth.user?.tienda?.pagos_membresia?.dias_restantes ?? 'sin días');
const inicialesNombreTienda = computed(() => {
    const nombreTienda = props.auth.user?.tienda?.nombre_tienda || '';

    const inicialTienda = nombreTienda.split(' ')[0]?.charAt(0).toUpperCase() || '';
    return inicialTienda;
});


const sidebarExpandido = ref(false); // true = expandido, false = colapsado

</script>


<template>
    <div :class="[sidebarExpandido ? 'w-[20%]' : 'w-[58px]']"
        class="min-h-[100vh] bg-secundary-default dark:bg-secundary-opacity text-mono-blanco p-3 transition-all duration-300 relative">
        <aside class="relative h-full">

            <div class="infoTienda flex items-center justify-between gap-2 px-2">
                <div v-if="sidebarExpandido" class="flex gap-2 items-center">
                    <div
                        class="user h-[25px] w-[25px] rounded-[5px] overflow-hidden flex items-center justify-center bg-slate-500">
                        <span class="text-md font-bold">
                            {{ inicialesNombreTienda }}
                        </span>
                    </div>
                    <div class="logo">
                        <h1 class="font-semibold text-[14px]">FixnologyCO</h1>
                    </div>
                </div>

                <button @click="sidebarExpandido = !sidebarExpandido">
                    <span class="material-symbols-rounded">
                        {{ sidebarExpandido ? 'right_panel_open' : 'left_panel_open' }}
                    </span>
                </button>
            </div>


            <div class="tienda rounded-md border px-2 py-1 mt-3 flex items-center gap-2">
                <div class="user h-[20px] w-[20px] rounded-full overflow-hidden flex items-center justify-center"
                    :class="[bgClase]">
                </div>
                <div v-if="sidebarExpandido" class="nombreApp">
                    <h2 class="text-[14px]">{{ nombre_tienda }}</h2>
                    <p class="text-[12px] -mt-[3px]">{{ diasRestantes }} días restantes</p>
                </div>

            </div>

            <div class="navegacion mt-5">
                <ul class="flex flex-col gap-1">
                    <li :class="[currentRoute === dashboardRoute ? [bgOpacity] : 'bg-transparent']"
                        class="px-2 py-1.5 rounded-[5px] cursor-pointer">
                        <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <span class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center">
                                        <span
                                            class="text-[18px] material-symbols-rounded text-secundary-light">space_dashboard</span>
                                    </div>
                                    <span v-if="sidebarExpandido" class="text-[14px]">Dashboard</span>
                                </div>
                                <div :class="[currentRoute === dashboardRoute ? focus : hover]"></div>
                            </span>
                        </a>
                    </li>

                    <div v-if="!sidebarExpandido" class="text-center text-secundary-light text-[10px]">Gestión</div>
                    <p v-if="sidebarExpandido" class="text-secundary-light text-[12px] mt-2.5">Gestión</p>

                    <li :class="[currentRoute === dashboardRoute ? [bgOpacity] : 'bg-transparent']"
                        class=" px-2 py-1.5 rounded-[5px] cursor-pointer">
                        <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <span class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center">
                                        <span
                                            class="text-[18px] material-symbols-rounded text-secundary-light">apps</span>
                                    </div>
                                    <span v-if="sidebarExpandido" class="text-[14px]">Mis aplicaciones</span>
                                </div>
                                <div :class="[currentRoute === dashboardRoute ? focus : hover]"></div>
                            </span>
                        </a>
                    </li>
                    <li :class="[currentRoute === dashboardRoute ? [bgOpacity] : 'bg-transparent']"
                        class=" px-2 py-1.5 rounded-[5px] cursor-pointer">
                        <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <span class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center">
                                        <span
                                            class="text-[18px] material-symbols-rounded text-secundary-light">people</span>
                                    </div>
                                    <span v-if="sidebarExpandido" class="text-[14px]">Gestor usuarios</span>
                                </div>
                                <div :class="[currentRoute === dashboardRoute ? focus : hover]"></div>
                            </span>
                        </a>
                    </li>

                    <div v-if="!sidebarExpandido" class="text-center text-secundary-light text-[10px]">Finanzas</div>
                    <p v-if="sidebarExpandido" class="text-secundary-light text-[12px] mt-2.5">Finanzas</p>

                    <li :class="[currentRoute === dashboardRoute ? [bgOpacity] : 'bg-transparent']"
                        class=" px-2 py-1.5 rounded-[5px] cursor-pointer">
                        <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <span class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center">
                                        <span
                                            class="text-[18px] material-symbols-rounded text-secundary-light">payment</span>
                                    </div>
                                    <span v-if="sidebarExpandido" class="text-[14px]">Pagos membresías</span>
                                </div>
                                <div :class="[currentRoute === dashboardRoute ? focus : hover]"></div>
                            </span>
                        </a>
                    </li>

                    <div v-if="!sidebarExpandido" class="text-center text-secundary-light text-[10px]">Seguro</div>
                    <p v-if="sidebarExpandido" class="text-secundary-light text-[12px] mt-2.5">Seguridad</p>

                    <li :class="[currentRoute === dashboardRoute ? [bgOpacity] : 'bg-transparent']"
                        class=" px-2 py-1.5 rounded-[5px] cursor-pointer">
                        <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <span class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center">
                                        <span
                                            class="text-[18px] material-symbols-rounded text-secundary-light">key</span>
                                    </div>
                                    <span v-if="sidebarExpandido" class="text-[14px]">Tokens acceso</span>
                                </div>
                                <div :class="[currentRoute === dashboardRoute ? focus : hover]"></div>
                            </span>
                        </a>
                    </li>

                </ul>
            </div>


        </aside>
    </div>

</template>
