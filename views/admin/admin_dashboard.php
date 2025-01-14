<?php

require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión y si es administrador
verificar_sesion_y_rol(['administrador']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Panel de Administrador</h2>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
        <div class="button-container">
            <a href="/PDO_5_MVC/public/index.php?page=clientes/clientes" class="btn-dashboard">Gestionar Clientes</a>
            <a href="/PDO_5_MVC/public/index.php?page=admin/admin_list" class="btn-dashboard">Gestionar Administradores</a>
            <a href="/PDO_5_MVC/public/index.php?page=auth/logout" class="btn-dashboard logout">Cerrar Sesión</a>
        </div>
    </div>
    <?php
    require_once '../views/includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>
