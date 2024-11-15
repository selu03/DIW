<?php
session_start(); 
if (!isset($_SESSION['usuario_nick'])) {
    header("Location: login.php"); 
    exit();
}

if (!isset($_GET['usuario_id'])) {
    die("ID de usuario no especificado."); 
}

$usuario_id = intval($_GET['usuario_id']); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$sql = "SELECT Usuario_nombre, Usuario_apellido1, Usuario_apellido2, Usuario_nick, Usuario_email, Usuario_numero_telefono, Usuario_nif FROM usuarios WHERE Usuario_id = $usuario_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['Usuario_nombre'];
    $apellido1 = $row['Usuario_apellido1'];
    $apellido2 = $row['Usuario_apellido2'];
    $nick = $row['Usuario_nick'];
    $email = $row['Usuario_email'];
    $telefono = $row['Usuario_numero_telefono'];
    $nif = $row['Usuario_nif'];
} else {
    echo "Usuario no encontrado.";
    exit();
}

$conn->close();     
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Datos del Usuario</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: 
            radial-gradient(circle at 40% 60%, green,rgb(49, 48, 48)); display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }
        .container {
            background: 
            linear-gradient(to top , rgb(30, 143, 30), rgba(136, 167, 127, 0.852)); 
       padding: 40px; padding: 40px;
            border-radius: 100px;
            box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
            color: #333;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 2rem;
        }
        input[type="text"], input[type="password"], input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 25px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background:  
            radial-gradient(circle at 40% 60%,rgb(49, 48, 48),green);
            border: none;
            color: white;
            border-radius: 15px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background:  
            radial-gradient(circle at 40% 60%,green,rgb(49, 48, 48));
        }
        a {
            color:rgb(7, 57, 7);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Datos</h2>
        <form action="actualizar_datos.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">

            <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo $nombre; ?>" required>

            <input type="text" id="apellido1" name="apellido1" placeholder="Primer Apellido" value="<?php echo $apellido1; ?>" required>

            <input type="text" id="apellido2" name="apellido2" placeholder="Segundo Apellido" value="<?php echo $apellido2; ?>" required>

            <input type="text" id="nick" name="nick" placeholder="Nombre de Usuario" value="<?php echo $nick; ?>" required>

            <input type="password" id="clave_anterior" name="clave_anterior" placeholder="Contraseña anterior" required>

            <input type="password" id="clave" na    me="clave" placeholder="Nueva Contraseña">

            <input type="text" id="nif" name="nif" value="<?php echo $nif; ?>" disabled>

            <input type="tel" id="telefono" name="telefono" placeholder="Número de Teléfono" value="<?php echo $telefono; ?>" required>

            <input type="submit" value="Actualizar">
            <p><b><a href="Restablecer_Contraseña.php">¿Olvidaste tu contraseña?</a></b></p>
            <p><b><a href="ver_perfil.php">Salir</a></b></p>
        </form>
    </div>
</body>
</html>
