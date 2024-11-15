<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

$conn = new mysqli($servername, $username, $password, $dbname);

if (isset($_POST['token']) && isset($_POST['nueva_contraseña'])) {
    $token = $conn->real_escape_string($_POST['token']);
    $nueva_contraseña = md5($_POST['nueva_contraseña']); // Hashea la contraseña

    // Validar el token
    $sql = "SELECT Usuario_id FROM usuarios WHERE Usuario_token_aleatorio = '$token'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Actualizar la contraseña
        $sql = "UPDATE usuarios SET Usuario_clave = '$nueva_contraseña', Usuario_token_aleatorio = NULL, Usuario_fecha_bloqueo = NULL WHERE Usuario_token_aleatorio = '$token'";
        $conn->query($sql);

        echo "Tu contraseña ha sido actualizada con éxito.";
    } else {
        echo "El enlace de restablecimiento es inválido o ha caducado.";
    }
}

$conn->close();
?>
