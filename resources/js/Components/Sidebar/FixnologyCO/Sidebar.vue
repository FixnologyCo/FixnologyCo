<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Colors from '@/Composables/ModularColores';

const { NombreApp, bgClase, textoClase, focus, buttonLink, hover } = Colors();

const props = defineProps({
    auth: Object,
});

const page = usePage();

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol

// Normaliza las rutas para que la comparación funcione
const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);
const dashboardRoute = computed(() => new URL(route('aplicacion.dashboard', { aplicacion, rol }), window.location.origin).pathname);
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
</script>


<template>
    <div class="w-[20%] min-h-[100vh] bg-secundary-opacity p-3">
        <aside>
            <div class="logo">
                <div class="infoTienda flex items-center gap-2">
                    <div
                        class="user h-[50px] w-[50px] rounded-[10px] overflow-hidden flex items-center justify-center bg-slate-500">
                        <span class="text-md font-bold">
                            {{ inicialesNombreTienda }}
                        </span>
                    </div>
                    <div class="logo">
                        <h1 class="font-semibold text-[20px]">FixnologyCO</h1>
                        <p class="text-[14px] -mt-[8px] font-light">Expertos en software</p>
                    </div>
                </div>
            </div>

            <div class="navegacion mt-5">
                <ul class="flex flex-col gap-1">
                    <li :class="[currentRoute === dashboardRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">space_dashboard</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Dashboard</span>
                            </div>
                            <div :class="[currentRoute === dashboardRoute ? focus : hover]"></div>
                        </a>
                    </li>

                    <li :class="[currentRoute === aplicacionesRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">apps</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Aplicaciones</span>
                            </div>
                            <div :class="[currentRoute === aplicacionesRoute ? focus : hover]"></div>
                            <div
                                class="h-[20px] w-[30px] rounded-full bg-universal-azul_opacity grid place-content-center text-[12px]">
                                <span>0</span></div>
                        </a>
                    </li>

                    <li :class="[currentRoute === clientesFixRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">people</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Clientes Fix</span>
                            </div>
                            <div :class="[currentRoute === clientesFixRoute ? focus : hover]"></div>
                            <div
                                class="h-[20px] w-[30px] rounded-full bg-universal-azul_opacity grid place-content-center text-[12px]">
                                <span>0</span></div>
                        </a>
                    </li>

                    <li :class="[currentRoute === notificacionesRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">notifications</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Notificaciones</span>
                            </div>
                            <div :class="[currentRoute === notificacionesRoute ? focus : hover]"></div>
                            <div
                                class="h-[20px] w-[30px] rounded-full bg-universal-azul_opacity grid place-content-center text-[12px]">
                                <span>0</span></div>
                        </a>
                    </li>

                    <li :class="[currentRoute === historialRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">history</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Actividad de app</span>
                            </div>
                            <div :class="[currentRoute === historialRoute ? focus : hover]"></div>
                            <div
                                class="h-[20px] w-full max-w-[45px] rounded-full bg-semaforo-verde_opacity grid place-content-center text-[12px] ">
                                <span>Nuevo</span></div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="miembros mt-2">
                <div class="navegadorMiembros flex justify-between items-center my-2">
                    <h2>Miembros</h2>
                    <div class="botones flex items-center gap-2">
                        <div class="left grid place-content-center rounded-full w-7 h-7 bg-secundary-opacity">
                            <span class="material-symbols-rounded">chevron_left</span>
                        </div>
                        <div class="left grid place-content-center rounded-full w-7 h-7 bg-secundary-opacity">
                            <span class="material-symbols-rounded">chevron_right</span>
                        </div>
                    </div>
                </div>
                <div class="listaMiembros flex flex-col gap-2">
                    <div class="miembro-estado flex items-center justify-between">
                        <div class="miembro flex items-center gap-2">
                            <div
                                class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center bg-slate-500 shadowM">
                                <span class="text-[12px] font-bold">
                                    NU
                                </span>
                            </div>
                            <div class="logo">
                                <h1 class="font-semibold text-[13px]">Nombre usuario</h1>
                            </div>
                        </div>
                        <div class="estado bg-semaforo-rojo w-2 h-2 rounded-full mx-2"></div>
                    </div>

                    <div class="miembro-estado flex items-center justify-between">
                        <div class="miembro flex items-center gap-2">
                            <div
                                class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center bg-slate-500 shadowM">
                                <span class="text-[12px] font-bold">
                                    NU
                                </span>
                            </div>
                            <div class="logo">
                                <h1 class="font-semibold text-[13px]">Nombre usuario</h1>
                            </div>
                        </div>
                        <div class="estado bg-semaforo-verde w-2 h-2 rounded-full mx-2"></div>
                    </div>

                    <div class="nuevoMiembro cursor-pointer flex items-center gap-2">
                            <div
                                class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center border border-universal-azul border-dashed">
                                <span class="material-symbols-rounded text-universal-azul text-[12px] font-bold">
                                    add
                                </span>
                            </div>
                            <div class="logo">
                                <h1 class="font-semibold text-[13px] text-universal-azul">Agregar miembro</h1>
                            </div>
                        </div>

                </div>
            </div>

            <div class="gestion absolute bottom-0"> 
                <ul class="flex flex-col gap-2">
                    <li :class="[currentRoute === configuracionesRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">settings</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Configuraciones</span>
                            </div>
                            <div :class="[currentRoute === configuracionesRoute ? focus : hover]"></div>
                        </a>
                    </li>
                    <li :class="[currentRoute === ayudaRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.dashboard', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">help</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Centro ayuda</span>
                            </div>
                            <div :class="[currentRoute === ayudaRoute ? focus : hover]"></div>
                        </a>
                    </li>
                </ul>
            </div>

        </aside>
    </div>
</template>
