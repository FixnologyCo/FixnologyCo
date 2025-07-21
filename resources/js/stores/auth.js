import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {

    state: () => ({
        user: null,
    }),

    getters: {
        // esta activo
        isAuthenticated: (state) => !!state.user,

        // datos de usuario
        idUsuario: (state) => state.user.perfil_usuario.id || 'Cargando info...',
        google_id: (state) => state.user.google_id || null,
        estadoUsuario: (state) => state.user.perfil_usuario.estado.tipo_estado || 'Cargando info...',
        rol: (state) => state.user?.rol?.tipo_rol || 'Cargando info...',
        rutaFoto: (state) => state.user?.perfil_usuario?.ruta_foto || 'Sin foto',
        primerNombre: (state) => state.user?.perfil_usuario?.primer_nombre || 'Cargando info...',
        segundoNombre: (state) => state.user?.perfil_usuario?.segundo_nombre || 'Cargando info...',
        primerApellido: (state) => state.user?.perfil_usuario?.primer_apellido || 'Cargando info...',
        segundoApellido: (state) => state.user?.perfil_usuario?.segundo_apellido || 'Cargando info...',
        nombreCompleto: (state) => {
            return `${state.user.perfil_usuario.primer_nombre} ${state.user.perfil_usuario.segundo_nombre} ${state.user.perfil_usuario.primer_apellido}`.trim();
        },
        indicativo: (state) => state.user.perfil_usuario.indicativo.codigo_pais || 'Cargando info...',
        telefono: (state) => state.user.perfil_usuario.telefono || 'Cargando info...',
        telefonoCompleto: (state) => {
            return `${state.user.perfil_usuario.indicativo.codigo_pais}${state.user.perfil_usuario.telefono}`.trim();
        },
        tipoDocumento: (state) => state.user.perfil_usuario.tipo_documento.documento_legal || 'Cargando info...',
        numero_documento: (state) => state.user.numero_documento || 'Cargando info...',
        email: (state) => state.user.perfil_usuario.correo || 'Cargando info...',
        genero: (state) => state.user.perfil_usuario.genero || 'Cargando info...',
        direccionResidencia: (state) => state.user.perfil_usuario.direccion_residencia || 'Cargando info...',
        ciudadResidencia: (state) => state.user.perfil_usuario.ciudad_residencia || 'Cargando info...',
        barrioResidencia: (state) => state.user.perfil_usuario.barrio_residencia || 'Cargando info...',
        createdAt: (state) => state.user.perfil_usuario.created_at || 'Cargando info...',
        updateAt: (state) => state.user.perfil_usuario.updated_at || 'Cargando info...',
        creadoPor: (state) => state.user.perfil_usuario.created_by || 'Cargando info...',
        ActualizadoPor: (state) => state.user.perfil_usuario.updated_by || 'Cargando info...',

        // datos de empleado
        estadoEmpleado: (state) => state.user.perfil_empleado.estado.tipo_estado || 'Cargando info...',
        establecimientoAsignado: (state) => state.user.perfil_empleado.establecimiento_id || 'Cargando info...',
        medioPagoEmpleado: (state) => state.user.perfil_empleado.medio_pago.forma_pago || 'Cargando info...',
        cargoEmpleado: (state) => state.user.perfil_empleado.cargo || 'Cargando info...',
        salarioBaseEmpleado: (state) => state.user.perfil_empleado.salario_base || 'Cargando info...',
        cuentaAhorrosEmpleado: (state) => state.user.perfil_empleado.cuenta_ahorros || 'Cargando info...',
        bancoEmpleado: (state) => state.user.perfil_empleado.banco || 'Cargando info...',
        horariosEmpleado: (state) => state.user.perfil_empleado.horario || 'Cargando info...',
        tipoContratoEmpleado: (state) => state.user.perfil_empleado.tipo_contrato || 'Cargando info...',
        documentoIngresoEmpleado: (state) => state.user.perfil_empleado.documentos_zip || 'Cargando info...',
        fechaIngresoEmpleado: (state) => state.user.perfil_empleado.fecha_ingreso || 'Cargando info...',
        fechaEgresoEmpleado: (state) => state.user.perfil_empleado.fecha_egreso || 'No aplica',
        fechaModificacionEmpleado: (state) => state.user.perfil_empleado.fecha_modificacion || 'Cargando info...',

        // datos de tienda
        estadoTienda: (state) => state.user.tienda[0].estado.tipo_estado || 'Cargando info...',
        rutaFotoEstablecimiento: (state) => state.user.tienda[0].ruta_foto_establecimiento || 'Sin foto',
        tipoEstablecimiento: (state) => state.user.tienda[0].tipo_establecimiento || 'Cargando info...',
        nombreTienda: (state) => state.user.tienda[0].nombre_establecimiento || 'Cargando info...',
        emailEstablecimiento: (state) => state.user.tienda[0].email_establecimiento || 'Cargando info...',
        telefonoEstablecimiento: (state) => state.user.tienda[0].telefono_establecimiento || 'Cargando info...',
        direccionEstablecimiento: (state) => state.user.tienda[0].direccion_establecimiento || 'Cargando info...',
        barrioEstablecimiento: (state) => state.user.tienda[0].barrio_establecimiento || 'Cargando info...',
        ciudadEstablecimiento: (state) => state.user.tienda[0].ciudad_establecimiento || 'Cargando info...',
        
        // datos de token
        estadoToken: (state) => state.user.tienda[0].token.estado.tipo_estado || 'Cargando info...',
        tokenTienda: (state) => state.user.tienda[0].token.token_activacion || 'Cargando info...',

        
        // datos de app
        aplicacion: (state) => state.user.tienda[0].aplicacion_web.nombre_app || 'Cargando info...',
        categoriaApp: (state) => state.user.tienda[0].aplicacion_web.categoria_app || 'Cargando info...',
        descripcionApp: (state) => state.user.tienda[0].aplicacion_web.descripcion_app || 'Cargando info...',
        membresia: (state) => state.user.tienda[0].aplicacion_web.membresia.nombre_membresia || 'Cargando info...',
        precioMembresia: (state) => state.user.tienda[0].aplicacion_web.membresia.precio_membresia || 'Cargando info...',
        periodoMembresia: (state) => state.user.tienda[0].aplicacion_web.membresia.periodo_duracion || 'Cargando info...',
        duracionMembresia: (state) => state.user.tienda[0].aplicacion_web.membresia.duracion_membresia || 'Cargando info...',
        descripcionMembresia: (state) => state.user.tienda[0].aplicacion_web.membresia.descripcion_corta || 'Cargando info...',

        // datos de facturacion
        estadoFactura: (state) => state.user.tienda[0].facturas[0].estado.tipo_estado || 'Cargando info...',
        medioPagoFactura: (state) => state.user.tienda[0].facturas[0].medio_pago.forma_pago || 'Cargando info...',
        montoFactura: (state) => state.user.tienda[0].facturas[0].monto_total || 'Cargando info...',
        diasRestantesMembresia: (state) => state.user.tienda[0].facturas[0].dias_restantes || 'Cargando info...',
        fechaPago: (state) => state.user.tienda[0].facturas[0].fecha_pago || 'Cargando info...'

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