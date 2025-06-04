<script setup>

import { Head, usePage, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { defineProps, computed, ref, onUnmounted, onMounted } from 'vue';
import SidebarSuperAdmin from '@/Components/Sidebar/FixnologyCO/Sidebar.vue';
import Header from '@/Components/header/Header.vue';
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/es' // ✅ Importa el idioma español
import Colors from '@/Composables/ModularColores';
import MensajesLayout from '@/Layouts/MensajesLayout.vue';

const { appName, bgClase, bgOpacity, focus, textoClase, borderClase, buttonFocus, hover } = Colors();

dayjs.locale('es')
dayjs.extend(relativeTime)

const props = defineProps({
  auth: {
    type: Object,
  },
  clientes: {
    type: Array,
    default: () => [],
  },
  aplicacion: {
    type: String,
    default: ''
  },
  foto_base64: String,
  errors: {
    type: Object,
    required: true
  }
})

const user = props.auth.user


const logout = () => {
  router.visit(route('logout'), {
    method: 'post',
    preserveScroll: true,
  });
};

const inicialesNombreUsuario = computed(() => {
  const nombres = props.auth.user?.nombres_ct || '';
  const apellidos = props.auth.user?.apellidos_ct || '';


  const firstNameInitial = nombres.split(' ')[0]?.charAt(0).toUpperCase() || '';
  const lastNameInitial = apellidos.split(' ')[0]?.charAt(0).toUpperCase() || '';

  return firstNameInitial + lastNameInitial;
});



const formatCOP = (value) => {
  if (value === null || value === undefined || isNaN(value)) {
    return 'Sin precio';
  }
  return parseFloat(value).toLocaleString('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });
};

dayjs.locale('es');
const formatFecha = (fecha) => {
  if (!fecha || !dayjs(fecha).isValid()) return 'Sin fecha';
  return dayjs(fecha).format('dddd D [de] MMMM [de] YYYY [a las] h:mm a');
};

const page = usePage();


const tienda = page.props.tienda ?? {}
const membresia = tienda.aplicacion?.membresia ?? {}

const tiempoActivo = ref('')

const calcularTiempo = () => {
  if (!user.fecha_creacion || !dayjs(user.fecha_creacion).isValid()) {
    tiempoActivo.value = 'Sin fecha'
    return
  }

  const fechaCreacion = dayjs(user.fecha_creacion)
  tiempoActivo.value = fechaCreacion.fromNow() // Ej: "hace 2 horas"
}

let intervaloRestante = null
let intervalo = null

onMounted(() => {
  calcularTiempo()

  // Recalcula cada 60 segundos
  intervalo = setInterval(() => {
    calcularTiempo()
  }, 60000)

  calcularDiasRestantes()
  // actualiza cada 24h
  intervaloRestante = setInterval(() => {
    calcularDiasRestantes()
  }, 86400000)
})

onUnmounted(() => {
  clearInterval(intervalo),
    clearInterval(intervaloRestante)
})

const diasRestantes = ref(0)

const calcularDiasRestantes = () => {
  const fechaActivacion = user.tienda?.pagos_membresia?.fecha_activacion
  const duracion = user.tienda?.aplicacion?.membresia?.duracion

  if (!fechaActivacion || !duracion) {
    diasRestantes.value = 0
    return
  }

  const activacion = dayjs(fechaActivacion)
  const hoy = dayjs()
  const diasTranscurridos = hoy.diff(activacion, 'day')
  const restantes = duracion - diasTranscurridos

  diasRestantes.value = restantes > 0 ? restantes : 0
}



const aplicacion = props.auth?.user?.tienda?.aplicacion?.nombre_app || 'Sin app';
const rol = props.auth.user.rol?.tipo_rol || 'Sin rol'; // Obtén el tipo de rol



const activeTab = ref(0)

const tabs = [
  { label: 'Cliente' },
  { label: 'Tienda' },
  { label: 'Ajustes avanzados' }
]

function getEstadoClass(estado) {
  if (estado === 'Inactivo' || estado === 'Pendiente') return 'bg-semaforo-rojo shadow-rojo';
  if (estado === 'Suspendido') return 'bg-semaforo-amarillo shadow-amarillo';
  if (estado === 'Activo' || estado === 'Pagada') return 'bg-semaforo-verde shadow-verde';
  return '';
}


const onFileChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('foto', file)

  router.post(route('usuario.actualizar.foto'), formData, {
    preserveScroll: true,
    preserveState: true,
  })
}

const form = ref({
  nombres_ct: user.nombres_ct || '',
  apellidos_ct: user.apellidos_ct || '',
  email_ct: user.email_ct || '',
  telefono_ct: user.telefono_ct || '',

  nombre_tienda: user.tienda?.nombre_tienda || '',
  email_tienda: user.tienda?.email_tienda || '',
  telefono_tienda: user.tienda?.telefono_tienda || '',
  barrio_tienda: user.tienda?.barrio_tienda || '',
  direccion_tienda: user.tienda?.direccion_tienda || '',
})

const submitForm = () => {
  router.put(
    route('aplicacion.editarMiPerfil.actualizar', {
      aplicacion: appName.value,
      rol: props.auth.user.rol.tipo_rol,
    }),
    form.value
  )
}


// ✅ Límites de caracteres para cada campo
const limitesCaracteres = {
  nombres_ct: 25,
  apellidos_ct: 25,
  email_ct: 60,
  telefono_ct: 10,

  nombre_tienda: 30,
  email_tienda: 60,
  telefono_tienda: 10,
  barrio_tienda: 30,
  direccion_tienda: 50,
}

// ✅ Función para capitalizar cada palabra
const capitalizeWords = (str) => {
  return str
    .split(' ')
    .filter(word => word)
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ')
}

// ✅ Función para capitalizar al salir del campo
const handleBlur = (field) => {
  if (typeof form[field] === 'string') {
    form[field] = capitalizeWords(form[field].trim())
  }
}

// ✅ Función para limitar caracteres y actualizar el valor
const handleInput = (event, field) => {
  const maxCaracteres = limitesCaracteres[field] || 25
  form[field] = event.target.value.slice(0, maxCaracteres)
}
</script>

<template>
  <div>

    <Head title="Editar mi perfil" />
    <MensajesLayout />


    <div class="flex">
      <SidebarSuperAdmin :auth="auth" />

      <div class="w-full px-3">
        <Header :auth="auth" :foto_base64="foto_base64" />


        <div class="contenido max-h-[90vh] w-full overflow-auto scrollbar-custom">
          <div class="banner w-full min-h-[150px] rounded-lg" :class="[bgOpacity]"></div>
          <div class="flex items-end justify-between encabezado-config h-[auto] py-10 mx-12">
            <div class="left-foto -mt-[120px] flex items-end gap-4">
              <div
                class="grid place-content-center foto w-[250px] h-[250px] rounded-[60px] bg-secundary-opacity backdrop-blur-lg">
                <template v-if="foto_base64">
                  <div class="relative w-[220px] h-[220px] group">

                    <img v-if="foto_base64" :src="foto_base64"
                      class="rounded-[50px] w-full h-full object-cover shadow-lg" />


                    <div
                      class="absolute inset-0 bg-black/40 rounded-[50px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">

                      <label for="fotoInput"
                        class="cursor-pointer bg-white p-3 rounded-full shadow-md hover:bg-gray-200 transition"
                        title="Cambiar foto">
                        <span class="material-symbols-rounded text-2xl" :class="[textoClase]">edit</span>
                      </label>
                    </div>

                    <!-- Input oculto -->
                    <input id="fotoInput" type="file" accept="image/*" @change="onFileChange" class="hidden" />
                  </div>
                </template>



                <template v-else>
                  <div class="p-2 w-[220px] h-[220px] rounded-[50px] grid place-content-center" :class="[bgClase]">
                    <p class="text-[45px] font-semibold">{{ inicialesNombreUsuario }}</p>
                  </div>
                </template>
              </div>



              <div class="nombre">
                <h3 class="font-semibold text-[30px]">{{ user.nombres_ct }} {{
                  user.apellidos_ct }}</h3>

                <div class="flex items-center justify-between">
                  <p id="rol-usuario" class="flex items-center gap-1.5 py-1">
                    <span class="material-symbols-rounded text-[20px] text-universal-azul">local_police</span>
                    {{ user.rol?.tipo_rol || 'Sin rol' }}
                  </p>
                  <div class="flex items-center gap-1 shadowM bg-universal-azul w-[auto] py-1 px-2 rounded-md">{{
                    user.tienda?.aplicacion?.membresia?.nombre_membresia }} <span
                      class="material-symbols-rounded text-[18px]">bolt</span></div>
                </div>


                <div class="botonesConfig my-3 flex gap-2 items-center">
                  <button @click="submitForm" class="px-4 py-2 rounded-md" :class="[bgClase, buttonFocus]">
                    Guardar cambios
                  </button>
                  <a :href="route('aplicacion.configuraciones', { aplicacion, rol })">
                    <button
                      class="opcion flex items-center gap-2 justify-center border border-secundary-light cursor-pointer py-2 px-4 rounded-md">
                      <p>Cancelar</p>
                    </button>
                  </a>
                </div>
              </div>

            </div>

            <div class="right-info">

              <div class="datos-recurentes flex items-end flex-col gap-2">
                <div class="dias-restante text-right w-auto rounded-md">
                  <h4 class="">Tu membresía finaliza en:</h4>
                  <p class="text-[35px] font-semibold -mt-3">{{ diasRestantes }}<span class="text-[14px]">Días</span>
                  </p>
                </div>
                <div class="dias-activo w-auto rounded-md">
                  <h4 class="text-right">Te uniste a la familia: </h4>
                  <p class="text-[35px] font-semibold -mt-3">{{ tiempoActivo }} <span class="text-[14px]"></span></p>
                </div>
              </div>
            </div>
          </div>

          <div class="w-full px-12 ">
            <div class="mb-6">
              <nav class="-mb-px flex space-x-4">
                <button v-for="(tab, index) in tabs" :key="index" @click="activeTab = index" :class="[
                  'text-md font-medium px-4 py-2',
                  activeTab === index
                    ? textoClase + ' ' + borderClase
                    : 'text-secundary-light ' + hover
                ]">
                  {{ tab.label }}
                </button>
              </nav>
            </div>

            <div v-if="activeTab === 0">
              <h2 class="text-2xl font-bold mb-4">Editar mis datos personales</h2>
              <div class="flex w-full justify-between gap-10">

                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="nombre-usuario">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="nombre-usuario" class="text-[14px] text-secundary-light">Nombre usuario:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.nombres_ct.length }} / {{ limitesCaracteres.nombres_ct }}
                      </p>
                    </div>

                    <div
                      class=" w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class=" text-[20px] pl-[5px] material-symbols-rounded"
                        :class="[textoClase]">format_italic</span>


                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa tus nombres" v-model="form.nombres_ct"
                        @input="handleInput($event, 'nombres_ct')" @blur="handleBlur('nombres_ct')" />
                    </div>
                  </div>

                  <div class="email-usuario">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="email-usuario" class="text-[14px] text-secundary-light">Email usuario:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.email_ct.length }} / {{ limitesCaracteres.email_ct }}
                      </p>
                    </div>

                    <div
                      class=" w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class=" text-[20px] pl-[5px] material-symbols-rounded" :class="[textoClase]">email</span>


                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa tus nombres" v-model="form.email_ct"
                        @input="handleInput($event, 'email_ct')" @blur="handleBlur('email_ct')" />
                    </div>
                  </div>
                </div>

                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="apellido-usuario">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="apellido-usuario" class="text-[14px] text-secundary-light">Apellidos
                        usuario:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.apellidos_ct.length }} / {{ limitesCaracteres.apellidos_ct }}
                      </p>
                    </div>

                    <div
                      class="w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class="text-[20px] pl-[5px] material-symbols-rounded"
                        :class="[textoClase]">format_italic</span>
                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa tus nombres" v-model="form.apellidos_ct"
                        @input="handleInput($event, 'apellidos_ct')" @blur="handleBlur('apellidos_ct')" />
                    </div>
                  </div>

                  <div class="telefono-usuario">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="telefono-usuario" class="text-[14px] text-secundary-light">Teléfono usuario:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.telefono_ct.length }} / {{ limitesCaracteres.telefono_ct }}
                      </p>
                    </div>

                    <div
                      class="w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class="text-[20px] pl-[5px] material-symbols-rounded" :class="[textoClase]">phone</span>
                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa tu teléfono" v-model="form.telefono_ct"
                        @input="handleInput($event, 'telefono_ct')" @blur="handleBlur('telefono_ct')" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else-if="activeTab === 1">
              <h2 class="text-2xl font-bold mb-4">Editar datos de mi tienda</h2>
              <div class="flex justify-between w-full gap-10">
                <div class="left-table w-[50%] flex flex-col gap-1">
                  <div class="tienda-usuario">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="tienda-usuario" class="text-[14px] text-secundary-light">Nombre de mi tienda:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.nombre_tienda.length }} / {{ limitesCaracteres.nombre_tienda }}
                      </p>
                    </div>

                    <div
                      class="w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class="text-[20px] pl-[5px] material-symbols-rounded" :class="[textoClase]">store</span>
                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa el nombre de tu empresa" v-model="form.nombre_tienda"
                        @input="handleInput($event, 'nombre_tienda')" @blur="handleBlur('nombre_tienda')" />
                    </div>
                  </div>

                  <div class="direccion-tienda">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="tienda-usuario" class="text-[14px] text-secundary-light">Dirección de la tienda:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.direccion_tienda.length }} / {{ limitesCaracteres.direccion_tienda }}
                      </p>
                    </div>

                    <div
                      class="w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class="text-[20px] pl-[5px] material-symbols-rounded" :class="[textoClase]">pin_drop</span>
                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa la direccion de la tienda" v-model="form.direccion_tienda"
                        @input="handleInput($event, 'direccion_tienda')" @blur="handleBlur('direccion_tienda')" />
                    </div>
                  </div>

                  <div class="barrio-tienda">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="tienda-usuario" class="text-[14px] text-secundary-light">Barrio de la tienda:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.barrio_tienda.length }} / {{ limitesCaracteres.barrio_tienda }}
                      </p>
                    </div>

                    <div
                      class="w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class="text-[20px] pl-[5px] material-symbols-rounded" :class="[textoClase]">pin_drop</span>
                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa el departamento de la tienda" v-model="form.barrio_tienda"
                        @input="handleInput($event, 'barrio_tienda')" @blur="handleBlur('barrio_tienda')" />
                    </div>
                  </div>

                  
                </div>
                <div class="right-table w-[50%] flex flex-col gap-1">
                  <div class="email-tienda">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="email-tienda" class="text-[14px] text-secundary-light">Email corporativo:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.email_tienda.length }} / {{ limitesCaracteres.email_tienda }}
                      </p>
                    </div>

                    <div
                      class="w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class="text-[20px] pl-[5px] material-symbols-rounded" :class="[textoClase]">email</span>
                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa el email de la tienda" v-model="form.email_tienda"
                        @input="handleInput($event, 'email_tienda')" @blur="handleBlur('email_tienda')" />
                    </div>
                  </div>

                  <div class="telefono-tienda">
                    <div
                      class="contador-input flex items-center justify-between xl:flex xl:items-center xl:justify-between">
                      <label for="telefono-tienda" class="text-[14px] text-secundary-light">Teléfono corporativo:</label>
                      <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                        {{ form.telefono_tienda.length }} / {{ limitesCaracteres.telefono_tienda }}
                      </p>
                    </div>

                    <div
                      class="w-[100%] p-[3px] flex items-center gap-[8px] xl:w-[100%] xl:p-[3px] xl:flex xl:items-center xl:gap-[8px] transition-all rounded-[5px] border-[1px] border-secundary-light">
                      <span class="text-[20px] pl-[5px] material-symbols-rounded" :class="[textoClase]">phone</span>
                      <input type="text" class="w-full focus:outline-none focus:border-none font-normal bg-transparent"
                        placeholder="Ingresa el teléfono de la tienda" v-model="form.telefono_tienda"
                        @input="handleInput($event, 'telefono_tienda')" @blur="handleBlur('telefono_tienda')" />
                    </div>
                  </div>


                 
                </div>
              </div>


            </div>

          


            <div v-else-if="activeTab === 2">
              <h2 class="text-2xl font-bold mb-4">Configuraciones Avanzadas</h2>
              <p>Esta sección está reservada para configuraciones avanzadas que no están disponibles en la interfaz
                principal.</p>
              <p>Próximamente...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>
