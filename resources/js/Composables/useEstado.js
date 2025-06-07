export default function useEstadoClass() {
  const getEstadoClass = (estado) => {
    if (estado === 'Inactivo' || estado === 'Pendiente') return 'bg-semaforo-rojo shadow-rojo';
    if (estado === 'Suspendido') return 'bg-semaforo-amarillo shadow-amarillo';
    if (estado === 'Activo' || estado === 'Pagada' || estado === 'Estable') return 'bg-semaforo-verde shadow-verde';
    return '';
  };

  return { getEstadoClass };
}
