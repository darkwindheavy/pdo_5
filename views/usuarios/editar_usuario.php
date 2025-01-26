<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones reutilizables
require_once '../app/Controladores/config.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "index.php?page=auth/login");
    exit;
}

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

// Obtener el ID del usuario que va a ser editado
$usuario_id = $_SESSION['usuario_id'];

try {
    // Obtener los datos del usuario
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
        try {
            $usuario->eliminarUsuario($usuario_id);
            session_destroy();

            // Mensaje de éxito y redirección al login
            echo '<script>
                    alert("Cuenta eliminada exitosamente. Serás redirigido al inicio de sesión.");
                    window.<?php echo BASE_URL; ?>index.php?page=auth/login";
                  </script>';
            exit;
        } catch (Exception $e) {
            $error = "Error al eliminar la cuenta: " . $e->getMessage();
        }
    } else {
        // Si se trata de una actualización
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $direccion = trim($_POST['direccion']);
        $localidad = trim($_POST['localidad']);
        $provincia = trim($_POST['provincia']);
        $telefono = trim($_POST['telefono']);
        $contrasena = !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;

        try {
            $usuario->editarUsuario($usuario_id, $dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, 'usuario', $contrasena);
            $exito = "Datos del usuario actualizados exitosamente.";

            // Redirigir a la zona privada
            header("Location: " . BASE_URL . "index.php?page=misc/zona_privada&mensaje=" . urlencode($exito));
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/styles.css">
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
            <form action="<?php echo BASE_URL; ?>index.php?page=usuarios/editar_usuario" method="post">
                <div class="form-group">
                    <label for="dni">DNI:</label>
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
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($datos_usuario['direccion']); ?>">
                </div>
                <div class="form-group">
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($datos_usuario['localidad']); ?>">
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <input type="text" id="provincia" name="provincia" value="<?php echo htmlspecialchars($datos_usuario['provincia']); ?>">
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
                <button type="submit" name="eliminar" onclick="return confirm('¿Está seguro de que desea eliminar su cuenta?');" class="btn-delete">Eliminar Cuenta</button>
            </form>
        <?php endif; ?>

        <a href="<?php echo BASE_URL; ?>index.php?page=misc/zona_privada" class="btn-secondary">Volver a la Zona Privada</a>
    </div>
    <?php require_once '../views/includes/footer.php'; ?>
</body>
</html>
