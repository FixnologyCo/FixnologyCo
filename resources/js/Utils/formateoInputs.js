// ✅ Límites de caracteres para cada campo
export const limitesCaracteres = {
  // inputs usuarios
  ruta_foto: 60,
  primer_nombre: 25,
  segundo_nombre: 25,
  primer_apellido: 25,
  segundo_apellido: 25,
  id_tipo_documento: 2,
  numero_documento: 20,
  indicativo: 4,
  telefono: 10,
  email: 60,
  genero: 25,
  direccion: 60,
  password: 30,
  password_confirm: 30,
  nombre_tienda: 20,
  tipo_tienda:60,

  // inputs app
  descripcion_app: 100,
  nombre_app: 30,
  
}

// ✅ Función para capitalizar cada palabra
export const capitalizeWords = (str) => {
  return str
    .split(' ')
    .filter(word => word)
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ')
}

// ✅ Función para capitalizar al salir del campo
export const handleBlur = (form, field) => {
  if (typeof form[field] === 'string') {
    form[field] = capitalizeWords(form[field].trim())
  }
}

// ✅ Función para limitar caracteres y actualizar el valor
export const handleInput = (event, form, field) => {
  const maxCaracteres = limitesCaracteres[field] || 25
  form[field] = event.target.value.slice(0, maxCaracteres)
}
