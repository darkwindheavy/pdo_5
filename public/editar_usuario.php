<?php
session_start();
require_once '../includes/funciones.php'; // Importar funciones reutilizables
require_once '../includes/header.php'; // Importar la cabecera común
require_once '../includes/BaseDeDatos.php'; // Importar la clase BaseDeDatos
require_once '../includes/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión
verificar_sesion_y_rol(['usuario', 'editor']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

// Obtener el ID del usuario que va a ser editado (el ID del usuario que inició sesión)
$usuario_id = $_SESSION['usuario_id'];

// Obtener los datos del usuario
try {
    $datos_usuario = $usuario->obtenerUsuarioPorId($usuario_id);

    if (!$datos_usuario) {
        $error = "No se encontró la información del usuario.";
    }
} catch (Exception $e) {
    manejar_error("Error al obtener los datos del usuario: " . $e->getMessage());
}

// Manejar la solicitud POST para actualizar o eliminar los datos del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {
    if (isset($_POST['eliminar'])) {
        // Si el botón de eliminar fue presionado
        try {
            // Eliminar el usuario usando el método de la clase Usuario
            $usuario->eliminarUsuario($usuario_id);
            
            // Mostrar mensaje de éxito antes de destruir la sesión y redirigir
            echo '<script>
                    alert("Cuenta eliminada exitosamente. Serás redirigido al inicio de sesión.");
                  </script>';
            
            // Cerrar sesión después de eliminar la cuenta
            session_destroy();
            
            // Redirigir a la página principal o de inicio después de mostrar el mensaje
            echo '<script>
                    window.location.href = "index.php";
                </script>';
            exit;
        } catch (Exception $e) {
            $error = "Error al eliminar la cuenta: " . $e->getMessage();
        }
    }
    else {
        // Si se trata de una actualización
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $direccion = trim($_POST['direccion']);
        $localidad = trim($_POST['localidad']);
        $provincia = trim($_POST['provincia']);
        $telefono = trim($_POST['telefono']);
        $contrasena = isset($_POST['contrasena']) && !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;

        try {
            // Editar el usuario
            $usuario->editarUsuario($usuario_id, $dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, 'usuario', $contrasena);
            $exito = "Datos del usuario actualizados exitosamente.";

            // Volver a obtener los datos del usuario actualizados
            $datos_usuario = $usuario->obtenerUsuarioPorId($usuario_id);

            // Redirigir a la zona privada despues de editar los datos
        header("Location: zona_privada.php?mensaje=" . urlencode($exito));
        exit;

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="edit-container">
        <h2>Editar Mis Datos</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <?php if (!empty($datos_usuario)): ?>
            <form action="editar_usuario.php" method="post">
            <div class="form-group">
                    <label for="dni">Dni:</label>
                    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($datos_usuario['dni']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos_usuario['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($datos_usuario['correo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($datos_usuario['direccion']); ?>" >
                </div>
                <div class="form-group">
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($datos_usuario['localidad']); ?>" >
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <input type="text" id="provincia" name="provincia" value="<?php echo htmlspecialchars($datos_usuario['provincia']); ?>" >
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datos_usuario['telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="contrasena" name="contrasena">
                </div>
                <button type="submit" class="btn-primary">Actualizar Datos</button>
                <button type="submit" name="eliminar" onclick="return confirm('¿Está seguro de que desea eliminar su cuenta?');">Eliminar Cuenta</button>
            </form>
        <?php endif; ?>

        <a href="zona_privada.php" class="btn-secondary">Volver a la Zona Privada</a>
    </div>
    <?php
    require_once '../includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>
