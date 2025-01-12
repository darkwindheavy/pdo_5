<?php
session_start();
require_once '../includes/funciones.php';
require_once '../includes/header.php';
require_once '../includes/BaseDeDatos.php';
require_once '../includes/Articulo.php';

verificar_sesion_y_rol(['administrador', 'editor']);

$db = new BaseDeDatos();
$articulo = new Articulo($db);

$error = '';
$exito = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Recibir los valores del formulario
        $codigo = strtoupper(trim($_POST['codigo']));
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $categoria = trim($_POST['categoria']);
        $precio = trim($_POST['precio']);
        $imagen = $_FILES['imagen'];

        // Validar los datos del artículo
        $articulo->validarArticulo($codigo, $imagen);

        // Procesar la imagen
        $nuevoNombre = $articulo->procesarImagen($imagen);

        // Crear el artículo en la base de datos
        $articulo->crearArticulo($codigo, $nombre, $descripcion, $categoria, $precio, $nuevoNombre);

        // Redirigir con mensaje de éxito
        header("Location: editor_dashboard.php?mensaje=" . urlencode("Artículo registrado exitosamente."));
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Alta de Artículo</title>
</head>
<body>
    <div class="container">
        <h2>Registrar Nuevo Artículo</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <form action="alta_articulo.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo" required pattern="[a-zA-Z]{3}[0-9]{1,5}" placeholder="El código debe estar formado por tres letras y seguido de hasta cinco números">
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <input type="text" id="categoria" name="categoria" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen (200x200 píxeles, máx 300 KB):</label>
                <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.gif" required>
            </div>
            <button type="submit" class="btn-primary">Registrar Artículo</button>
        </form>
        <a href="editor_dashboard.php" class="btn-secondary">Volver</a>
    </div>

    <?php
    require_once '../includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>
