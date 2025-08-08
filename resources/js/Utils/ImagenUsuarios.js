export const getFotoUrlCompleta = (usuario) => {
  if (!usuario || !usuario.perfil_usuario) {
    return null;
  }
  const ruta = usuario.perfil_usuario.ruta_foto;
  if (!ruta) {
    return null;
  }
  return ruta.startsWith("http") ? ruta : `/storage/${ruta}`;
}

export const getFotoEstablecimientoUrlCompleta = (usuario) => {
  if (!usuario || !usuario.establecimiento_asignado.ruta_foto_establecimiento) {
    return null;
  }
  const ruta = usuario.establecimiento_asignado.ruta_foto_establecimiento;
  if (!ruta) {
    return null;
  }
  return ruta.startsWith("http") ? ruta : `/storage/${ruta}`;
}