import { computed , watchEffect} from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useAppColors() {
    const auth = usePage().props.auth;

    const primaryColor = computed(() => {
        return auth.user.establecimiento_asignado.aplicacion_web.estilo.primary || '#3652AD';
    });
    
    watchEffect(() => {
        if (typeof window !== 'undefined') {
            document.documentElement.style.setProperty('--color-primary', primaryColor.value);
        }
    });


return {
    primaryColor,
};
}

