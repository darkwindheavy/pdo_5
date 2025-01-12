<?php
session_start();
require_once '../includes/funciones.php'; // Importar funciones reutilizables
require_once '../includes/header.php'; // Importar la cabecera común
require_once '../includes/BaseDeDatos.php'; // Importar la clase BaseDeDatos
require_once '../includes/Articulo.php'; // Importar la clase Articulo para gestionar artículos

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
    header("Location: editor_dashboard.php?mensaje=Artículo eliminado exitosamente");
    exit;
} catch (Exception $e) {
    // Manejar el error si ocurre alguna excepción
    manejar_error("Error al eliminar el artículo: " . $e->getMessage());
}

