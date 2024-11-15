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

$provinciaSeleccionada = ""; 
$condicionProvincia = "";

if (isset($_GET['provincia']) && $_GET['provincia'] != '') {
    $provinciaSeleccionada = $conn->real_escape_string($_GET['provincia']);
    $condicionProvincia = " WHERE Usuario_provincia = '$provinciaSeleccionada'";
}

$provinciasSql = "SELECT DISTINCT Usuario_provincia FROM usuarios";
$provinciasResult = $conn->query($provinciasSql);
$provincias = [];
if ($provinciasResult->num_rows > 0) {
    while ($row = $provinciasResult->fetch_assoc()) {
        $provincias[] = $row['Usuario_provincia'];
    }
}

$usuarios_por_pagina = isset($_GET['usuarios_por_pagina']) ? (int)$_GET['usuarios_por_pagina'] : 3;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $usuarios_por_pagina;

$sql_total = "SELECT COUNT(*) as total FROM usuarios" . $condicionProvincia;
$result_total = $conn->query($sql_total);
$total_usuarios = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_usuarios / $usuarios_por_pagina);

$sql = "SELECT Usuario_id, CONCAT(Usuario_nombre, ' ', Usuario_apellido1, ' ', Usuario_apellido2) AS nombre_completo, Usuario_nick, Usuario_email, Usuario_nif, Usuario_provincia, Usuario_numero_telefono, Usuario_fecha_ultima_conexion, Usuario_rol, Usuario_bloqueado 
        FROM usuarios" . $condicionProvincia . 
       " LIMIT $offset, $usuarios_por_pagina";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos de la página */
        body { font-family: 'Poppins', sans-serif; background: radial-gradient(circle at 40% 60%, green, rgb(49, 48, 48)); color: #333; text-align: center; }
        h1 { color: white; }
        table { width: 100%; margin: 20px 0; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; text-align: center; }
        th { background-color: rgb(7, 57, 7); color: white; }
        tr:hover { background-color: #f1f1f1; }
        a { color: rgb(7, 57, 7); text-decoration: none; font-weight: bold; transition: color 0.3s; }
        a:hover { color: #5a4dcf; }
        #Salir { color: white; font-weight: bold; }
        select { margin-bottom: 20px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .pagination { margin-top: 30px; }
        .pagination a { color: white; padding: 8px 16px; background-color: rgb(7, 57, 7); margin: 0 5px; border-radius: 15px; text-decoration: none; }
        .pagination a:hover { background-color: rgb(30, 100, 30); }
    </style>
    <script>
        function toggleSelectAll(source) {
            checkboxes = document.getElementsByName('user_ids[]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<body>
    <h1>Gestión de Usuarios</h1>
    
    <!-- Filtro de provincia -->
    <form method="GET" action="">
        <select name="provincia" onchange="this.form.submit()">
            <option value="">Todas las Provincias</option>
            <?php foreach ($provincias as $provincia): ?>
                <option value="<?php echo htmlspecialchars($provincia); ?>" 
                <?php if ($provinciaSeleccionada === $provincia) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($provincia); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="usuarios_por_pagina" onchange="this.form.submit()">
            <option value="3" <?php echo ($usuarios_por_pagina == 3) ? 'selected' : ''; ?>>3 Usuarios</option>
            <option value="5" <?php echo ($usuarios_por_pagina == 5) ? 'selected' : ''; ?>>5 Usuarios</option>
            <option value="10" <?php echo ($usuarios_por_pagina == 10) ? 'selected' : ''; ?>>10 Usuarios</option>
        </select>
    </form>

    <form method="POST" action="actualizar_bloqueo.php">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" onClick="toggleSelectAll(this)"></th>
                    <th>Nombre Completo</th>
                    <th>Nick</th>
                    <th>Email</th>
                    <th>DNI</th>
                    <th>Provincia</th>
                    <th>Número de Teléfono</th>
                    <th>Última Conexión</th>
                    <th>Bloqueado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='user_ids[]' value='" . $row['Usuario_id'] . "'></td>";
                        echo "<td>" . $row['nombre_completo'] . "</td>";
                        echo "<td>" . $row['Usuario_nick'] . "</td>";
                        echo "<td>" . $row['Usuario_email'] . "</td>";
                        echo "<td>" . $row['Usuario_nif'] . "</td>";
                        echo "<td>" . $row['Usuario_provincia'] . "</td>";
                        echo "<td>" . $row['Usuario_numero_telefono'] . "</td>";
                        echo "<td>" . $row['Usuario_fecha_ultima_conexion'] . "</td>";
                        echo "<td>" . ($row['Usuario_bloqueado'] ? 'Sí' : 'No') . "</td>";
                        echo "<td><a href='Admin_editar_usuario.php?usuario_id=" . $row['Usuario_id'] . "'>Editar</a> | 
                                  <a href='eliminar_usuario.php?id=" . $row['Usuario_id'] . "'>Eliminar</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No se encontraron usuarios</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <div>
            <button type="submit" name="accion" value="bloquear">Bloquear seleccionados</button>
            <button type="submit" name="accion" value="desbloquear">Desbloquear seleccionados</button>
            <button type="submit" name="accion" value="eliminar">Eliminar seleccionados</button>
           </div>
    </form>

    <!-- Paginación -->
    <div class="pagination">
        <?php
        for ($i = 1; $i <= $total_paginas; $i++) {
            if ($i == $pagina_actual) {
                echo "<span style='padding:8px 16px; background-color: #ddd;'>$i</span>";
            } else {
                echo "<a href='gestionar_usuarios.php?pagina=$i&usuarios_por_pagina=$usuarios_por_pagina&provincia=$provinciaSeleccionada'>$i</a>";
            }
        }
        ?>
    </div>

    <div><a href="vistaAdmin.php" id="Salir">Cerrar sesión</a></div>

</body>
</html>

<?php
$conn->close();
?>
