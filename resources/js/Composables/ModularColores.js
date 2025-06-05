import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useAppColors() {
    const auth = usePage().props.auth;

    const coloresBg = {
        'FixnologyCO': 'bg-universal-naranja text-mono-blanco shadow-universal-naranja',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const bgFocus = {
        'FixnologyCO': 'bg-universal-naranja text-mono-blanco shadow-universal-naranja w-[15px] h-[10px] mx-3 rounded-full',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const bgFocusOpacity = {
        'FixnologyCO': 'bg-universal-naranja_opacity rounded-[10px]',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const coloresTexto = {
        'FixnologyCO': 'text-universal-naranja',
        'default': 'text-gray-500'
    };

    const button = {
        'FixnologyCO': 'bg-universal-naranja shadow-universal-naranja rounded-[8px] py-[5px] px-[15px] flex items-center justify-center gap-2',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const btn_link = {
        'FixnologyCO': 'bg-universal-naranja text-mono-blanco shadow-universal-naranja rounded-full py-[5px] px-[5px] flex items-center justify-center gap-2',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const border = {
        'FixnologyCO': 'border-b-2 border-universal-naranja',
        'default': 'border-gray-300'
    };

    const hoverClass = {
        'FixnologyCO': 'hover:text-universal-naranja',
        'Essentials': 'hover:bg-essentials-primary',
        'Machine': 'hover:bg-machine-primary',
        'Shopper': 'hover:bg-shopper-primary',
        'Smart': 'hover:bg-smart-primary hover:text-mono-negro',
    }


const appName = computed(() =>
    auth?.user?.tienda?.aplicacion?.nombre_app || 'default'
);

const bgClase = computed(() => coloresBg[appName.value] || coloresBg['default']);
const bgOpacity = computed(() => bgFocusOpacity[appName.value] || bgFocusOpacity['default']);
const textoClase = computed(() => coloresTexto[appName.value] || coloresTexto['default']);
const focus = computed(() => bgFocus[appName.value] || bgFocus['default']);
const buttonLink = computed(() => btn_link[appName.value] || btn_link['default']);
const hover = computed(() => hoverClass[appName.value] || hoverClass['default'])
const borderClase = computed(() => border[appName.value] || border['default'])

return {
    appName,
    bgClase,
    bgOpacity,
    textoClase,
    focus,
    buttonLink,
    hover,
    borderClase
};
}

