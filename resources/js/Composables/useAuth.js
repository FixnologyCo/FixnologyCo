import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';


export default function useAuth() {
    
    const page = usePage();

    const user = computed(() => page.props.auth.user);
    
    
    const perfilUsuario = computed(() => user.value?.perfil_usuario);

    const primerNombre = computed(() => perfilUsuario.value?.primer_nombre);

    const nombreCompleto = computed(() => {
        if (!perfilUsuario.value) return 'Usuario';
        return `${perfilUsuario.value.primer_nombre} ${perfilUsuario.value.primer_apellido}`.trim();
    });
    
    const rol = computed(() => perfilUsuario.value?.rol?.tipo_rol);
    
    const rutaFoto = computed(() => perfilUsuario.value?.ruta_foto);

    return {
        user,
        perfilUsuario,
        primerNombre,
        nombreCompleto,
        rol,
        rutaFoto,
    };
}