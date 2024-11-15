<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_POST['user_ids']) && isset($_POST['accion'])) {
    $user_ids = implode(',', array_map('intval', $_POST['user_ids']));
    $accion = $_POST['accion'];

    if ($accion == 'bloquear') {
        $estado = 1;
        $fecha_bloqueo = date("Y-m-d");
        $restablecer_intentos = ""; 

        $sql = "UPDATE usuarios SET Usuario_bloqueado = ?, Usuario_fecha_bloqueo = ? $restablecer_intentos WHERE Usuario_id IN ($user_ids)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $estado, $fecha_bloqueo);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion == 'desbloquear') {
        $estado = 0;
        $fecha_bloqueo = NULL;
        $restablecer_intentos = ", Usuario_numero_intentos = 0";

        $sql = "UPDATE usuarios SET Usuario_bloqueado = ?, Usuario_fecha_bloqueo = NULL $restablecer_intentos WHERE Usuario_id IN ($user_ids)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $estado);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion == 'eliminar') {
        
        $deleteSql = "DELETE FROM usuarios WHERE Usuario_id IN ($user_ids)";
        if ($conn->query($deleteSql) === TRUE) {
            echo "Usuarios eliminados exitosamente.";
        } else {
            echo "Error al eliminar usuarios: " . $conn->error;
        }
    }
}

$conn->close();
header("Location: gestionar_usuarios.php");
exit();
?>
