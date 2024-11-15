<?php
session_start(); 

if (!isset($_SESSION['usuario_nick'])) {
    header("Location: login.php"); 
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nick = $_SESSION['usuario_nick'];

$sql = "SELECT Usuario_id, CONCAT(Usuario_nombre, ' ', Usuario_apellido1, ' ', Usuario_apellido2) AS nombre_completo, Usuario_nick, Usuario_email, Usuario_numero_telefono, Usuario_nif FROM usuarios WHERE Usuario_nick = '$nick' OR Usuario_email = '$nick'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $usuario_id = $row['Usuario_id'];
    $Nombre_Completo = $row['nombre_completo'];
    $nif = $row['Usuario_nif'];
    $email = $row['Usuario_email'];
    $telefono = $row['Usuario_numero_telefono'];
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
    <title>Perfil del Usuario</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: 
            radial-gradient(circle at 40% 60%, green,rgb(49, 48, 48));
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-attachment: fixed;
        }
        .container {
            background: 
            linear-gradient(to bottom right, rgb(30, 143, 30), rgba(136, 167, 127, 0.852));  padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            font-size: 1.2rem;
            margin: 10px 0;
        }
        a {
            display: block;
            margin-top: 20px;
            color:rgb(7, 57, 7);
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Perfil de Usuario</h2>
        <p><strong>Nombre Completo:</strong> <?php echo $Nombre_Completo; ?></p>
        <p><strong>Nombre de Usuario:</strong> <?php echo $nick; ?></p>
        <p><strong>Email:</strong> <?php echo $email; ?></p>
        <p><strong>Número de Teléfono:</strong> <?php echo $telefono; ?></p>
        <p><strong>DNI:</strong> <?php echo $nif; ?></p>
        <a href="editar_usuario.php?usuario_id=<?php echo $usuario_id; ?>">Editar Datos</a>
        <a href="VistaUsuarios.php">Salir</a>
    </div>
</body>
</html>
