<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Colors from '@/Composables/ModularColores';

const { NombreApp, bgClase, textoClase, focus, buttonLink, hover } = Colors();

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

const currentPage = ref(1);
const perPage = 4;

const totalPages = computed(() => {
    return Math.ceil(props.usuariosRol4.length / perPage);
});

const paginatedUsuarios = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return props.usuariosRol4.slice(start, start + perPage);
});

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const page = usePage();

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
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

const obtenerIniciales = (usuario) => {
    const nombres = usuario.nombres_ct || '';
    const apellidos = usuario.apellidos_ct || '';

    const firstNameInitial = nombres.split(' ')[0]?.charAt(0).toUpperCase() || '';
    const lastNameInitial = apellidos.split(' ')[0]?.charAt(0).toUpperCase() || '';

    return firstNameInitial + lastNameInitial;
};

</script>


<template>
    <div class="w-[20%] min-h-[100vh] bg-secundary-opacity p-3">
        <aside class="relative h-full">
            <div class="logo">
                <div class="infoTienda flex items-center gap-2">
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
            </div>

            <div class="tienda rounded-md border border-universal-azul_secundaria p-1 mt-3 flex items-center gap-2">
                <div class="user h-[20px] w-[20px] rounded-full overflow-hidden flex items-center justify-center" :class="[bgClase]">
                </div>
                <div class="nombreApp">
                    <h2 class="text-[14px]">{{ aplicacion }}</h2>
                    <p class="text-[12px] -mt-[3px]">{{ diasRestantes }} días restantes</p>
                </div>

            </div>

            <div class="navegacion mt-5">
                <ul class="flex flex-col gap-1">
                    <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                        <li :class="[currentRoute === dashboardRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                            class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                            <span class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center text-secundary-light">
                                        <span class="text-[20px] material-symbols-rounded">space_dashboard</span>
                                    </div>
                                    <span class="text-[15px] text-secundary-light">Dashboard</span>
                                </div>
                                <div :class="[currentRoute === dashboardRoute ? focus : hover]"></div>
                            </span>
                        </li>
                    </a>


                    <a :href="route('aplicacion.dashboard', { aplicacion, rol })">
                        <li :class="[currentRoute === aplicacionesRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                            class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                            <span class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center text-secundary-light">
                                        <span class="text-[20px] material-symbols-rounded">apps</span>
                                    </div>
                                    <span class="text-[15px] text-secundary-light">Aplicaciones</span>
                                </div>
                                <div :class="[currentRoute === aplicacionesRoute ? focus : hover]"></div>
                                <div
                                    class="h-[20px] w-[30px] rounded-full bg-universal-azul_opacity grid place-content-center text-[12px]">
                                    <span>{{ cantidadApps }}</span>
                                </div>
                            </span>
                        </li>
                    </a>


                    <li :class="[currentRoute === clientesFixRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.clientesFix', { aplicacion, rol })">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-secundary-light">
                                    <span class="text-[20px] material-symbols-rounded">people</span>
                                </div>
                                <span class="text-[15px] text-secundary-light">Clientes Fix</span>
                            </div>
                            <div :class="[currentRoute === clientesFixRoute ? focus : hover]"></div>
                            <div
                                class="h-[20px] w-[30px] rounded-full bg-universal-azul_opacity grid place-content-center text-[12px]">
                                <span>{{ cantidadClientesRol1 }}</span>
                            </div>
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
                                <span>0</span>
                            </div>
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
                                <span>Nuevo</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="miembros mt-2">
                <div class="navegadorMiembros flex justify-between items-center my-2">
                    <h2>Miembros</h2>
                    <div class="botones flex items-center gap-2">
                        <div @click="prevPage"
                            class="left grid place-content-center rounded-full w-7 h-7 bg-secundary-opacity cursor-pointer">
                            <span class="material-symbols-rounded">chevron_left</span>
                        </div>
                        <div @click="nextPage"
                            class="left grid place-content-center rounded-full w-7 h-7 bg-secundary-opacity cursor-pointer">
                            <span class="material-symbols-rounded">chevron_right</span>
                        </div>
                    </div>
                </div>

                <div class="listaMiembros flex flex-col gap-2">
                    <div v-for="usuario in paginatedUsuarios" :key="usuario.id"
                        class="miembro-estado flex items-center justify-between">
                        <div class="miembro-estado flex items-center justify-between">
                            <div class="miembro flex items-center gap-2">
                                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center"
                                    :class="bgClase">
                                    <span class="text-[12px] font-bold">
                                        {{ obtenerIniciales(usuario) }}
                                    </span>

                                </div>
                                <div class="logo">
                                    <h1 class="font-semibold text-[13px]">{{ usuario.nombres_ct }}</h1>
                                    <p class="text-[11px] font-semibold -mt-[5px]">{{ usuario.apellidos_ct }}</p>
                                </div>
                            </div>

                        </div>
                        <div class="estado bg-semaforo-verde w-2 h-2 rounded-full mx-2"></div>
                    </div>

                    <!-- Botón Agregar -->
                    <div class="nuevoMiembro cursor-pointer flex items-center gap-2">
                        <div
                            class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center border border-universal-azul border-dashed">
                            <span class="material-symbols-rounded text-universal-azul text-[12px] font-bold">add</span>
                        </div>
                        <div class="logo">
                            <h1 class="font-semibold text-[13px] text-universal-azul">Agregar miembro</h1>
                        </div>
                    </div>
                </div>
            </div>


            <div class="gestion absolute bottom-0 w-full">
                <ul class="flex flex-col gap-2">
                    <li :class="[currentRoute === configuracionesRoute ? 'bg-secundary-opacity' : 'bg-transparent']"
                        class="hover:bg-secundary-opacity p-2 rounded-lg cursor-pointer">
                        <a class="flex items-center justify-between"
                            :href="route('aplicacion.configuraciones', { aplicacion, rol })">
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
