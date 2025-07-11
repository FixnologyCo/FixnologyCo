// ✅ Límites de caracteres para cada campo
export const limitesCaracteres = {
  descripcion_app: 100,
  nombre_app: 30,
  primer_nombre: 25,
  primer_apellido: 25,
  numero_identificacion: 20,
  password: 30,
  password_confirm: 30

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
