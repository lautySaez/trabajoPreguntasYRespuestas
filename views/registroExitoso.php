<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/home.css">

<div class="home-container" style="text-align:center; padding:60px 20px;">
    <div class="welcome-section" style="max-width:600px; margin:0 auto; background:#ffffffd8; padding:40px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.1); color:#000;">
        <h1 style="color:#2c3e50; margin-bottom:20px;">¡Registro Exitoso!</h1>
        <p style="font-size:1.2em; color:#34495e;">Tu cuenta fue creada correctamente.</p>
        <p style="font-size:1.1em; margin-bottom:30px;">Revisá el código verificador en tu email para poder iniciar sesión y comenzar a jugar.</p>

        <a href="index.php?controller=LoginController&method=inicioSesion" 
           class="boton-partida"
           style="text-decoration:none; background-color:#4CAF50; color:white; padding:12px 25px; border-radius:8px; font-weight:bold; transition:0.3s;">
           Ir a Iniciar Sesión
        </a>
    </div>
</div>

<?php include("views/partials/footer.php"); ?>