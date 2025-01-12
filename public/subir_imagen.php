<?php
session_start();
require_once '../includes/funciones.php'; // Importar funciones reutilizables
require_once '../includes/header.php'; // Importar la cabecera común

// Verificar si el usuario ha iniciado sesión
verificar_sesion_y_rol(['usuario', 'administrador', 'editor']); // Permitir a todos los roles acceder

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Formulario de Subida de Imágenes</h2>
    <form action="procesar_imagen.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="imagen">Seleccionar imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>
        </div>
        <div class="form-group">
            <label for="anchura">Anchura (en píxeles):</label>
            <input type="number" id="anchura" name="anchura" min="1" required>
        </div>
        <div class="form-group">
            <label for="altura">Altura (en píxeles):</label>
            <input type="number" id="altura" name="altura" min="1" required>
        </div>
        <button type="submit">Subir Imagen</button>
    </form>
</body>
</html>

