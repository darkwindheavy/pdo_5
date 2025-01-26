<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones reutilizables
require_once '../app/Controladores/config.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión y si es editor
verificar_sesion_y_rol(['editor']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

// Obtener el ID del editor desde la sesión
$editor_id = $_SESSION['usuario_id'];

try {
    // Obtener los datos del editor
    $datos_editor = $usuario->obtenerUsuarioPorId($editor_id);

    if (!$datos_editor) {
        $error = "No se encontró la información del editor.";
    }

    // Manejar la solicitud POST para actualizar los datos del editor
    if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {
        // Recibir y sanitizar los valores del formulario
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $localidad = trim($_POST['localidad']);
        $provincia = trim($_POST['provincia']);
        $contrasena = isset($_POST['contrasena']) && !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;

        try {
            // Editar los datos del editor
            $usuario->editarUsuario($editor_id, $dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, 'editor', $contrasena);
            $exito = "Datos del editor actualizados exitosamente.";

            // Redirigir al formulario actual para mostrar mensaje de éxito
            header("Location: " . BASE_URL . "index.php?page=usuarios/editar_editor&mensaje=" . urlencode($exito));
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mis Datos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/styles.css">
</head>
<body>
    <div class="edit-container">
        <h2>Editar Mis Datos</h2>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="exito"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($datos_editor)): ?>
            <form action="<?php echo BASE_URL; ?>index.php?page=usuarios/editar_editor" method="post">
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($datos_editor['dni']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos_editor['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($datos_editor['correo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($datos_editor['direccion']); ?>">
                </div>
                <div class="form-group">
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($datos_editor['localidad']); ?>">
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <input type="text" id="provincia" name="provincia" value="<?php echo htmlspecialchars($datos_editor['provincia']); ?>">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datos_editor['telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="contrasena" name="contrasena">
                </div>
                <button type="submit" class="btn-primary">Actualizar Datos</button>
            </form>
        <?php endif; ?>

        <a href="<?php echo BASE_URL; ?>index.php?page=editor/editor_dashboard" class="btn-secondary">Volver al Panel del Editor</a>
    </div>
    <?php require_once '../views/includes/footer.php'; ?>
</body>
</html>

