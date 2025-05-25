import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useAppColors() {
  const auth = usePage().props.auth;

  const coloresBg = {
    'FixnologyCO': 'bg-universal-naranja shadow-universal-naranja',
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

  const appName = computed(() =>
    auth?.user?.tienda?.aplicacion?.nombre_app || 'default'
  );

  const bgClase = computed(() => coloresBg[appName.value] || coloresBg['default']);
  const textoClase = computed(() => coloresTexto[appName.value] || coloresTexto['default']);
  const buttonFocus = computed(() => button[appName.value] || button['default']);

  return {
    appName,
    bgClase,
    textoClase,
    buttonFocus
  };
}
