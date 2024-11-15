<?php
// Recibir el token desde la URL
$token = $_GET['token'] ?? '';

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar que el token sea válido
$sql = "SELECT Usuario_id, Usuario_fecha_bloqueo FROM usuarios WHERE Usuario_token_aleatorio = '$token'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fechaBloqueo = $user['Usuario_fecha_bloqueo'];
    
    // Comprobar que el token no ha expirado
    if (new DateTime() > new DateTime($fechaBloqueo)) {
        die("El enlace de restablecimiento ha caducado.");
    }
} else {
    die("El enlace de restablecimiento es inválido o ha caducado.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
</head>
<body>
    <h2>Restablecer Contraseña</h2>
    <form action="actualizar_contraseña.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="nueva_contraseña">Nueva Contraseña:</label>
        <input type="password" name="nueva_contraseña" id="nueva_contraseña" required>
        <button type="submit">Actualizar Contraseña</button>
    </form>
</body>
</html>
