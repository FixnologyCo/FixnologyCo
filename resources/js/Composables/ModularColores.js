import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useAppColors() {
    const auth = usePage().props.auth;

    const coloresBg = {
        'FixnologyCO': 'bg-universal-naranja shadow-universal-naranja',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const bgFocus = {
        'FixnologyCO': 'bg-universal-naranja shadow-universal-naranja w-[15px] h-[10px] mx-3 rounded-full',
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
        'FixnologyCO': 'bg-universal-naranja shadow-universal-naranja rounded-full py-[5px] px-[5px] flex items-center justify-center gap-2',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const hoverClass = computed(() => {
    switch (appName.value) {
        case 'FixnologyCO':
            return 'hover:bg-universal-naranja';
        case 'Essentials':
            return 'hover:bg-essentials-primary';
        case 'Machine':
            return 'hover:bg-machine-primary';
        case 'Shopper':
            return 'hover:bg-shopper-primary';
        case 'Smart':
            return 'hover:bg-smart-primary hover:text-mono-negro';
        default:
            return 'hover:bg-gray-300';
    }
});

const appName = computed(() =>
    auth?.user?.tienda?.aplicacion?.nombre_app || 'default'
);

const bgClase = computed(() => coloresBg[appName.value] || coloresBg['default']);
const textoClase = computed(() => coloresTexto[appName.value] || coloresTexto['default']);
const focus = computed(() => bgFocus[appName.value] || bgFocus['default']);
const buttonLink = computed(() => btn_link[appName.value] || btn_link['default']);
const hover = computed(() => hoverClass[appName.value] || hoverClass['default'])

return {
    appName,
    bgClase,
    textoClase,
    focus,
    buttonLink,
    hover
};
}

