// resources/js/composables/useTema.js
import { ref, watchEffect } from 'vue'

const modoOscuro = ref(false)
const sistemaOscuro = window.matchMedia('(prefers-color-scheme: dark)')

const aplicarTema = (esOscuro) => {
  const html = document.documentElement
  html.classList.add('transicion-tema')

  html.classList.toggle('dark', esOscuro)

  setTimeout(() => {
    html.classList.remove('transicion-tema')
  }, 500)
}

const detectarTemaInicial = () => {
  const preferencia = localStorage.getItem('modoOscuro')

  if (preferencia === null) {
    // Sin preferencia: usar el sistema
    modoOscuro.value = sistemaOscuro.matches
  } else {
    // Preferencia manual
    modoOscuro.value = preferencia === 'true'
  }

  aplicarTema(modoOscuro.value)
}

const toggleTema = () => {
  modoOscuro.value = !modoOscuro.value
  localStorage.setItem('modoOscuro', modoOscuro.value)
  aplicarTema(modoOscuro.value)
}

const escucharCambiosSistema = () => {
  sistemaOscuro.addEventListener('change', (e) => {
    const preferencia = localStorage.getItem('modoOscuro')

    // Solo reaccionar si no hay preferencia manual
    if (preferencia === null) {
      modoOscuro.value = e.matches
      aplicarTema(modoOscuro.value)
    }
  })
}

// Reacciona a cambios manuales en tiempo real
watchEffect(() => {
  aplicarTema(modoOscuro.value)
})

export function useTema() {
  return {
    modoOscuro,
    toggleTema,
    detectarTemaInicial,
    escucharCambiosSistema
  }
}
