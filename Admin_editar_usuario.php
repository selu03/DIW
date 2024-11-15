<?php
session_start();

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
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

if (isset($_GET['usuario_id'])) {
    $usuario_id = intval($_GET['usuario_id']);

    $sql = "SELECT Usuario_nombre, Usuario_apellido1, Usuario_apellido2, Usuario_nick, Usuario_email, 
            Usuario_nif, Usuario_numero_telefono, Usuario_domicilio, Usuario_poblacion, Usuario_provincia,
            Usuario_comunidad_autonoma 
            FROM usuarios WHERE Usuario_id = $usuario_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    } else {
        die("Usuario no encontrado.");
    }
} else {
    die("ID de usuario no especificado.");
}

// Obtener lista de municipios para el select
$municipiosSql = "SELECT Municipio FROM municipios";
$municipiosResult = $conn->query($municipiosSql);
$municipios = [];
if ($municipiosResult->num_rows > 0) {
    while ($row = $municipiosResult->fetch_assoc()) {
        $municipios[] = $row['Municipio'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido1 = $conn->real_escape_string($_POST['apellido1']);
    $apellido2 = $conn->real_escape_string($_POST['apellido2']);
    $nick = $conn->real_escape_string($_POST['nick']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $domicilio = $conn->real_escape_string($_POST['domicilio']);
    $poblacion = $conn->real_escape_string($_POST['poblacion']);

    // Obtener la idProvincia y la idCCAA de la población
    $provinciaSql = "SELECT p.idProvincia, p.Provincia, c.idCCAA, c.Nombre 
                     FROM provincias p
                     JOIN municipios m ON p.idProvincia = m.idProvincia
                     JOIN ccaa c ON p.idCCAA = c.idCCAA
                     WHERE m.Municipio = '$poblacion'";

    $provinciaResult = $conn->query($provinciaSql);
    
    if ($provinciaResult->num_rows > 0) {
        $provinciaData = $provinciaResult->fetch_assoc();
        $usuario_provincia = $provinciaData['Provincia']; // Nombre de la provincia
        $usuario_comunidad_autonoma = $provinciaData['Nombre']; // Nombre de la CCAA
    } else {
        die("La población no está registrada en la base de datos.");
    }

    $updateSql = "UPDATE usuarios SET 
                    Usuario_nombre = '$nombre', 
                    Usuario_apellido1 = '$apellido1', 
                    Usuario_apellido2 = '$apellido2', 
                    Usuario_nick = '$nick', 
                    Usuario_numero_telefono = '$telefono',
                    Usuario_domicilio = '$domicilio',
                    Usuario_poblacion = '$poblacion',
                    Usuario_provincia = '$usuario_provincia',
                    Usuario_comunidad_autonoma = '$usuario_comunidad_autonoma'
                  WHERE Usuario_id = $usuario_id";

    if ($conn->query($updateSql) === TRUE) {
        echo "Datos actualizados correctamente.";
        header("Location: gestionar_usuarios.php");
        exit();
    } else {
        echo "Error al actualizar los datos: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 40% 60%, green, rgb(49, 48, 48));
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-attachment: fixed;
            color: white;
        }
        .container {
            background: linear-gradient(to top right, rgb(30, 143, 30), silver);
            padding: 40px;
            border-radius: 120px;
            box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            color: #333;
            text-align: left;
        }
        h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="tel"], select {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
        }
        .salir {
            text-align: center; 
            margin-top: 20px; 
        }
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: radial-gradient(circle at 40% 60%, rgb(49, 48, 48), green);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background: radial-gradient(circle at 40% 60%, green, rgb(49, 48, 48));
        }
        p {
            text-align: center;
            margin-top: 20px;
        }
        a {
            color: rgb(7, 57, 7);
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Editar Usuario</h1>
        <form action="Admin_editar_usuario.php?usuario_id=<?php echo $usuario_id; ?>" method="POST">
            <input type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($usuario['Usuario_nombre']); ?>" required>
            <input type="text" name="apellido1" placeholder="Primer Apellido" value="<?php echo htmlspecialchars($usuario['Usuario_apellido1']); ?>" required>
            <input type="text" name="apellido2" placeholder="Segundo Apellido" value="<?php echo htmlspecialchars($usuario['Usuario_apellido2']); ?>" required>
            <input type="text" name="nick" placeholder="Nombre de Usuario" value="<?php echo htmlspecialchars($usuario['Usuario_nick']); ?>" required>
            <input type="text" name="email" placeholder="Correo Electrónico" value="<?php echo htmlspecialchars($usuario['Usuario_email']); ?>">
            <input type="text" name="nif" placeholder="DNI" value="<?php echo htmlspecialchars($usuario['Usuario_nif']); ?>">
            <input type="text" name="telefono" placeholder="Número de Teléfono" value="<?php echo htmlspecialchars($usuario['Usuario_numero_telefono']); ?>">
            <input type="text" name="domicilio" placeholder="Domicilio" value="<?php echo htmlspecialchars($usuario['Usuario_domicilio']); ?>">
            
            <select name="poblacion" required>
                <option value="" disabled>Selecciona la Población</option>
                <?php foreach ($municipios as $municipio): ?>
                    <option value="<?php echo htmlspecialchars($municipio); ?>" <?php if ($municipio == $usuario['Usuario_poblacion']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($municipio); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Actualizar">
        </form>
    </div>
</body>
</html>
