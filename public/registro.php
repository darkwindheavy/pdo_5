<?php
session_start();
require_once '../includes/funciones.php'; // Importar funciones reutilizables
require_once '../includes/BaseDeDatos.php'; // Importar la clase BaseDeDatos
require_once '../includes/Usuario.php'; // Importar la clase Usuario

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Recibir y sanitizar los valores del formulario
        $dni = trim(strtoupper($_POST['dni']));
        $nombre = trim(htmlspecialchars($_POST['nombre']));
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim(htmlspecialchars($_POST['direccion']));
        $localidad = trim(htmlspecialchars($_POST['localidad']));
        $provincia = trim(htmlspecialchars($_POST['provincia']));
        $contrasena = trim($_POST['contrasena']);

        // Crear el usuario utilizando la clase Usuario
        $usuario->crearUsuario($dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, 'usuario', $contrasena);
        
        // Mostrar mensaje de éxito y redirigir al login
        echo '<script>
                alert("Registro exitoso. Serás redirigido a la página de inicio de sesión.");
                window.location.href = "login.php";
            </script>';
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Registro de Usuario</title>
</head>
<body>
    <header>
        <h1>Registro de Usuario</h1>
        <nav class="navbar">
            <ul>
                <li><a href="index.php" class="nav-button">Inicio</a></li>
                <!-- Ocultamos el enlace de cerrar sesión, ya que el usuario no está registrado -->
            </ul>
        </nav>
    </header>

    <div class="edit-container">
        <h2>Regístrate</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="registro.php" method="post">
            <div>
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required>
            </div>
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono">
            </div>
            <div>
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion">
            </div>
            <div>
                <label for="localidad">Localidad:</label>
                <input type="text" id="localidad" name="localidad">
            </div>
            <div>
                <label for="provincia">Provincia:</label>
                <input type="text" id="provincia" name="provincia">
            </div>
            <div>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit" class="btn-primary">Registrarse</button>
        </form>

        <div class="login-link">
            <p>¿Ya tienes una cuenta? <a href="login.php" class="btn-secondary">Inicia sesión aquí</a></p>
        </div>
    </div>

    <?php
    require_once '../includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>


