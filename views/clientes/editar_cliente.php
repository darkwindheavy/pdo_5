<?php
session_start();
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión y si es administrador
verificar_sesion_y_rol(['administrador']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$exito = '';

// Verificar si se recibió el ID del cliente a editar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    manejar_error("No se ha especificado un ID de cliente válido.");
}

$id = intval($_GET['id']);

try {
    // Obtener el cliente que se intenta editar
    $cliente = $usuario->obtenerUsuarioPorId($id);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recibir los valores del formulario
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $localidad = trim($_POST['localidad']);
        $provincia = trim($_POST['provincia']);
        $rol = 'usuario'; // El rol siempre será 'usuario' para clientes
        $contrasena = isset($_POST['contrasena']) && !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;


        // Editar el cliente
        $usuario->editarUsuario($id, $dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, $rol, $contrasena);
        $exito = "Cliente actualizado exitosamente.";
        //$cliente = $usuario->obtenerUsuarioPorId($id);

        // Redirigir a la lista de administradores después de crear el usuario
        header("Location: clientes.php?mensaje=" . urlencode($exito));
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
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="/public/css/styles.css">
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
            <form action="editar_cliente.php?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                    <label for="nombre">Dni:</label>
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
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion']); ?>" >
                </div>
                <div class="form-group">
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo htmlspecialchars($cliente['localidad']); ?>" >
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <input type="text" id="provincia" name="provincia" value="<?php echo htmlspecialchars($cliente['provincia']); ?>" >
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" required>
                        <option value="usuario" <?php echo ($cliente['rol'] === 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                        <?php if ($_SESSION['usuario_rol'] === 'superadministrador'): ?>
                            <option value="administrador" <?php echo ($cliente['rol'] === 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="contrasena" name="contrasena">
                </div>
                <button type="submit" class="btn-primary">Actualizar</button>
                <button type="submit" name="eliminar" onclick="return confirm('Está seguro de que desea eliminar este cliente?');" class="btn-delete">Eliminar Cliente</button>
            </form>
        <?php endif; ?>

        <a href="clientes.php" class="btn-secondary">Volver a la Lista de Clientes</a>
    </div>

<?php
require_once '../views/includes/footer.php'; // Importar el pie de página común
?>

</body>
</html>
