import { computed , watchEffect} from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useAppColors() {
    const auth = usePage().props.auth;

    const primaryColor = computed(() => {
        return auth.user.establecimiento_asignado.aplicacion_web.estilo.primary || '#f05235';
    });

    const secondaryColor = computed(() => {
        return auth.user.establecimiento_asignado.aplicacion_web.estilo.secondary || '#83B4FF';
    });
    
    watchEffect(() => {
        if (typeof window !== 'undefined') {
            document.documentElement.style.setProperty('--color-primary', primaryColor.value);
            document.documentElement.style.setProperty('--color-secondary', secondaryColor.value);
        }
    });


return {
    primaryColor,
    secondaryColor,
};
}

