// resources/js/composables/useTiempo.js
import { ref, onMounted, onUnmounted } from 'vue'
import dayjs from 'dayjs'
import { fromNow, calcularRestantes } from '@/Utils/date'

export function useTiempo(detallesCliente) {
  const tiempoActivo = ref('')
  const diasRestantes = ref(0)

  let intervalo = null
  let intervaloRestante = null

  const calcularTiempo = () => {
    if (!detallesCliente.value?.fecha_creacion || !dayjs(detallesCliente.value.fecha_creacion).isValid()) {
      tiempoActivo.value = 'Sin fecha'
      return
    }

    tiempoActivo.value = fromNow(detallesCliente.value.fecha_creacion)
  }

  const calcularDiasRestantes = () => {
    const fechaActivacion = detallesCliente.value?.tienda?.pagos_membresia?.fecha_activacion
    const duracion = detallesCliente.value?.tienda?.aplicacion?.membresia?.duracion
    diasRestantes.value = calcularRestantes(fechaActivacion, duracion)
  }

  onMounted(() => {
    calcularTiempo()
    calcularDiasRestantes()

    intervalo = setInterval(calcularTiempo, 60000)
    intervaloRestante = setInterval(calcularDiasRestantes, 8640000) // 24 horas en milisegundos
  })

  onUnmounted(() => {
    clearInterval(intervalo)
    clearInterval(intervaloRestante)
  })

  return { tiempoActivo, diasRestantes }
}
