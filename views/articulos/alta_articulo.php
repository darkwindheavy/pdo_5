<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Articulo.php'; // Importar la clase Usuario

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
        header("Location: /PDO_5_MVC/public/index.php?page=editor/editor_dashboard&mensaje=" . urlencode("Artículo registrado exitosamente."));
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
    <link rel="stylesheet" href="/PDO_5_MVC/public/css/styles.css">
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

        <form action="/PDO_5_MVC/public/index.php?page=articulos/alta_articulo" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo" required pattern="[a-zA-Z]{3}[0-9]{1,5}" placeholder="Tres letras seguidas de hasta cinco números">
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
        <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard" class="btn-secondary">Volver</a>
    </div>

    <?php
    require_once '../views/includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>
