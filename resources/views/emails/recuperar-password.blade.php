<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #0B192C; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(255, 255, 255, 0.05); border: 1px solid #f2f4f8; color: #f2f4f8;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="box-shadow: 0px 8px 20px #f05235; width: 36px; height: 25px; border-radius: 20%; background: #f05235;"></div>
            <h1 style="margin: 0px 10px; font-size: 24px;">Fixnology CO</h1>
        </div>

        <h2 style="color: #f2f4f8;">¡Hola, <span style="color: #f2f4f8;">{{ $nombre }}!</span> 👋</h2>
        <p style="color:#f2f4f8;">Recibiste este correo porque solicitaste restablecer tu contraseña en <strong>Fixnology Co</strong>.</p>
        <p style="color:#f2f4f8;">Haz clic en el botón de abajo para crear una nueva contraseña:</p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #f05235; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">
                Restablecer contraseña
            </a>
        </p>

        <p style="color:#f2f4f8;">Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>
        <p style="color: #f05235;">Este enlace expirará en 5 minutos por razones de seguridad.</p>

        <p style="color:#f2f4f8; margin-top: 30px; font-weight: bolder;">Gracias,<br>Equipo Fixnology Co</p>
    </div>
</body>
</html>
