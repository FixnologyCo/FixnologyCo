<script setup>
import { ref, computed } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Sidebar from '@/Components/Sidebar/FixnologyCO/Sidebar.vue';
import Header from '@/Components/header/Header.vue';;
import Colors from '@/Composables/ModularColores';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';
import { formatFecha } from '@/Utils/date';
import { useTiempo } from '@/Composables/useTiempo';

const page = usePage();
const user = ref(page.props.auth.user)
const { tiempoActivo, diasRestantes } = useTiempo(user)
const { appName, bgClase, bgOpacity, focus, textoClase, borderClase, buttonFocus, hover } = Colors();


const props = defineProps({
    auth: {
        type: Object,
    },
    foto_base64: String,
    auditorias: {
        type: Array,
        required: true
    }
})

const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol

// Mapeo de acciones a iconos y colores
const iconos = {
    create: { icon: 'add_circle', color: 'bg-green-200 text-green-800' },
    Modificado: { icon: 'edit', color: 'bg-gradient-to-r from-green-200 to-green-400 text-green-900' },
    Eliminado: { icon: 'delete', color: 'bg-gradient-to-r from-red-200 to-red-400 text-red-900' },
    Iniciado_sesión: { icon: 'Login', color: 'bg-gradient-to-r from-blue-200 to-blue-400 text-blue-900' },
    Cerrado_sesión: { icon: 'Logout', color: 'bg-gradient-to-r from-orange-200 to-orange-400 text-orange-900 ' },
    navegacion: { icon: 'visibility', color: 'bg-gray-200 text-gray-800' },
    default: { icon: 'info', color: 'bg-slate-200 text-slate-800' }
}

</script>

<template>
    <div>

        <Head title="Historial" />
        <MensajesLayout />


        <div class="flex">
            <Sidebar :auth="auth" />

            <main class="w-full h-[100%]">
                <Header :auth="auth" :foto_base64="foto_base64" />

                <div class="contenido p-3">
                    <!-- navegable -->
                    <div class="options flex gap-1 items-center text-[14px]">
                        <a :href="route('aplicacion.configuraciones', { aplicacion, rol })"
                            class="text-mono-negro dark:text-mono-blanco dark:hover:text-universal-azul" :class="hover">
                            <p>Configuraciones</p>
                        </a>
                        <span
                            class="material-symbols-rounded text-[18px] text-mono-negro dark:text-mono-blanco">chevron_right</span>
                        <p class="font-bold text-mono-negro dark:text-mono-blanco">Historial de movimientos</p>
                    </div>

                    <div class="my-7 flex justify-between items-center">
                        <div class="encabezado flex gap-2 items-center bg-transparent">
                            <div class=" h-[20px] w-[20px] rounded-full" :class="[bgClase]">
                            </div>
                            <p class="text-mono-negro dark:text-mono-blanco">Historial de movimientos:</p>
                        </div>

                    </div>

                    <div
                        class="cardInfo my-4 flex flex-col gap-2 h-[70.5vh] max-h-[80vh] overflow-y-auto scrollbar-custom">
                        <transition-group name="fade" tag="div">
                            <div v-for="a in auditorias" :key="a.id"
                                class="anuncio mr-2 flex justify-between gap-3 items-center my-1 rounded-lg p-3">
                                <div class="iconoIfo flex items-center gap-3">
                                    <span
                                        class="material-symbols-rounded p-3 rounded-lg shadow transition-transform duration-300 hover:scale-110"
                                        :class="iconos[a.accion_normalizada]?.color || iconos.default.color">
                                        {{ iconos[a.accion_normalizada]?.icon || iconos.default.icon }}
                                    </span>

                                    <div class="info w-full text-mono-negro dark:text-mono-blanco">
                                        <p>
                                            El <template v-if="a.modelo_id"> usuario: <strong>{{ a.modelo_id
                                            }}</strong></template>
                                            se ha <strong>{{ a.accion }}</strong>

                                        </p>
                                        <div class="creacion-id gap-4 flex justify-between text-[12px] ">
                                            <p>Acción realizada por: <span>{{ a.usuario }}</span>.</p>
                                            <p>ID movimiento: <span>{{ a.id }}</span>.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-mono-negro dark:text-mono-blanco fecha-categoria flex flex-col items-end gap-2 text-[12px]">
                                    <p>{{ formatFecha(a.fecha) }}</p>
                                    <p class="p-2 rounded"
                                        :class="iconos[a.accion_normalizada]?.color || iconos.default.color">
                                        {{ a.comentario }}
                                    </p>
                                </div>
                            </div>
                        </transition-group>
                    </div>
                </div>
            </main>


        </div>
    </div>
</template>
