// Composables/useTiempo.js
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/es';

dayjs.extend(relativeTime);
dayjs.locale('es');

export function useTiempo(userSource) {
    const tiempoActivo = ref(null);
    const diasRestantes = ref(null);
    let intervalId = null;

    const calcularTiempoActivo = (fechaCreacion) => {
        if (!fechaCreacion) return 'N/A';
        return dayjs(fechaCreacion).fromNow(true); // 'true' para quitar "hace"
    };

    const calcularDiasRestantes = (factura) => {
        if (!factura || !factura.fecha_pago) return 'N/A';
        const fechaPago = dayjs(factura.fecha_pago);
        const diasMembresia = factura.dias_restantes || 0;
        const fechaExpiracion = fechaPago.add(diasMembresia, 'day');
        const dias = fechaExpiracion.diff(dayjs(), 'day');
        return dias > 0 ? dias : 0;
    };

    const actualizarValores = () => {
        const user = userSource.value; // Leemos el valor actual del usuario
        if (user) {
            tiempoActivo.value = calcularTiempoActivo(user.perfil_usuario?.created_at);
            const factura = user.establecimientos?.[0]?.facturas?.[0];
            diasRestantes.value = calcularDiasRestantes(factura);
        } else {
            tiempoActivo.value = null;
            diasRestantes.value = null;
        }
    };

    // Usamos 'watch' para reaccionar cuando el usuario cambie
    watch(userSource, actualizarValores, { immediate: true });

    onMounted(() => {
        intervalId = setInterval(actualizarValores, 60000); // Actualiza cada minuto
    });

    onUnmounted(() => {
        if (intervalId) {
            clearInterval(intervalId);
        }
    });

    return { tiempoActivo, diasRestantes };
}