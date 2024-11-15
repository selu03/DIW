<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: 
            radial-gradient(circle at 40% 60%, green,rgb(49, 48, 48)); display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-attachment: fixed;
            text-align: center;
            color: white;
        }
        .container {
            background: 
            linear-gradient(to bottom right, rgb(30, 143, 30), rgba(136, 167, 127, 0.852)); 
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            color: #333;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: #fff;
            background:  
    radial-gradient(circle at 40% 60%,rgb(49, 48, 48),green);
       border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background:  
            radial-gradient(circle at 40% 60%,green,rgb(49, 48, 48));
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php
        echo "<h2>Bienvenido, Administrador " . htmlspecialchars($_SESSION['usuario_nick']) . "</h2>";
        ?>
        <a href="gestionar_usuarios.php">Gestionar Usuarios</a><br>
        <a href="cerrar_sesion.php">Cerrar Sesión</a>
    </div>
</body>
</html>
