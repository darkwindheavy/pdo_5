<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar dependencias
require_once '../app/Controladores/funciones.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php';
require_once '../app/Modelos/Articulo.php';

// Enrutamiento: determinar qué página cargar
$page = isset($_GET['page']) ? $_GET['page'] : 'auth/login';
$viewPath = "../views/$page.php";

// Lógica para redirigir según el rol del usuario
if (isset($_SESSION['usuario_rol'])) {
    switch ($_SESSION['usuario_rol']) {
        case 'administrador':
            // Redirigir al dashboard del administrador
            if ($page === 'auth/login') {
                header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_dashboard");
                exit;
            }
            break;
        case 'editor':
            // Redirigir al dashboard del editor
            if ($page === 'auth/login') {
                header("Location: /PDO_5_MVC/public/index.php?page=editor/editor_dashboard");
                exit;
            }
            break;
        default:
            // Redirigir a la zona privada por defecto
            if ($page === 'auth/login') {
                header("Location: /PDO_5_MVC/public/index.php?page=zona_privada");
                exit;
            }
            break;
    }
}

// Verificar si la vista existe
if (file_exists($viewPath)) {
    // Verificar si se requiere inicio de sesión para la vista
    if ($page !== 'auth/login' && !isset($_SESSION['usuario_id'])) {
        header("Location: /PDO_5_MVC/public/index.php?page=auth/login");
        exit;
    }

    // Incluir la vista
    require_once $viewPath;
} else {
    die("Página no encontrada: $page");
}



