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

const currentRoute = computed(() => new URL(page.url, window.location.origin).pathname);

const dia = ref('');
const mes = ref('');
const anio = ref('');
const hora = ref('');
const saludo = ref('');

function actualizarFechaHora() {
    const fecha = new Date();
    dia.value = fecha.getDate();

    const monthNamesClock = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    mes.value = monthNamesClock[fecha.getMonth()];
    anio.value = fecha.getFullYear();

    let horas = fecha.getHours();
    const minutos = fecha.getMinutes().toString().padStart(2, "0");
    const segundos = fecha.getSeconds().toString().padStart(2, "0");
    const periodo = horas >= 12 ? "pm" : "am";

    if (horas > 12) {
        horas -= 12;
    } else if (horas === 0) {
        horas = 12;
    }
    hora.value = `${horas}:${minutos}:${segundos} ${periodo}`;
}

let clockInterval = null;
onMounted(() => {
    actualizarFechaHora();
    clockInterval = setInterval(actualizarFechaHora, 1000);
});
onUnmounted(() => {
    clearInterval(clockInterval);
});


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
    <header class="header px-4 py-2 flex items-center justify-between gap-3">
        <div class="left w-[20%] rounded-md">
            <div class="infoTienda flex items-center gap-2">
                <div class="user h-[30px] w-[30px] rounded-full overflow-hidden flex items-center justify-center"
                    :class="[bgClase]">
                    <span class="text-md font-bold">
                        {{ inicialesNombreTienda }}
                    </span>
                </div>
                <div class="logo">
                    <div class="flex items-center" v-if="auth && auth.user">
                        <h3 class="font-semibold">{{ auth.user.tienda?.nombre_tienda || 'Sin tienda' }} App</h3>
                        <span class="material-symbols-rounded text-[17px]">keyboard_arrow_down</span>
                    </div>
                    <div v-else>
                        <p>Cargando información del usuario...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="date w-[23%] flex gap-3 items-center">
                <div class="dia flex items-center justify-center font-semibold h-10 w-7 rounded-full" :class="[bgClase]"
                    id="dia">
                    {{ dia }}
                </div>
                <div class="mes-año flex gap-1 text-[14px] font-medium" id="mes-año">
                    <span id="mes">{{ mes }}</span>
                    <span id="anio">{{ anio }}</span>
                </div>
                <div :class="[bgClase]" class="separador h-8 w-[2px] rounded-lg"></div>
                <div class="hora text-[14px]" id="hora">{{ hora }}</div>
            </div>
        <div class="flex gap-3 items-center">
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
                   
                </div>
            </div>
    </header>
</template>
