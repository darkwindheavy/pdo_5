<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../app/Controladores/config.php';
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

verificar_sesion_y_rol(['administrador']); // Verificar rol de administrador

$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

// Verificar si se recibió el ID del cliente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    manejar_error("No se ha especificado un ID válido.");
}

$id = intval($_GET['id']);

try {
    // Obtener datos del cliente
    $cliente = $usuario->obtenerUsuarioPorId($id);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['eliminar'])) {
            // Eliminar cliente
            $usuario->eliminarUsuario($id);
            $exito = "Cliente eliminado exitosamente.";
            header("Location: " . BASE_URL . "index.php?page=clientes/clientes&mensaje=" . urlencode($exito));
            exit;
        } else {
            // Actualizar cliente
            $dni = trim($_POST['dni']);
            $nombre = trim($_POST['nombre']);
            $correo = trim($_POST['correo']);
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            $localidad = trim($_POST['localidad']);
            $provincia = trim($_POST['provincia']);
            $contrasena = !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;

            $usuario->editarUsuario($id, $dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, 'usuario', $contrasena);
            $exito = "Cliente actualizado exitosamente.";
            header("Location: " . BASE_URL . "index.php?page=clientes/clientes&mensaje=" . urlencode($exito));
            exit;
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
    <title>Editar Cliente</title>
    <link rel="s<?php echo BASE_URL; ?>css/styles.css">
</head>
<body>
    <div class="edit-container">
        <h2>Editar Cliente</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <?php if (!empty($cliente)): ?>
            <form action="<?php echo BASE_URL; ?>index.php?page=clientes/editar_cliente&id=<?php echo $id; ?>" method="post">
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($cliente['dni']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion']); ?>">
                </div>
                <div class="form-group">
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($cliente['localidad']); ?>">
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <input type="text" id="provincia" name="provincia" value="<?php echo htmlspecialchars($cliente['provincia']); ?>">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="contrasena" name="contrasena">
                </div>
                <button type="submit" class="btn-primary">Actualizar</button>
                <button type="submit" name="eliminar" class="btn-delete" onclick="return confirm('¿Está seguro de que desea eliminar este cliente?');">Eliminar Cliente</button>
            </form>
        <?php endif; ?>

        <a href="<?php echo BASE_URL; ?>index.php?page=clientes/clientes" class="btn-secondary">Volver a la Lista de Clientes</a>
    </div>

    <?php require_once '../views/includes/footer.php'; ?>
</body>
</html>

