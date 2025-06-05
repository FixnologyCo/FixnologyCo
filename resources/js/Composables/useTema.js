import { ref, watchEffect, onMounted } from 'vue'

const modoOscuro = ref(false)

const aplicarTema = (esOscuro) => {
  const html = document.documentElement

  // Agrega clase para animar suavemente el cambio de tema
  html.classList.add('transicion-tema')

  if (esOscuro) {
    html.classList.add('dark')
  } else {
    html.classList.remove('dark')
  }

  // Quitar la animación después de que se aplica
  setTimeout(() => {
    html.classList.remove('transicion-tema')
  }, 500) // duración en ms (ajusta según tu tailwind)
}

const detectarTemaInicial = () => {
  const preferencia = localStorage.getItem('modoOscuro')

  modoOscuro.value = preferencia !== null
    ? preferencia === 'true'
    : window.matchMedia('(prefers-color-scheme: dark)').matches

  aplicarTema(modoOscuro.value)
}

const toggleTema = () => {
  modoOscuro.value = !modoOscuro.value
  localStorage.setItem('modoOscuro', modoOscuro.value)
  aplicarTema(modoOscuro.value)
}

// Escucha cambios del sistema operativo
const escucharCambiosSistema = () => {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  mediaQuery.addEventListener('change', e => {
    modoOscuro.value = e.matches
    localStorage.setItem('modoOscuro', modoOscuro.value)
    aplicarTema(modoOscuro.value)
  })
}

// Se asegura de aplicar el tema al montar
onMounted(() => {
  detectarTemaInicial()
  escucharCambiosSistema()
})

// Reacciona a cambios manuales en tiempo real
watchEffect(() => {
  aplicarTema(modoOscuro.value)
})

export function useTema() {
  return {
    modoOscuro,
    toggleTema,
  }
}
