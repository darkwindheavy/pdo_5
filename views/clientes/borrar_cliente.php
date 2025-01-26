<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../app/Controladores/config.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario ha iniciado sesión y si es administrador
verificar_sesion_y_rol(['administrador']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

// Verificar si se recibió el ID del cliente a borrar a través de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    manejar_error("No se ha especificado un ID de cliente válido.");
}

$id = intval($_GET['id']);

try {
    // Obtener el cliente que se intenta eliminar
    $cliente = $usuario->obtenerUsuarioPorId($id);

    if ($cliente) {
        // Proceder con la eliminación
        $resultado = $usuario->eliminarUsuario($id);

        if ($resultado) {
            // Redirigir al usuario de nuevo a la lista de clientes con un mensaje de éxito
            header("Location: " . BASE_URL . "index.php?page=clientes/clientes&mensaje=Cliente eliminado exitosamente");
            exit;
        } else {
            manejar_error("No se pudo eliminar el cliente. Es posible que no exista.");
        }
    } else {
        manejar_error("El cliente especificado no existe.");
    }
} catch (Exception $e) {
    manejar_error("Error al eliminar el cliente: " . $e->getMessage());
}





