// composables/useTema.js

import { ref, onMounted } from 'vue';

export function useTema() {
  const modoOscuro = ref(false);
  const animando = ref(false); // Para la animación del botón

  const detectarPreferencia = () => {
    const preferencia = localStorage.getItem("modoOscuro");
    modoOscuro.value =
      preferencia !== null
        ? preferencia === "true"
        : window.matchMedia("(prefers-color-scheme: dark)").matches;
    document.documentElement.classList.toggle("dark", modoOscuro.value);
  };

const cambiarTema = () => {
    modoOscuro.value = !modoOscuro.value;
    document.documentElement.classList.toggle("dark", modoOscuro.value);
    localStorage.setItem("modoOscuro", modoOscuro.value);
  };

  const animarCambioTema = (event) => {
    if (animando.value || !document.startViewTransition) {
        if (!animando.value) cambiarTema();
        return;
    }

    const x = event.clientX;
    const y = event.clientY;
    const endRadius = Math.hypot(
      Math.max(x, window.innerWidth - x),
      Math.max(y, window.innerHeight - y)
    );

    const transition = document.startViewTransition(() => {
        cambiarTema();
    });

    transition.ready.then(() => {
      // ✅ PASO 1: Determina el color de la sombra según el tema al que cambias.
      // Un resplandor claro para el modo oscuro, una sombra oscura para el modo claro.
      const shadowColor = modoOscuro.value ? 'rgba(255, 255, 255, 0.2)' : 'rgba(0, 0, 0, 0.4)';
      
      // ✅ PASO 2: Expande la llamada a .animate() para incluir la propiedad 'filter'.
      document.documentElement.animate(
        [
          // Estado inicial (círculo de 0px sin sombra)
          {
            clipPath: `circle(0px at ${x}px ${y}px)`,
            filter: `drop-shadow(0 0 5px ${shadowColor})`,
          },
          // Estado final (círculo gigante con una sombra más grande)
          {
            clipPath: `circle(${endRadius}px at ${x}px ${y}px)`,
            filter: `drop-shadow(0 0 30px ${shadowColor})`,
          }
        ],
        {
          duration: 600, // Aumenté un poco la duración para apreciar mejor el efecto
          easing: "ease-in-out",
          pseudoElement: "::view-transition-new(root)",
        }
      );
    });

    // Control para la animación del icono (sin cambios)
    animando.value = true;
    setTimeout(() => {
      animando.value = false;
    }, 700);
  };

  onMounted(detectarPreferencia);

  return {
    modoOscuro,
    animando,
    animarCambioTema,
  };
}