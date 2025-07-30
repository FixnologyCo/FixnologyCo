// resources/js/composables/useTiempo.js
import { ref, onMounted, onUnmounted } from 'vue';
import dayjs from 'dayjs';
import { fromNow, calcularRestantes } from '@/Utils/date';
import { useAuthStore } from "@/stores/auth";

// No llames a la tienda aquí arriba

export function useTiempo() { // El parámetro 'user' no es necesario si usas el authStore
  // ¡CORRECTO! Llama a la tienda aquí dentro.
  const authStore = useAuthStore(); 

  const tiempoActivo = ref('');
  const diasRestantes = ref(0);

  let intervalo = null;
  let intervaloRestante = null;

  const calcularTiempo = () => {
    // Verifica si la fecha de creación existe en la tienda
    if (!authStore.establecimientoAsignado.created_at || !dayjs(authStore.establecimientoAsignado.created_at).isValid()) {
      tiempoActivo.value = 'Fecha no disponible';
      return;
    }
    
    tiempoActivo.value = fromNow(authStore.establecimientoAsignado.created_at);
  };

  const calcularDiasRestantes = () => {
    const fechaActivacion = authStore.user.establecimientos.facturas[0]?.fecha_pago;

    
    const duracion = authStore.duracionMembresia;
    diasRestantes.value = calcularRestantes(fechaActivacion, duracion);
  };

  onMounted(() => {
    // Llama a las funciones para establecer los valores iniciales
    calcularTiempo();
    calcularDiasRestantes();

    // Configura los intervalos para que se actualicen periódicamente
    intervalo = setInterval(calcularTiempo, 60000); // Cada minuto
    intervaloRestante = setInterval(calcularDiasRestantes, 3600000); // Cada hora
  });

  onUnmounted(() => {
    // Limpia los intervalos cuando el componente se destruye
    clearInterval(intervalo);
    clearInterval(intervaloRestante);
  });

  return { tiempoActivo, diasRestantes };
}