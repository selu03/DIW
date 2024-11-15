<?php
session_start(); 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nick = $_POST['nick'];
$clave = $_POST['clave'];

$nick = $conn->real_escape_string($nick);
$clave = md5($conn->real_escape_string($clave));

$sql = "SELECT Usuario_id, Usuario_clave, Usuario_rol, Usuario_bloqueado, Usuario_numero_intentos FROM usuarios WHERE Usuario_nick = '$nick'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $clave_encriptada = $row['Usuario_clave'];
    $rol = $row['Usuario_rol']; 
    $usuario_id = $row['Usuario_id'];
    $usuario_bloqueado = $row['Usuario_bloqueado'];
    $numero_intentos = $row['Usuario_numero_intentos'];

    // Verificar si el usuario está bloqueado
    if ($usuario_bloqueado == 1) {
        echo "<script type='text/javascript'>
                alert('Tu cuenta ha sido bloqueada. No puedes iniciar sesión.');
                window.location.href = 'login.html';
              </script>";
    } else {
        if ($clave === $clave_encriptada) {
            // Restablecer intentos a 0 si el login es correcto
            $update_sql = "UPDATE usuarios SET Usuario_numero_intentos = 0, Usuario_fecha_ultima_conexion = NOW() WHERE Usuario_id = '$usuario_id'";
            $conn->query($update_sql);

            $_SESSION['usuario_nick'] = $nick;
            $_SESSION['usuario_rol'] = $rol;

            if ($rol === 'admin') {
                header("Location: VistaAdmin.php"); 
            } else {
                header("Location: VistaUsuarios.php");
            }
            exit();
        } else {
            // Incrementar el contador de intentos fallidos
            $numero_intentos++;

            // Mensaje personalizado dependiendo de los intentos fallidos
            if ($numero_intentos >= 3) {
                // Bloquear al usuario si supera los 3 intentos
                $update_sql = "UPDATE usuarios SET Usuario_bloqueado = 1 WHERE Usuario_id = '$usuario_id'";
                $conn->query($update_sql);
                echo "<script type='text/javascript'>
                        alert('Tu cuenta ha sido bloqueada debido a demasiados intentos fallidos.');
                        window.location.href = 'login.html';
                      </script>";
            } else {
                // Actualizar el número de intentos en la base de datos
                $update_sql = "UPDATE usuarios SET Usuario_numero_intentos = $numero_intentos WHERE Usuario_id = '$usuario_id'";
                $conn->query($update_sql);

                // Mostrar los intentos restantes
                $intentos_restantes = 3 - $numero_intentos;
                echo "<script type='text/javascript'>
                        alert('Contraseña incorrecta. Intento fallido $numero_intentos de 3. Te quedan $intentos_restantes intento(s).');
                        window.location.href = 'login.html';
                      </script>";
            }
        }
    }
} else {
    // Si el usuario no existe, mostrar un mensaje
    echo "<script type='text/javascript'>
            alert('Usuario no encontrado');
            window.location.href = 'login.html';
          </script>";
}

$conn->close();
?>
