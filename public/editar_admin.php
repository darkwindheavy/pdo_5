<?php
session_start();
require_once '../includes/funciones.php'; // Importar funciones reutilizables
require_once '../includes/header.php'; // Importar la cabecera común
require_once '../includes/BaseDeDatos.php'; // Importar la clase BaseDeDatos
require_once '../includes/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión y si es administrador 
verificar_sesion_y_rol(['administrador']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

// Verificar si se recibió el ID del administrador a editar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    manejar_error("No se ha especificado un ID de administrador válido.");
}

$id = intval($_GET['id']);

try {
    // Obtener el administrador que se intenta editar
    $admin = $usuario->obtenerUsuarioPorId($id);

    if (!$admin || !in_array($admin['rol'], ['administrador', 'editor'])) {
        manejar_error("No se encontró un administrador o editor con el ID especificado.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si el ID a editar no es el del propio administrador logueado
        if ($id === $_SESSION['usuario_id']) {
            $error = "No puedes editar tu propio perfil desde aquí.";
            header("Location: admin_list.php?mensaje=" . urlencode($error));
            exit;
        }

        // Recibir los valores del formulario
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $localidad = trim($_POST['localidad']);
        $provincia = trim($_POST['provincia']);
        $rol = 'administrador'; // El rol siempre será 'administrador' para este caso
        $contrasena = isset($_POST['contrasena']) && !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;

        // Editar el administrador
        $usuario->editarUsuario($id, $dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, $rol, $contrasena);
        $exito = "Administrador actualizado exitosamente.";

        // Redirigir a la lista de administradores después de la edición
        header("Location: admin_list.php?mensaje=" . urlencode($exito));
        exit;
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
    <title>Editar Administrador</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="edit-container">
        <h2>Editar Administrador</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <?php if (!empty($admin)): ?>
            <form action="editar_admin.php?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                    <label for="dni">Dni:</label>
                    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($admin['dni']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($admin['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($admin['correo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($admin['direccion']); ?>" >
                </div>
                <div class="form-group">
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($admin['localidad']); ?>" >
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <input type="text" id="provincia" name="provincia" value="<?php echo htmlspecialchars($admin['provincia']); ?>" >
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($admin['telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="contrasena" name="contrasena">
                </div>
                <button type="submit" class="btn-primary">Actualizar</button>
            </form>
        <?php endif; ?>

        <a href="admin_list.php" class="btn-secondary">Volver a la Lista de Administradores</a>
    </div>

<?php
require_once '../includes/footer.php'; // Importar el pie de página común
?>

</body>
</html>

