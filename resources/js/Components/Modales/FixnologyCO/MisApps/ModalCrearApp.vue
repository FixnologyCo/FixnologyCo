<script setup>
import { defineProps, defineEmits, watch, reactive } from 'vue'
import 'dayjs/locale/es';
import Colors from '@/Composables/ModularColores';
import { handleBlur, handleInput, limitesCaracteres } from '@/Utils/formateoInputs'

const { appName, bgClase, bgOpacity, focus, textoClase, borderClase, buttonFocus, hoverClase, hoverTexto, buttonClase, buttonSecundario } = Colors();

const props = defineProps({
    mostrar: Boolean,
    estados: {
        type: Array,
        required: true,
    },
    plan_aplicaciones: {
        type: Array,
        required: true,
    },
    membresias: {
        type: Array,
        required: true,
    }
})

const emit = defineEmits(['cerrar'])


const form = reactive({
    nombre_app: '',
    descripcion_app: '',
    id_estado: '',
    id_plan_aplicacion: '',
    id_membresia: ''
})
</script>

<template>
    <transition name="fade">
        <div v-if="props.mostrar" @click.self="emit('cerrar')"
            class="fixed inset-0 bg-mono-negro bg-opacity-65 backdrop-blur-[2px] pr-5 flex items-center justify-end z-50">
            <!-- Modal deslizante -->
            <transition name="slide">
                <div
                    class="bg-secundary-default border border-secundary-light p-6 rounded-lg w-full max-w-lg relative text-mono-blanco transform transition-transform duration-300">
                    <div class="headerPop flex items-center justify-between">
                        <h2 class="text-xl font-bold">Crear nueva aplicación</h2>
                        <i class="material-symbols-rounded cursor-pointer" @click.self="emit('cerrar')"
                            :class="[hoverTexto]">cancel</i>
                    </div>

                    <div class="caja my-4">

                        <div class="flex items-center justify-between">
                            <label for="rol-usuario"
                                class="text-[14px] text-secundary-default dark:text-secundary-light">Nombre de
                                aplicación:</label>

                            <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                                {{ form.nombre_app.length }} / {{ limitesCaracteres.nombre_app }}
                            </p>
                        </div>

                        <div
                            class="flex items-center titulo-icono w-full rounded-lg p-5 border border-secundary-light my-4 gap-3">
                            <div class="flex items-center gap-1">
                                <div class="iconoApp bg-red-400 w-[50px] h-[50px] rounded-md grid place-content-center">
                                    <span class="material-symbols-rounded text-[35px]">bolt</span>
                                </div>
                                <span class="material-symbols-rounded cursor-pointer">keyboard_arrow_down</span>
                            </div>

                            <input type="text"
                                class="w-full border border-secundary-light rounded-md px-2 py-1  focus:outline-none focus:border-none font-normal bg-transparent text-secundary-default dark:text-mono-blanco"
                                placeholder="Ingresa el nombre de la app" v-model="form.nombre_app"
                                @blur="handleBlur(form, 'nombre_app')"
                                @input="(e) => handleInput(e, form, 'nombre_app')" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="rol-usuario"
                                class="text-[14px] text-secundary-default dark:text-secundary-light">descripción de
                                la aplicación:</label>

                            <p class="2xl:text-[10px] xl:text-[12px] text-[8px] text-right  text-secundary-light">
                                {{ form.descripcion_app.length }} / {{ limitesCaracteres.descripcion_app }}
                            </p>
                        </div>
                        <div class="">
                            <input type="text"
                                class="w-full border border-secundary-light rounded-md px-2 py-1 my-2 focus:outline-none focus:border-none font-normal bg-transparent text-secundary-default dark:text-mono-blanco"
                                placeholder="Ingresa una breve descripción" v-model="form.descripcion_app"
                                @blur="handleBlur(form, 'descripcion_app')"
                                @input="(e) => handleInput(e, form, 'descripcion_app')" />
                        </div>

                        <div class="tags flex gap-2 my-2">
                            <div class="desplegar-estados w-[33.3%]">
                                <div class="flex items-center justify-between">
                                    <label for="rol-usuario"
                                        class="text-[14px] text-secundary-default dark:text-secundary-light">Estado:</label>
                                </div>

                                <div class="custom-select">
                                    <select v-model="form.id_estado"
                                        class="w-full 2xl:p-2 border border-secundary-light rounded-md">
                                        <option value="" disabled>Disponibles: {{ estados.length }}</option>
                                        <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                                            {{ estado.tipo_estado }}
                                        </option>
                                    </select>
                                    <div class="select-arrow"></div>
                                </div>
                            </div>

                             <div class="desplegar-plan w-[33.3%]">
                                <div class="flex items-center justify-between">
                                    <label for="rol-usuario"
                                        class="text-[14px] text-secundary-default dark:text-secundary-light">Plan aplicacion:</label>
                                </div>

                                <div class="custom-select">
                                    <select v-model="form.id_plan_aplicacion"
                                        class="w-full 2xl:p-2 border border-secundary-light rounded-md">
                                        <option value="" disabled>Disponibles: {{ plan_aplicaciones.length }}</option>
                                        <option v-for="plan in plan_aplicaciones" :key="plan.id" :value="plan.id">
                                            {{ plan.nombre_plan }}
                                        </option>
                                    </select>
                                    <div class="select-arrow"></div>
                                </div>
                            </div>

                             <div class="desplegar-membresia w-[33.3%]">
                                <div class="flex items-center justify-between">
                                    <label for="rol-usuario"
                                        class="text-[14px] text-secundary-default dark:text-secundary-light">Membresía:</label>
                                </div>

                                <div class="custom-select">
                                    <select v-model="form.id_membresia"
                                        class="w-full 2xl:p-2 border border-secundary-light rounded-md">
                                        <option value="" disabled>Disponibles: {{ membresias.length }}</option>
                                        <option v-for="membresia in membresias" :key="membresia.id" :value="membresia.id">
                                            {{ membresia.nombre_membresia }}
                                        </option>
                                    </select>
                                    <div class="select-arrow"></div>
                                </div>
                            </div>

                        </div>

                        <button class="w-full mt-10" :class="[buttonClase]">Crear aplicación <span class="material-symbols-rounded">send</span></button>

                    </div>

                </div>
            </transition>
        </div>
    </transition>
</template>

<style scoped>
/* Fade para fondo */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide para el modal */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from {
    transform: translateX(100%);
}

.slide-leave-to {
    transform: translateX(100%);
}
</style>