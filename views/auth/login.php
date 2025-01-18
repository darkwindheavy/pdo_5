<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones reutilizables
$mostrar_enlaces = false; // No mostrar enlaces en esta páginas
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los valores y sanitizarlos
    $dni = isset($_POST['dni']) ? trim(htmlspecialchars($_POST['dni'])) : '';
    $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

    // Validación de campos
    if (empty($dni) || empty($contrasena)) {
        $error = "Por favor, ingrese el DNI y la contraseña.";
    } else {
        try {
            // Verificar si el DNI existe y obtener el hash de la contraseña
            $usuarioData = $usuario->obtenerUsuario($dni);

            if ($usuarioData && password_verify($contrasena, $usuarioData['contrasena'])) {
                // Si la contraseña es correcta, iniciar la sesión
                $_SESSION['usuario_id'] = $usuarioData['id'];
                $_SESSION['usuario_nombre'] = $usuarioData['nombre'];
                $_SESSION['usuario_rol'] = $usuarioData['rol'];

                
                // Redirigir al panel correspondiente según el rol
                switch (strtolower($usuarioData['rol'])) {
                    case 'administrador':
                        header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_dashboard");
                        break;
                    case 'editor':
                        header("Location: /PDO_5_MVC/public/index.php?page=editor/editor_dashboard");
                        break;
                    default:
                        header("Location: /PDO_5_MVC/public/index.php?page=misc/zona_privada");
                        break;
                }
                exit;
            } else {
                $error = "DNI o contraseña incorrectos.";
            }
        } catch (Exception $e) {
            $error = "Error al iniciar sesión: " . $e->getMessage();
        }
    }
}
?>

<!-- Formulario de Inicio de Sesión -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/styles.css">
    <title>Inicio de Sesión</title>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="exito">
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="/PDO_5_MVC/public/index.php?page=auth/login" method="post">
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit" class="btn-primary">Iniciar Sesión</button>
        </form>
        <a href="/PDO_5_MVC/public/index.php?page=auth/registro" class="btn-secondary">Crear una cuenta</a>
        <a href="/PDO_5_MVC/public/index.php?page=auth/olvide_contrasena" class="btn-secondary">Olvidé mi contraseña</a>
    <?php
    require_once '../views/includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>
