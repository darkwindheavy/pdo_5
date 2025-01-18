<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Verificar que el usuario tiene permisos para esta acción
verificar_sesion_y_rol(['administrador']);

$db = new BaseDeDatos();
$usuario = new Usuario($db);

// Verificar si se recibió un ID válido
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_list&error=ID inválido");
    exit;
}

$id = intval($_GET['id']);

// Verificar si el ID coincide con el del administrador logueado
if ($id === $_SESSION['usuario_id']) {
    header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_list&error=No puedes eliminar tu propia cuenta");
    exit;
}

try {
    // Eliminar administrador
    $usuario->eliminarUsuario($id);

    // Redirigir con mensaje de éxito
    header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_list&mensaje=Administrador eliminado exitosamente");
    exit;
} catch (Exception $e) {
    // Redirigir con mensaje de error
    header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_list&error=" . urlencode("Error al eliminar el administrador: " . $e->getMessage()));
    exit;
}






    


