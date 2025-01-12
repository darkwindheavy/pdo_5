<?php
session_start();

// Verificar si el usuario ya ha iniciado sesión
if (isset($_SESSION['usuario_id'])) {
    // Si el usuario es administrador, redirigir al dashboard del administrador
    if ($_SESSION['usuario_rol'] === 'administrador') {
        header("Location: admin_dashboard.php");
        exit;
        
    } 
    if ($_SESSION['usuario_rol'] === 'superadministrador') {
        header("Location: superadmin_dashboard.php");
        exit;
        
    } else {
        // Si el usuario es regular, redirigir a la zona privada del usuario
        header("Location: zona_privada.php");
        exit;
    }
} else {
    // Si no está autenticado, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit;
}
