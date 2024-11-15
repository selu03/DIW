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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['poblacion'])) {
        $poblacion = $_POST['poblacion'];
    } else {
        echo "La población no ha sido seleccionada.";
        exit(); 
    }

    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $nick = $_POST['nick'];
    $clave = md5($_POST['clave']); 
    $email = $_POST['email'];
    $nif = $_POST['nif'];
    $telefono = $_POST['telefono'];

    $sql = "SELECT p.Provincia, c.Nombre as Comunidad 
            FROM Municipios m
            JOIN Provincias p ON m.idProvincia = p.idProvincia
            JOIN CCAA c ON p.idCCAA = c.idCCAA
            WHERE m.Municipio = '$poblacion'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usuario_provincia = $row['Provincia'];
        $usuario_comunidad = $row['Comunidad'];

        $sql_insert = "INSERT INTO usuarios 
                       (Usuario_nombre, Usuario_apellido1, Usuario_apellido2, Usuario_nick, Usuario_clave, Usuario_email, Usuario_fecha_alta, Usuario_nif, Usuario_numero_telefono, Usuario_poblacion, Usuario_provincia, Usuario_comunidad_autonoma) 
                       VALUES 
                       ('$nombre', '$apellido1', '$apellido2', '$nick', '$clave', '$email', NOW(), '$nif', '$telefono', '$poblacion', '$usuario_provincia', '$usuario_comunidad')";

        if ($conn->query($sql_insert) === TRUE) {
            header("Location: registro_exitoso.html");
            exit(); 
        } else {
            echo "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    } else {
        echo "Población no encontrada.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title><style>
        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 40% 60%, green,rgb(49, 48, 48));
            color: #333; display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-attachment: fixed;
        }
        .container {
            background: linear-gradient(to top right, rgb(30, 143, 30), rgba(136, 167, 127, 0.852));   
            padding: 30px;
            border-radius: 100px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            margin-bottom: 0px;
            font-size: 2.5rem;
            color: rgb(7, 57, 7);
        }
        input[type="text"], input[type="password"], input[type="email"], select {
            width: 100%;
            padding: 7px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus, select:focus {
            border-color: #6d5dfc;
            outline: none;
        }
        input[type="submit"] {
            margin-top: 20px;
            padding: 15px 30px;
            width: 100%;
            font-size: 1.2rem;
            font-weight: bold;
            background: radial-gradient(circle at 40% 60%,rgb(49, 48, 48),green);
            border: none;
            border-radius: 50px;
            color: rgba(255, 255, 255, 0.916);
            cursor: pointer;
            transition: background 0.3s ease;
        }
        input[type="submit"]:hover {
            background: radial-gradient(circle at center,green, rgb(47, 46, 46) );
        }
        a {
            display: block;
            margin-top: 15px;
            color:rgb(7, 57, 7);
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: white;
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script>
        function validarFormulario(event) {
            event.preventDefault();
            const campos = [
                { id: "nombre", regex: /^[a-zA-Z\s]{2,}$/, mensaje: "Nombre inválido. Debe tener mínimo 2 caracteres." },
                { id: "apellido1", regex: /^[a-zA-Z\s]{2,}$/, mensaje: "Primer apellido inválido.Debe tener mínimo 2 caracteres" },
                { id: "apellido2", regex: /^[a-zA-Z\s]{2,}$/, mensaje: "Segundo apellido inválido.Debe tener mínimo 2  caracteres" },
                { id: "nick", regex: /^[a-zA-Z0-9_]{3,15}$/, mensaje: "El nombre de usuario debe tener entre 3 y 15 caracteres." },
                { id: "clave", regex: /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/, mensaje: "La contraseña debe tener al menos una mayúscula, una minúscula y un número." },
                { id: "email", regex: /^[\w\.\-]+@[a-zA-Z\d\.\-]+\.[a-zA-Z]{2,4}$/, mensaje: "Correo electrónico no válido." },
                { id: "nif", regex: /^[XYZ]?\d{5,8}[A-Z]$/, mensaje: "DNI inválido." },
                { id: "telefono", regex: /^[6-9]\d{8}$/, mensaje: "Teléfono no válido. Debe comenzar con 6, 7, 8 o 9 y tener 9 dígitos." }
            ];

            for (let campo of campos) {
                const valor = document.getElementById(campo.id).value;
                if (!campo.regex.test(valor)) {
                    mostrarError(campo.mensaje, campo.id);
                    return;
                }
            }
            document.forms["formRegistro"].submit();
        }

        function mostrarError(mensaje, idCampo) {
            const mensajeError = document.getElementById("mensajeError");
            mensajeError.innerHTML = mensaje;
            document.getElementById(idCampo).focus();
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Formulario de Registro</h2>
        <form name="formRegistro" action="registro_usuarios.php" method="POST" onsubmit="validarFormulario(event)">
            <input type="text" id="nombre" name="nombre" placeholder="Nombre">
            <input type="text" id="apellido1" name="apellido1" placeholder="Primer Apellido">
            <input type="text" id="apellido2" name="apellido2" placeholder="Segundo Apellido">
            <input type="text" id="nick" name="nick" placeholder="Nombre de Usuario">
            <input type="password" id="clave" name="clave" placeholder="Contraseña">
            <input type="email" id="email" name="email" placeholder="Correo Electrónico">
            <input type="text" id="nif" name="nif" placeholder="DNI">
            <input type="text" id="telefono" name="telefono" placeholder="Número de Teléfono">
            
            <select id="poblacion" name="poblacion">
                <option value="">Selecciona una Población</option>
                <?php
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }
                $sql_municipios = "SELECT Municipio FROM Municipios";
                $result_municipios = $conn->query($sql_municipios);
                if ($result_municipios->num_rows > 0) {
                    while ($row = $result_municipios->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['Municipio']) . '">' . htmlspecialchars($row['Municipio']) . '</option>';
                    }
                } else {
                    echo '<option value="">No hay poblaciones disponibles</option>';
                }
                $conn->close();
                ?>
            </select>

            <input type="submit" value="Registrarse">
            <a href="login.html">¿Ya estás registrado? Inicia Sesión</a>
            <div id="mensajeError" class="error"></div>
        </form>
    </div>
</body>
</html>
