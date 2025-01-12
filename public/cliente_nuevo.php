<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start(); // Captura cualquier salida accidental

require_once '../includes/funciones.php';
require_once '../includes/BaseDeDatos.php';
require_once '../includes/Usuario.php';

ob_end_clean(); // Limpia cualquier salida acumulada

// Verificar si el usuario ha iniciado sesión y si es administrador
verificar_sesion_y_rol(['administrador']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Recibir los valores del formulario
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $localidad = trim($_POST['localidad']);
        $provincia = trim($_POST['provincia']);
        $rol = 'usuario'; // El rol será siempre 'usuario' para los clientes
        $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;

        // Crear el cliente
        $exito = $usuario->crearUsuario($dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, $rol, $contrasena);
        $mensaje = "Cliente añadido exitosamente: " . htmlspecialchars($exito);

        // Redirigir si no hay errores
        header("Location: clientes.php?mensaje=" . urlencode($exito));
        exit;

            } catch (Exception $e) {
        $error = "Error al añadir cliente: " . htmlspecialchars($e->getMessage());
        }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Cliente</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="edit-container">
        <h2>Añadir Nuevo Cliente</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <form action="cliente_nuevo.php" method="post">
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($dni ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="localidad">Localidad:</label>
                <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($localidad ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="provincia">Provincia:</label>
                <input type="text" id="provincia" name="provincia" value="<?php echo htmlspecialchars($provincia ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" value="">
            </div>
            <button type="submit" class="btn-primary">Añadir Cliente</button>
        </form>

        <a href="clientes.php" class="btn-secondary">Volver a la Lista de Clientes</a>
    </div>
    <?php
    require_once '../includes/footer.php'; // Importar el pie de página común
    ?>

</body>
</html>



