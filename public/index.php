<?php
session_start();

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

// Verificar si la vista existe
if (file_exists($viewPath)) {
    // Lógica de roles o permisos (si es necesario)
    if ($page !== 'auth/login' && !isset($_SESSION['usuario_id'])) {
        header("Location: /index.php?page=auth/login");
        exit;
    }

    // Incluir la vista
    require_once $viewPath;
} else {
    die("Página no encontrada: $page");
}


