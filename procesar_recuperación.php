<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);

    // Verificar que el email esté registrado
    $sql = "SELECT Usuario_id FROM usuarios WHERE Usuario_email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generar token único
        $token = bin2hex(random_bytes(16));
        $expira = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Guardar token en la base de datos
        $sql = "UPDATE usuarios SET Usuario_token_aleatorio = '$token', Usuario_fecha_bloqueo = '$expira' WHERE Usuario_email = '$email'";
        $conn->query($sql);

        // Enviar enlace de restablecimiento
        $enlace = "http://localhost/restablecer_contraseña.php?token=$token";
        $mensaje = "Haz clic en el siguiente enlace para restablecer tu contraseña: $enlace";
        mail($email, "Recuperación de contraseña", $mensaje, "From: no-reply@tudominio.com");

        echo "Revisa tu correo electrónico para restablecer tu contraseña.";
    } else {
        echo "El email no está registrado.";
    }
}

$conn->close();
?>
