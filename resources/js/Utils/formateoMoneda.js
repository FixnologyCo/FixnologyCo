export const formatCOP = (value) => {
  if (value === null || value === undefined || isNaN(value)) {
    return 'Sin precio';
  }

  return parseFloat(value).toLocaleString('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });
}
