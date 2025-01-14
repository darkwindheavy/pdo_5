<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión y si es administrador
verificar_sesion_y_rol(['administrador']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

// Verificar si se recibió el ID del administrador a borrar a través de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    manejar_error("No se ha especificado un ID de administrador válido.");
}

$id = intval($_GET['id']);

// Obtener el ID del administrador que está actualmente logueado
$admin_logueado_id = $_SESSION['usuario_id'];

// Verificar si el administrador que se intenta eliminar es el mismo que está logueado
if ($id === $admin_logueado_id) {
    // Mostrar mensaje de error y redirigir a la lista de administradores
    echo '<script>
            alert("No puedes eliminar tu propia cuenta desde aquí.");
            window.location.href = "/PDO_5_MVC/public/index.php?page=admin/admin_list";
        </script>';
    exit;
}

try {
    // Proceder con la eliminación si no es el administrador logueado
    $usuario->eliminarUsuario($id);
    // Redirigir al usuario de nuevo a la lista de administradores con un mensaje de éxito
    header("Location: /PDO_5_MVC/public/index.php?page=admin/admin_list&mensaje=Administrador eliminado exitosamente");
    exit;
} catch (Exception $e) {
    manejar_error("Error al eliminar el administrador: " . $e->getMessage());
}




    


