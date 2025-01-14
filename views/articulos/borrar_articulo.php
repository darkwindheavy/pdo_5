<?php
session_start();
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Articulo.php'; // Importar la clase Usuario

// Verificar si el usuario tiene acceso (ej. editor o administrador)
verificar_sesion_y_rol(['administrador', 'editor']);

// Inicializar la conexión a la base de datos y la clase Articulo
$db = new BaseDeDatos();
$articulo = new Articulo($db);

// Verificar si se recibió el ID del artículo a borrar a través de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    manejar_error("No se ha especificado un ID de artículo válido.");
    exit;
}

$id = intval($_GET['id']);

try {
    // Intentar borrar el artículo usando el método de la clase Articulo
    $articulo->borrarArticulo($id);

    // Redirigir al usuario de nuevo a la lista de artículos con un mensaje de éxito
    header("Location: /PDO_5_MVC/public/index.php?page=editor/editor_dashboard&mensaje=Artículo eliminado exitosamente");
    exit;
} catch (Exception $e) {
    // Manejar el error si ocurre alguna excepción
    manejar_error("Error al eliminar el artículo: " . $e->getMessage());
}

