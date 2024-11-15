<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
</head>
<body>
    <h2>Recuperar Contraseña</h2>
    <form action="procesar_recuperacion.php" method="post">
        <label for="email">Ingresa tu email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Enviar enlace de recuperación</button>
    </form>
</body>
</html>
