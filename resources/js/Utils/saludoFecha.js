import { ref, onMounted, onUnmounted, computed } from "vue";

const nombreDia = ref("");
const dia = ref("");
const mes = ref("");
const anio = ref("");
const hora = ref("");
const saludo = ref("");

 function actualizarFechaHora() {
  const fecha = new Date();
  dia.value = fecha.getDate();

  const nombreDias = [
    "Domingo",
    "Lunes",
    "Martes",
    "Miércoles",
    "Jueves",
    "Viernes",
    "Sábado",
  ];
  const monthNamesClock = [
    "enero",
    "febrero",
    "marzo",
    "abril",
    "mayo",
    "junio",
    "julio",
    "agosto",
    "septiembre",
    "octubre",
    "noviembre",
    "diciembre",
  ];
  mes.value = monthNamesClock[fecha.getMonth()];
  nombreDia.value = nombreDias[fecha.getDay()];
  anio.value = fecha.getFullYear();

  let horas = fecha.getHours();
  const minutos = fecha.getMinutes().toString().padStart(2, "0");
  const segundos = fecha.getSeconds().toString().padStart(2, "0");
  const periodo = horas >= 12 ? "pm" : "am";

  if (horas > 12) {
    horas -= 12;
  } else if (horas === 0) {
    horas = 12;
  }
  hora.value = `${horas}:${minutos}:${segundos} ${periodo}`;
}

let clockInterval = null;
onMounted(() => {
  actualizarFechaHora();
  clockInterval = setInterval(actualizarFechaHora, 1000);
});
onUnmounted(() => {
  clearInterval(clockInterval);
});

let fecha = new Date();
let horas = fecha.getHours();

if (horas > 12) {
  horas -= 12;
} else if (horas === 0) {
  horas = 12;
}

if (fecha.getHours() < 12) {
  saludo.value = "¡Buenos días!";
} else if (fecha.getHours() < 18) {
  saludo.value = "¡Buenas tardes!";
} else {
  saludo.value = "¡Buenas noches!";
}
