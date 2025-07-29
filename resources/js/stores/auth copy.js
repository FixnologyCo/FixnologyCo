import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
    }),

    getters: {
        // --- GETTERS BASE (AUXILIARES) ---
        // Estos getters simplifican el acceso y proporcionan seguridad.
        isAuthenticated: (state) => !!state.user,
        perfilUsuario: (state) => state.user?.perfilUsuario || null,
        perfilEmpleado: (state) => state.user?.perfilEmpleado || null,
        establecimientoAsignado: (state) => state.user?.establecimientoAsignado || null,
        aplicacionWeb: (state) => state.user?.establecimientoAsignado?.aplicacionWeb || null,
        
        // --- DATOS DE USUARIO ---
        idUsuario: (state) => state.user?.id || null,
        
        // CORREGIDO: Se convirtieron a funciones normales para que 'this' funcione correctamente.
        rol(state) {
            return this.perfilUsuario?.rol?.tipo_rol || null;
        },
        rol_id(state) {
            return this.perfilUsuario?.rol?.id || null;
        },
        
        nombreCompleto(state) {
            if (!this.perfilUsuario) return null;
            const { primer_nombre, segundo_nombre, primer_apellido } = this.perfilUsuario;
            return [primer_nombre, segundo_nombre, primer_apellido].filter(Boolean).join(' ').trim();
        },

        fotoUrlCompleta(state) {
            // CORREGIDO: Se referencia 'this.perfilUsuario' que sí existe como getter.
            const ruta = this.perfilUsuario?.ruta_foto;
            if (!ruta) return null; // O una imagen por defecto: '/images/default-avatar.png'
            return ruta.startsWith('http') ? ruta : `/storage/${ruta}`;
        },

        email: (state) => state.user?.perfilUsuario?.correo || null,
        telefono: (state) => state.user?.perfilUsuario?.telefono || null,
        numero_documento: (state) => state.user?.numero_documento || null,

        // --- DATOS DE EMPLEADO ---
        cargo: (state) => state.user?.perfilEmpleado?.cargo || null,
        salario: (state) => state.user?.perfilEmpleado?.salario_base || null,
        fechaIngreso: (state) => state.user?.perfilEmpleado?.fecha_ingreso || null,
        
        // --- DATOS DEL ESTABLECIMIENTO ASIGNADO ---
        nombreEstablecimiento: (state) => state.user?.establecimientoAsignado?.nombre_establecimiento || null,
        idEstablecimiento: (state) => state.user?.establecimientoAsignado?.id || null,

        // --- DATOS DE LA APLICACIÓN WEB ---
        nombreApp: (state) => state.user?.establecimientoAsignado?.aplicacionWeb?.nombre_app || 'FixnologyCO',
        membresiaApp: (state) => state.user?.establecimientoAsignado?.aplicacionWeb?.membresia?.nombre_membresia || null,
        bgColorApp: (state) => state.user?.establecimientoAsignado?.aplicacionWeb?.estilo?.color_fondo || 'bg-gray-800',

        // --- DATOS DE FACTURACIÓN (EJEMPLO CON LA PRIMERA FACTURA) ---
        primeraFactura: (state) => state.user?.establecimientoAsignado?.facturas?.[0] || null,
        
        // CORREGIDO: Se convirtieron a funciones normales para que 'this' funcione correctamente.
        montoPrimeraFactura(state) {
            return this.primeraFactura?.monto_total || null;
        },
        estadoPrimeraFactura(state) {
            return this.primeraFactura?.estado?.tipo_estado || null;
        },
    },

    actions: {
        /**
         * Establece el usuario en el estado.
         * @param {object} userData - Los datos del usuario recibidos desde el backend.
         */
        setUser(userData) {
            this.user = userData;
        },

        /**
         * Limpia los datos del usuario para cerrar la sesión.
         */
        clearUser() {
            this.user = null;
        },
    },
});
