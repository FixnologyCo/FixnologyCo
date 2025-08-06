export const getInicialesUsuario = (usuario) => {
        if (!usuario || !usuario.perfil_usuario) {
            return "";
        }
        const primerNombre = usuario.perfil_usuario.primer_nombre || "";
        const primerApellido = usuario.perfil_usuario.primer_apellido || "";

        const inicialNombre = primerNombre.charAt(0).toUpperCase();
        const inicialApellido = primerApellido.charAt(0).toUpperCase();

        return inicialNombre && inicialApellido
            ? `${inicialNombre}${inicialApellido}`
            : inicialNombre;
    }

export const getInicialesEstablecimiento = (establecimiento) => {
        const nombreEstablecimiento = establecimiento.establecimiento_asignado?.nombre_establecimiento || "Sin la tienda";
        const palabrasAIgnorar = ["de", "el", "la", "los", "las", "y", "a"];
        const iniciales = nombreEstablecimiento
            .split(" ")
            .filter((palabra) => !palabrasAIgnorar.includes(palabra.toLowerCase()))
            .map((palabra) => palabra[0])
            .join("");

        return iniciales.toUpperCase().slice(0, 2);
    }

   