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
        'FixnologyCO': 'bg-universal-naranja shadow-universal-naranja rounded-md py-2 px-4 flex items-center justify-center gap-2 text-mono-blanco hover:-translate-y-2 hover:bg-orange-700 hover:shadow-orange-700',
        'default': 'bg-gray-300 shadow-gray-300'
    };

    const buttonSecundary = {
        'FixnologyCO': 'rounded-md py-2 px-4 flex items-center justify-center gap-2 text-mono-negro dark:text-mono-blanco  border border-secundary-light dark:hover:text-mono-negro hover:-translate-y-2 hover:bg-secundary-light',
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

    const hoverText = {
        'FixnologyCO': 'hover:text-universal-naranja dark:hover:text-universal-naranja',
    }

    const hoverBg = {
        'FixnologyCO': 'hover:bg-universal-naranja hover:text-mono-blanco hover:shadow-universal-naranja',
    }

const appName = computed(() => {
    return auth?.user?.tienda[0]?.aplicacion_web?.nombre_app || 'default'
    
});

const colorFondo = computed(() => {
  return auth?.user?.tienda?.aplicacion?.color_fondo || '#CCCCCC'; // color por defecto si no hay
});


const bgClase = computed(() => coloresBg[appName.value] || coloresBg['default']);
const bgOpacity = computed(() => bgFocusOpacity[appName.value] || bgFocusOpacity['default']);
const textoClase = computed(() => coloresTexto[appName.value] || coloresTexto['default']);
const focus = computed(() => bgFocus[appName.value] || bgFocus['default']);
const buttonLink = computed(() => btn_link[appName.value] || btn_link['default']);
const hoverTexto = computed(() => hoverText[appName.value] || hoverTexto['default'])
const hoverClase = computed(() => hoverBg[appName.value] || hoverBg['default'])
const borderClase = computed(() => border[appName.value] || border['default'])
const buttonClase = computed(() => button[appName.value] || button['default']);
const buttonSecundario = computed(() => buttonSecundary[appName.value] || buttonSecundary['default']);

return {
    appName,
    bgClase,
    bgOpacity,
    textoClase,
    focus,
    buttonLink,
    hoverTexto,
    hoverClase,
    borderClase,
    buttonClase,
    buttonSecundario,
    colorFondo
};
}

