<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../app/Controladores/config.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

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
        $usuario->crearUsuario($dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, $rol, $contrasena);
        $exito = "Cliente añadido exitosamente.";

        // Redirigir si no hay errores
        header("Location: " . BASE_URL . "index.php?page=clientes/clientes&mensaje=" . urlencode($exito));
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/styles.css">
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

        <form action="<?php echo BASE_URL; ?>index.php?page=clientes/cliente_nuevo" method="post">
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
                <input type="password" id="contrasena" name="contrasena">
            </div>
            <button type="submit" class="btn-primary">Añadir Cliente</button>
        </form>

        <a href="<?php echo BASE_URL; ?>index.php?page=clientes/clientes" class="btn-secondary">Volver a la Lista de Clientes</a>
    </div>
    <?php
    require_once '../views/includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>




