<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener el nombre de la página actual
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Sistema de Gestión de Clientes</title>
</head>
<body>
    <header>
        <h1>Bienvenido al Sistema de Gestión</h1>
        <!-- Solo mostrar la barra de navegación si no estamos en login.php -->
        <?php if ($current_page !== 'login.php'): ?>
        <nav class="navbar">
            <ul>
                <li><a href="index.php" class="nav-button">Inicio</a></li>
                <?php if (isset($_SESSION['usuario_rol']) && ($_SESSION['usuario_rol'] === 'administrador' || $_SESSION['usuario_rol'] === 'superadministrador')): ?>
                    <li><a href="clientes.php" class="nav-button">Clientes</a></li>
                    <li><a href="admin_dashboard.php" class="nav-button">Panel Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="nav-button">Cerrar Sesión</a></li>
            </ul>
        </nav>
        <?php endif; ?>
    </header>



