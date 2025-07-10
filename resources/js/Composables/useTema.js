// /utils/useTheme.js

import { ref, onMounted } from 'vue';

// La función composable
export function useTema() {
  // El estado y la lógica se encapsulan aquí dentro
  const modoOscuro = ref(false);
  const animando = ref(false);

  const detectarPreferencia = () => {
    const preferencia = localStorage.getItem("modoOscuro");
    modoOscuro.value =
      preferencia !== null
        ? preferencia === "true"
        : window.matchMedia("(prefers-color-scheme: dark)").matches;

    document.documentElement.classList.toggle("dark", modoOscuro.value);
  };

  const toggleTema = () => {
    modoOscuro.value = !modoOscuro.value;
    document.documentElement.classList.toggle("dark", modoOscuro.value);
    localStorage.setItem("modoOscuro", modoOscuro.value);
  };

  const animarCambioTema = () => {
    if (animando.value) return; // Evita doble clic
    animando.value = true;
    toggleTema();
    setTimeout(() => {
      animando.value = false;
    }, 600);
  };

  // El ciclo de vida se conecta al componente que usa el composable
  onMounted(() => {
    detectarPreferencia();
  });

  // Retornamos solo lo que el componente necesita usar
  return {
    modoOscuro,
    animando,
    animarCambioTema,
  };
}