<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Carga las dependencias instaladas por Composer
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

$db = new BaseDeDatos();
$usuario = new Usuario($db);

$error = '';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = trim($_POST['dni']);
    $correo = trim($_POST['correo']);

    try {
        // Validar si el usuario existe
        $usuarioData = $usuario->obtenerUsuarioPorDniYCorreo($dni, $correo);
        if ($usuarioData) {
            // Generar token único y tiempo de expiración
            $token = bin2hex(random_bytes(16));
            $expiracion = date("Y-m-d H:i:s", strtotime('+30 minutes'));

            // Guardar el token en la base de datos
            $usuario->guardarTokenRecuperacion($usuarioData['id'], $token, $expiracion);

            // Configurar PHPMailer
            $mail = new PHPMailer(true);
            $enlace = "http://localhost/PDO_5_MVC/public/index.php?page=auth/restablecer_contrasena&token=" . urlencode($token);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'perrocdev@gmail.com'; // Tu correo Gmail
            $mail->Password = 'pqjthwgxyzkwixmg'; // Contraseña o App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configurar correo
            $mail->setFrom('perrocdev@gmail.com', 'Proyecto PDO5');
            $mail->addAddress($correo); // Destinatario
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar contraseña';
            $mail->Body = "Haz clic en el siguiente enlace para restablecer tu contraseña: <a href='$enlace'>$enlace</a>";

            $mail->send();
            $mensaje = "Se ha enviado un enlace de recuperación a tu correo electrónico.";
        } else {
            $error = "No se encontró un usuario con ese DNI y correo.";
        }
    } catch (Exception $e) {
        $error = "Error al procesar la solicitud: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvidé mi Contraseña</title>
    <link rel="stylesheet" href="/PDO_5_MVC/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="form-title">Recuperar Contraseña</h2>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="success-message"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <form action="/PDO_5_MVC/public/index.php?page=auth/olvide_contrasena" method="post" class="form">
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required class="form-input">
            </div>

            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required class="form-input">
            </div>

            <button type="submit" class="btn-primary">Enviar</button>
        </form>

        <div class="back-to-login">
            <a href="/PDO_5_MVC/public/index.php?page=auth/login" class="btn-secondary">Volver al Inicio de Sesión</a>
        </div>
    </div>
</body>
</html>
