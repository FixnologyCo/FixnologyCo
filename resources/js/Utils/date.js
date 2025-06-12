// resources/js/utils/date.js
import dayjs from 'dayjs'
import 'dayjs/locale/es'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)
dayjs.locale('es')

export const formatFecha = (fecha) => {
  if (!fecha || !dayjs(fecha).isValid()) return 'Sin fecha'
  return dayjs(fecha).format('dddd D [de] MMMM [de] YYYY [a las] h:mm a')
}

export const formatFechaShort = (fecha) => {
  if (!fecha || !dayjs(fecha).isValid()) return 'Sin fecha'
  return dayjs(fecha).format('MMMM D, YYYY | h:mm A')
}

export const fromNow = (fecha) => {
  if (!fecha || !dayjs(fecha).isValid()) return 'Sin fecha'
  return dayjs(fecha).fromNow()
}

export const calcularRestantes = (fechaActivacion, duracion) => {
  if (!fechaActivacion || !duracion) return 0
  const activacion = dayjs(fechaActivacion)
  const hoy = dayjs()
  const transcurridos = hoy.diff(activacion, 'day')
  const restantes = duracion - transcurridos
  return restantes > 0 ? restantes : 0
}
