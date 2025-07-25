import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
    }),

    getters: {
        // esta activo
        isAuthenticated: (state) => !!state.user,

        // datos de usuario
        idUsuario: (state) =>
            state.user.perfil_usuario.id || null,
        google_id: (state) => state.user.google_id || null,
        estadoUsuario: (state) =>
            state.user.perfil_usuario.estado.tipo_estado || null,
        rol: (state) => state.user?.rol?.tipo_rol || null,
        rol_id: (state) => state.user?.rol?.id,
        rutaFoto(state) {
            return state.user?.perfil_usuario?.ruta_foto || null;
        },
        fotoUrlCompletaUsuario(state) {
            const ruta = this.rutaFoto;
            if (ruta) {

                if (ruta.startsWith("http")) {
                    return ruta;
                }
                return `/storage/${ruta}`;
            }
            return null;
        },
        primerNombre: (state) =>
            state.user?.perfil_usuario?.primer_nombre || null,
        segundoNombre: (state) =>
            state.user?.perfil_usuario?.segundo_nombre || null,
        primerApellido: (state) =>
            state.user?.perfil_usuario?.primer_apellido || null,
        segundoApellido: (state) =>
            state.user?.perfil_usuario?.segundo_apellido || null,
        nombreCompleto: (state) => {
            return `${state.user.perfil_usuario.primer_nombre} ${state.user.perfil_usuario.segundo_nombre} ${state.user.perfil_usuario.primer_apellido}`.trim();
        },
        indicativo: (state) =>
            state.user.perfil_usuario.indicativo.codigo_pais ||
            null,
        indicativo_id: (state) => state.user.perfil_usuario.indicativo.id || 1,
        telefono: (state) =>
            state.user.perfil_usuario.telefono || null,
        telefonoCompleto: (state) => {
            return `${state.user.perfil_usuario.indicativo.codigo_pais}${state.user.perfil_usuario.telefono}`.trim();
        },
        tipoDocumento: (state) =>
            state.user.perfil_usuario.tipo_documento.documento_legal ||
            null,
        tipoDocumento_id: (state) =>
            state.user.perfil_usuario.tipo_documento.id || 1,
        numero_documento: (state) =>
            state.user.numero_documento || null,
        email: (state) =>
            state.user.perfil_usuario.correo || null,
        genero: (state) =>
            state.user.perfil_usuario.genero || null,
        direccionResidencia: (state) =>
            state.user.perfil_usuario.direccion_residencia ||
            null,
        ciudadResidencia: (state) =>
            state.user.perfil_usuario.ciudad_residencia || null,
        barrioResidencia: (state) =>
            state.user.perfil_usuario.barrio_residencia || null,
        createdAt: (state) =>
            state.user.perfil_usuario.created_at || null,
        updateAt: (state) =>
            state.user.perfil_usuario.updated_at || null,
        creadoPor: (state) =>
            state.user.perfil_usuario.created_by || null,
        ActualizadoPor: (state) =>
            state.user.perfil_usuario.updated_by || null,

        // datos de empleado
        estadoEmpleado: (state) =>
            state.user.perfil_empleado.estado.tipo_estado || null,
        establecimientoAsignado: (state) =>
            state.user.perfil_empleado.establecimiento_id || null,
        medioPagoEmpleado: (state) =>
            state.user.perfil_empleado.medio_pago.forma_pago ||
            null,
        cargoEmpleado: (state) =>
            state.user.perfil_empleado.cargo || null,
        salarioBaseEmpleado: (state) =>
            state.user.perfil_empleado.salario_base || null,
        cuentaAhorrosEmpleado: (state) =>
            state.user.perfil_empleado.cuenta_ahorros || null,
        bancoEmpleado: (state) =>
            state.user.perfil_empleado.banco || null,
        horariosEmpleado: (state) =>
            state.user.perfil_empleado.horario || null,
        tipoContratoEmpleado: (state) =>
            state.user.perfil_empleado.tipo_contrato || null,
        documentoIngresoEmpleado: (state) =>
            state.user.perfil_empleado.documentos_zip || null,
        fechaIngresoEmpleado: (state) =>
            state.user.perfil_empleado.fecha_ingreso || null,
        fechaEgresoEmpleado: (state) =>
            state.user.perfil_empleado.fecha_egreso || "No aplica",
        fechaModificacionEmpleado: (state) =>
            state.user.perfil_empleado.fecha_modificacion || null,

        // datos de tienda
        tienda_id: (state) => state.user.tienda[0].id || 8,
        estadoTienda: (state) =>
            state.user.tienda[0].estado.tipo_estado || null,
        rutaFotoEstablecimiento: (state) =>
            state.user.tienda[0].ruta_foto_establecimiento || null,
        fotoUrlCompletaEstablecimiento(state) {
            const ruta = this.rutaFotoEstablecimiento; 
            if (ruta) {
               
                if (ruta.startsWith('http')) {
                    return ruta;
                }
                
                return `/storage/${ruta}`;
            }
            return null;
        },
        tipoEstablecimiento: (state) =>
            state.user.tienda[0].tipo_establecimiento || null,
        nombreTienda: (state) =>
            state.user.tienda[0].nombre_establecimiento || null,
        emailEstablecimiento: (state) =>
            state.user.tienda[0].email_establecimiento || null,
        telefonoEstablecimiento: (state) =>
            state.user.tienda[0].telefono_establecimiento || null,
        direccionEstablecimiento: (state) =>
            state.user.tienda[0].direccion_establecimiento ||
            null,
        barrioEstablecimiento: (state) =>
            state.user.tienda[0].barrio_establecimiento || null,
        ciudadEstablecimiento: (state) =>
            state.user.tienda[0].ciudad_establecimiento || null,

        // datos de token
        estadoToken: (state) =>
            state.user.tienda[0].token.estado.tipo_estado || null,
        tokenTienda: (state) =>
            state.user.tienda[0].token.token_activacion || null,

        // datos de app
        aplicacion: (state) =>
            state.user.tienda[0].aplicacion_web.nombre_app ||
            null,
        categoriaApp: (state) =>
            state.user.tienda[0].aplicacion_web.categoria_app ||
            null,
        descripcionApp: (state) =>
            state.user.tienda[0].aplicacion_web.descripcion_app ||
            null,
        membresia: (state) =>
            state.user.tienda[0].aplicacion_web.membresia.nombre_membresia ||
            null,
        precioMembresia: (state) =>
            state.user.tienda[0].aplicacion_web.membresia.precio_membresia ||
            null,
        periodoMembresia: (state) =>
            state.user.tienda[0].aplicacion_web.membresia.periodo_duracion ||
            null,
        duracionMembresia: (state) =>
            state.user.tienda[0].aplicacion_web.membresia.duracion_membresia ||
            null,
        descripcionMembresia: (state) =>
            state.user.tienda[0].aplicacion_web.membresia.descripcion_corta ||
            null,
        bgColor: (state) =>
            state.user.tienda[0].aplicacion_web.estilo.color_fondo ||
            null,

        // datos de facturacion
        estadoFactura: (state) =>
            state.user.tienda[0].facturas[0].estado.tipo_estado ||
            null,
        medioPagoFactura: (state) =>
            state.user.tienda[0].facturas[0].medio_pago.forma_pago ||
            null,
        montoFactura: (state) =>
            state.user.tienda[0].facturas[0].monto_total || null,
        diasRestantesMembresia: (state) =>
            state.user.tienda[0].facturas[0].dias_restantes ||
            null,
        fechaPago: (state) =>
            state.user.tienda[0].facturas[0].fecha_pago || null,
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
