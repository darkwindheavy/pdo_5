<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones reutilizables
require_once '../views/includes/header.php';
$error ='';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /PDO_5_MVC/public/index.php?page=auth/login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona Privada</title>
    <link rel="stylesheet" href="/PDO_5_MVC/public/css/styles.css">
</head>
<body>
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="exito"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php endif; ?>

    <div class="dashboard-container">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h2>
        <div class="button-container">
            <a href="/PDO_5_MVC/public/index.php?page=usuarios/editar_usuario&id=<?php echo $_SESSION['usuario_id']; ?>" class="btn-dashboard">Editar Mis Datos</a>
            <a href="/PDO_5_MVC/public/index.php?page=auth/logout" class="btn-dashboard logout">Cerrar Sesión</a>
        </div>
    </div>

    <?php
    require_once '../views/includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>


