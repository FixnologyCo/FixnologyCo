<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import 'dayjs/locale/es';

const props = defineProps({
  auth: {
    type: Object,
    required: true
  },
  clientes: {
    type: Array,
    default: () => [],
  },
  aplicacion: {
    type: String,
    default: ''
  },
  errors: {
    type: Object,
    required: true
  },
})


const user = props.auth.user
const auth = usePage().props.auth;
const clientes = ref(props.clientes);

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

const appName = computed(() => props.auth?.user?.tienda?.aplicacion?.nombre_app || 'default');
const bgClase = computed(() => coloresBg[appName.value]);
const buttonFocus = computed(() => button[appName.value]);
const textoClase = computed(() => coloresTexto[appName.value]);
</script>

<template>
            <slot />
</template>