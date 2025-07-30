import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
    }),

    getters: {
        // --- GETTERS BASE (AUXILIARES) ---
        // Estos getters simplifican el acceso y proporcionan seguridad.
        isAuthenticated: (state) => !!state.user,
        perfilUsuario: (state) => state.user?.perfil_usuario || null,
        perfilEmpleado: (state) => state.user?.perfil_empleado || null,
        // CORREGIDO: Se usa el nombre 'snake_case' que envía Laravel.
        establecimientoAsignado: (state) => state.user?.establecimiento_asignado || null,
        aplicacionWeb() { return this.establecimientoAsignado?.aplicacion_web || null; },

        // --- DATOS DE USUARIO ---
        // Todos los getters ahora son seguros y usan los auxiliares para mayor claridad.
        idUsuario() { return this.perfilUsuario?.id || null; },
        google_id: (state) => state.user?.google_id || null,
        estadoUsuario() { return this.perfilUsuario?.estado?.tipo_estado || null; },
        rol() { return this.perfilUsuario?.rol?.tipo_rol || null; },
        rol_id() { return this.perfilUsuario?.rol?.id || null; },
        rutaFoto() { return this.perfilUsuario?.ruta_foto || null; },
        fotoUrlCompletaUsuario() {
            const ruta = this.rutaFoto;
            if (!ruta) return null;
            return ruta.startsWith("http") ? ruta : `/storage/${ruta}`;
        },
        primerNombre() { return this.perfilUsuario?.primer_nombre || null; },
        segundoNombre() { return this.perfilUsuario?.segundo_nombre || null; },
        primerApellido() { return this.perfilUsuario?.primer_apellido || null; },
        segundoApellido() { return this.perfilUsuario?.segundo_apellido || null; },
        nombreCompleto() {
            if (!this.perfilUsuario) return "";
            return `${this.primerNombre || ''} ${this.segundoNombre || ''} ${this.primerApellido || ''}`.replace(/\s+/g, ' ').trim();
        },
        indicativo() { return this.perfilUsuario?.indicativo?.codigo_pais || null; },
        indicativo_id() { return this.perfilUsuario?.indicativo?.id || null; },
        telefono() { return this.perfilUsuario?.telefono || null; },
        telefonoCompleto() {
            if (!this.indicativo || !this.telefono) return null;
            return `${this.indicativo}${this.telefono}`;
        },
        tipoDocumento() { return this.perfilUsuario?.tipo_documento?.documento_legal || null; },
        tipoDocumento_id() { return this.perfilUsuario?.tipo_documento?.id || null; },
        numero_documento: (state) => state.user?.numero_documento || null,
        email() { return this.perfilUsuario?.correo || null; },
        genero() { return this.perfilUsuario?.genero || null; },
        direccionResidencia() { return this.perfilUsuario?.direccion_residencia || null; },
        ciudadResidencia() { return this.perfilUsuario?.ciudad_residencia || null; },
        barrioResidencia() { return this.perfilUsuario?.barrio_residencia || null; },
        createdAt() { return this.perfilUsuario?.created_at || null; },
        updateAt() { return this.perfilUsuario?.updated_at || null; },
        creadoPor() { return this.perfilUsuario?.created_by || null; },
        ActualizadoPor() { return this.perfilUsuario?.updated_by || null; },

        // --- DATOS DE EMPLEADO ---
        estadoEmpleado() { return this.perfilEmpleado?.estado?.tipo_estado || null; },
        establecimientoIdAsignado() { return this.perfilEmpleado?.establecimiento_id || null; },
        medioPagoEmpleado() { return this.perfilEmpleado?.medio_pago?.forma_pago || null; },
        cargoEmpleado() { return this.perfilEmpleado?.cargo || null; },
        salarioBaseEmpleado() { return this.perfilEmpleado?.salario_base || null; },
        cuentaAhorrosEmpleado() { return this.perfilEmpleado?.cuenta_ahorros || null; },
        bancoEmpleado() { return this.perfilEmpleado?.banco || null; },
        horariosEmpleado() { return this.perfilEmpleado?.horario || null; },
        tipoContratoEmpleado() { return this.perfilEmpleado?.tipo_contrato || null; },
        documentoIngresoEmpleado() { return this.perfilEmpleado?.documentos_zip || null; },
        fechaIngresoEmpleado() { return this.perfilEmpleado?.fecha_ingreso || null; },
        fechaEgresoEmpleado() { return this.perfilEmpleado?.fecha_egreso || "No aplica"; },
        fechaModificacionEmpleado() { return this.perfilEmpleado?.fecha_modificacion || null; },

        // --- DATOS DE ESTABLECIMIENTO ASIGNADO ---
        establecimiento_id() { return this.establecimientoAsignado?.id || null; },
        estadoestablecimiento() { return this.establecimientoAsignado?.estado?.tipo_estado || null; },
        rutaFotoEstablecimiento() { return this.establecimientoAsignado?.ruta_foto_establecimiento || null; },
        fotoUrlCompletaEstablecimiento() {
            const ruta = this.rutaFotoEstablecimiento;
            if (!ruta) return null;
            return ruta.startsWith('http') ? ruta : `/storage/${ruta}`;
        },
        tipoEstablecimiento() { return this.establecimientoAsignado?.tipo_establecimiento || null; },
        nombreestablecimiento() { return this.establecimientoAsignado?.nombre_establecimiento || null; },
        emailEstablecimiento() { return this.establecimientoAsignado?.email_establecimiento || null; },
        telefonoEstablecimiento() { return this.establecimientoAsignado?.telefono_establecimiento || null; },
        direccionEstablecimiento() { return this.establecimientoAsignado?.direccion_establecimiento || null; },
        barrioEstablecimiento() { return this.establecimientoAsignado?.barrio_establecimiento || null; },
        ciudadEstablecimiento() { return this.establecimientoAsignado?.ciudad_establecimiento || null; },

        // --- DATOS DE TOKEN ---
        estadoToken() { return this.establecimientoAsignado?.token?.estado?.tipo_estado || null; },
        tokenestablecimiento() { return this.establecimientoAsignado?.token?.token_activacion || null; },

        // --- DATOS DE APP ---
        aplicacion() { return this.aplicacionWeb?.nombre_app || null; },
        categoriaApp() { return this.aplicacionWeb?.categoria_app || null; },
        descripcionApp() { return this.aplicacionWeb?.descripcion_app || null; },
        membresia() { return this.aplicacionWeb?.membresia?.nombre_membresia || null; },
        precioMembresia() { return this.aplicacionWeb?.membresia?.precio_membresia || null; },
        periodoMembresia() { return this.aplicacionWeb?.membresia?.periodo_duracion || null; },
        duracionMembresia() { return this.aplicacionWeb?.membresia?.duracion_membresia || null; },
        descripcionMembresia() { return this.aplicacionWeb?.membresia?.descripcion_corta || null; },
        bgColor() { return this.aplicacionWeb?.estilo?.color_fondo || null; },

        // --- DATOS DE FACTURACION ---
        estadoFactura() { return this.establecimientoAsignado?.facturas?.[0]?.estado?.tipo_estado || null; },
        medioPagoFactura() { return this.establecimientoAsignado?.facturas?.[0]?.medio_pago?.forma_pago || null; },
        montoFactura() { return this.establecimientoAsignado?.facturas?.[0]?.monto_total || null; },
        diasRestantesMembresia() { return this.establecimientoAsignado?.facturas?.[0]?.dias_restantes || null; },
        fechaPago() { return this.establecimientoAsignado?.facturas?.[0]?.fecha_pago || null; },

        // --- DATOS DE ESTILO ---
        estiloApp() { return this.aplicacionWeb?.estilo?.nombre_relacion || null; },
        colorPrimario() { return this.aplicacionWeb?.estilo?.primary || null; },
        colorSecundario() { return this.aplicacionWeb?.estilo?.secondary || null; },
        icono() { return this.aplicacionWeb?.estilo?.icono || null; },
       
    },

    actions: {
        setUser(user) {
            this.user = user;
        },
        clearUser() {
            this.user = null;
        },
    },
});
