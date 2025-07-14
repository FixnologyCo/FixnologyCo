import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
 
  state: () => ({
    user: null,
  }),

  getters: {
    // esta activo
    isAuthenticated: (state) => !!state.user,

    // datos de usuario
    rol: (state) => state.user?.rol?.tipo_rol,
    rutaFoto: (state) => state.user?.perfil_usuario?.ruta_foto,
    primerNombre: (state) => state.user?.perfil_usuario?.primer_nombre,
    primerApellido: (state) => state.user?.perfil_usuario?.primer_apellido,
    nombreCompleto: (state) => {
      return `${state.user.perfil_usuario.primer_nombre} ${state.user.perfil_usuario.primer_apellido}`.trim();
    },
    perfilUsuario: (state) => state.user?.perfil_usuario,

    // datos de empleado

    // datos de tienda
    nombreTienda: (state) => state.user.tienda[0].nombre_establecimiento,

    // datos de token

    // datos de app
    aplicacion: (state) => state.user.tienda[0].aplicacion_web.nombre_app,
    membresia: (state) => state.user.tienda[0].aplicacion_web.membresia.nombre_membresia,

    // datos de facturacion
    
  },

  
  actions: {
    setUser(user) {
      this.user = user;
    },
    clearUser() {
        this.user = null;
    }
  },
});