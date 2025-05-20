<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f2f4f8; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #1a202c;">Hola 👋</h2>
        <p>Recibiste este correo porque solicitaste restablecer tu contraseña en <strong>Fixnology Co</strong>.</p>
        <p>Haz clic en el botón de abajo para crear una nueva contraseña:</p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #1e40af; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">
                Restablecer contraseña
            </a>
        </p>

        <p>Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>
        <p>Este enlace expirará en 60 minutos por razones de seguridad.</p>

        <p style="margin-top: 30px;">Gracias,<br>Equipo Fixnology Co</p>
    </div>
</body>
</html>
