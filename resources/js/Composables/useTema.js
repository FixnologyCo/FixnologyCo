// /utils/useTheme.js

import { ref, onMounted, computed } from 'vue';

// La función composable
export function useTema() {
 
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
    if (animando.value) return; 
    animando.value = true;
    toggleTema();
    setTimeout(() => {
      animando.value = false;
    }, 600);
  };

 
  onMounted(() => {
    detectarPreferencia();
  });


  return {
    modoOscuro,
    animando,
    animarCambioTema,
   
  };
}
