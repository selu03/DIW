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

if (isset($_POST['confirmar']) && isset($_POST['id'])) {
    $usuario_id = intval($_POST['id']);

    $deleteSql = "DELETE FROM usuarios WHERE Usuario_id = $usuario_id";

    if ($conn->query($deleteSql) === TRUE) {
        header("Location: gestionar_usuarios.php?status=deleted"); 
        exit(); // Redirigir y detener la ejecución.
    } else {
        echo "Error al eliminar el usuario: " . $conn->error;
    }

} else if (isset($_GET['id'])) {
    $usuario_id = intval($_GET['id']);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmar Eliminación</title>
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background: radial-gradient(circle at 40% 60%, green, rgb(49, 48, 48));
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                color: white;
            }
            .container {
                background: linear-gradient(to bottom right, rgb(30, 143, 30), rgba(136, 167, 127, 0.852));
                padding: 40px;
                border-radius: 10px;
                text-align: center;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            }
            h2 {
                margin-bottom: 20px;
                color: rgb(7, 57, 7);
            }
            p {
                color: rgb(7, 57, 7);
                font-size: 1.2rem;
                margin-bottom: 30px;
            }
            button, a {
                padding: 10px 20px;
                background-color: #fff;
                color: rgb(7, 57, 7);    
                border: none;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                margin: 5px;
                cursor: pointer;
            }
            button:hover, a:hover {
                background-color: #f0f0f0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar este usuario?</p>
            <form method="post" action="eliminar_usuario.php">
                <input type="hidden" name="id" value="<?php echo $usuario_id; ?>">
                <button type="submit" name="confirmar" value="si">Sí, eliminar</button>
                <a href="gestionar_usuarios.php">No, cancelar</a>
            </form>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "ID de usuario no especificado.";
}

$conn->close(); 
?>
