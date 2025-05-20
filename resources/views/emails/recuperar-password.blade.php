<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body style="font-family: Arial, sans-serif; background-color: #141414; padding: 20px; color: #f3fafe ;">
    <div class="logo
         
          flex items-center gap-2 justify-center
          ">
        <div class="gota
           
            h-5 w-8 rounded-full bg-[#f05235]
            "
            style="box-shadow: 0px 8px 20px #FE4F2D;"></div>
        <div class="logo">
            <h1 class="text-[20px] font-semibold">Fixnology CO</h1>
            <p class="-mt-[8px] text-[14px] font-medium">Empresa de software especializada</p>
        </div>
    </div>

    <div class="welcome flex items-center flex-col gap-3">

        <h2 class="
           
            font-bold text-[22px] mt-3 text-center
            
            ">¡Hola! 👋</h2>
        <p class="
            
            text-[15px]
            text-center
            px-8
            ">Recibiste este correo porque solicitaste restablecer tu contraseña en <strong>Fixnology Co</strong>.</p>

            <a href="{{ $url }}">
        <button type="submit" style="box-shadow: 0px 8px 20px #FE4F2D; font-weight: 500;"  class="bg-[#FE4F2D] w-full p-2 rounded-sm flex items-center gap-2 shadow-[0px 8px 20px #FE4F2D]">
            Restablecer contraseña 
            <span class="material-symbols-rounded bg-transparent">bolt</span>
        </button>
        <br>
    </a>

    <p>Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>
    <p>Este enlace expirará en 15 minutos por razones de seguridad.</p>

    
    <p style="margin-top: 30px; text-align: left;">Gracias, Equipo Fixnology Co.</p>
</div>

    

    

</body>

</html>