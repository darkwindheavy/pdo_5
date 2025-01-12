<?php
session_start();
require_once '../includes/BaseDeDatos.php';
require_once '../includes/Usuario.php';

$db = new BaseDeDatos();
$usuario = new Usuario($db);

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$error = '';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevaContrasena = trim($_POST['nueva_contrasena']);
    $confirmarContrasena = trim($_POST['confirmar_contrasena']);

    if (empty($nuevaContrasena) || empty($confirmarContrasena)) {
        $error = "Ambos campos de contraseña son obligatorios.";
    } elseif ($nuevaContrasena !== $confirmarContrasena) {
        $error = "Las contraseñas no coinciden.";
    } else {
        try {
            // Validar el token y actualizar la contraseña
            $usuario->restablecerContrasena($token, $nuevaContrasena);
            $mensaje = "Contraseña actualizada correctamente.";
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
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="form-title">Restablecer Contraseña</h2>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="success-message"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php else: ?>
            <form action="restablecer_contrasena.php?token=<?php echo urlencode($token); ?>" method="post" class="form">
                <div class="form-group">
                    <label for="nueva_contrasena">Nueva Contraseña:</label>
                    <input type="password" id="nueva_contrasena" name="nueva_contrasena" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required class="form-input">
                </div>

                <button type="submit" class="btn-primary">Actualizar Contraseña</button>
            </form>
        <?php endif; ?>

        <!-- Botón para regresar al login -->
<div class="back-to-login">
    <a href="login.php" class="btn-secondary">Volver al Inicio de Sesión</a>
</div>

    </div>
</body>
</html>

