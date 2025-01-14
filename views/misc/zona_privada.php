<?php
session_start();
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../views/includes/header.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona Privada</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h2>
        <div class="button-container">
            <a href="editar_usuario.php?id=<?php echo $_SESSION['usuario_id']; ?>" class="btn-dashboard">Editar Mis Datos</a>
            <a href="logout.php" class="btn-dashboard logout">Cerrar Sesión</a>
        </div>
    </div>
    <?php
    require_once '../views/includes/footer.php'; // Importar el pie de página común
    ?>

</body>
</html>

