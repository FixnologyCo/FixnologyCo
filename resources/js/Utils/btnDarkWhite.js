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
  animando.value = true;
  toggleTema();
  setTimeout(() => {
    animando.value = false;
  }, 600);
};

onMounted(() => {
  detectarPreferencia();
});