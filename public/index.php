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

// Redirecciones según el rol
if (isset($_SESSION['usuario_rol']) && in_array($page, ['auth/login'])) {
    switch ($_SESSION['usuario_rol']) {
        case 'administrador':
            header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_dashboard");
            exit;
        case 'editor':
            header("Location: /PDO_5_MVC/public/index.php?page=editor/editor_dashboard");
            exit;
        default:
            header("Location: /PDO_5_MVC/public/index.php?page=misc/zona_privada");
            exit;
    }
}

// Verificar si la página es pública o el usuario está autenticado
$publicPages = ['auth/login', 'auth/registro', 'auth/olvide_contrasena', 'auth/restablecer_contrasena'];
if (!in_array($page, $publicPages) && !isset($_SESSION['usuario_id'])) {
    header("Location: /PDO_5_MVC/public/index.php?page=auth/login");
    exit;
}

// Verificar si la vista existe e incluirla
if (file_exists($viewPath)) {
    require_once $viewPath;
} else {
    die("Página no encontrada: $page");
}




