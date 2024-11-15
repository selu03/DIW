<?php
session_start();

if (!isset($_SESSION['usuario_nick'])) {
    header("Location: login.php"); 
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = intval($_POST['usuario_id']);
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $nick = $_POST['nick'];
    $clave_anterior = $_POST['clave_anterior'];
    $clave = isset($_POST['clave']) ? md5($_POST['clave']) : null; 
    $telefono = $_POST['telefono'];

    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "usuarios";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $nick_usuario = $_SESSION['usuario_nick'];
    $sql = "SELECT Usuario_clave FROM usuarios WHERE Usuario_nick = '$nick_usuario' OR Usuario_email = '$nick_usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clave_encriptada = $row['Usuario_clave'];

        if (md5($clave_anterior) !== $clave_encriptada) {
            echo "Contraseña anterior incorrecta.";
            exit();
        }
    } else {
        echo "Usuario no encontrado.";
        exit();
    }

    $sql_update = "UPDATE usuarios SET 
        Usuario_nombre = '$nombre',
        Usuario_apellido1 = '$apellido1',
        Usuario_apellido2 = '$apellido2',
        Usuario_nick = '$nick',
        Usuario_numero_telefono = '$telefono'";

    if ($clave) {
        $sql_update .= ", Usuario_clave = '$clave'";
    }

    $sql_update .= " WHERE Usuario_id = $usuario_id";

    if ($conn->query($sql_update) === TRUE) {
        echo "Datos actualizados exitosamente.";
        header("Location: VistaUsuarios.php"); 
        exit();
    } else {
        echo "Error al actualizar los datos: " . $conn->error;
    }

    $conn->close(); 
} else {
    echo "Método de solicitud no permitido.";
}
?>
