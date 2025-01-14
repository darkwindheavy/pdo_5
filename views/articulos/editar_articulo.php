<?php
session_start();
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Articulo.php'; // Importar la clase Usuario

// Verificar si el usuario tiene acceso (editor o administrador)
verificar_sesion_y_rol(['administrador', 'editor']);

// Inicializar la conexión a la base de datos y la clase Articulo
$db = new BaseDeDatos();
$articulo = new Articulo($db);

$error = '';
$exito = '';

// Verificar si se recibió el ID del artículo a editar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    manejar_error("No se ha especificado un ID de artículo válido.");
}

$id = intval($_GET['id']);

try {
    // Obtener los datos del artículo que se intenta editar
    $datos_articulo = $articulo->obtenerArticuloPorId($id);

    if (!$datos_articulo) {
        manejar_error("No se encontró el artículo especificado.");
    }

    // Manejar la solicitud POST para actualizar los datos del artículo
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recibir y sanitizar los valores del formulario
        $codigo = strtoupper(trim($_POST['codigo']));
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $categoria = trim($_POST['categoria']);
        $precio = trim($_POST['precio']);
        $imagen = $_FILES['imagen'];

        // Validar el patrón del código (tres letras seguidas de hasta cinco números)
        if (!preg_match('/^[A-Z]{3}\d{1,5}$/', $codigo)) {
            $error = "El código debe contener tres letras seguidas de hasta cinco números.";
        } elseif ($imagen['size'] > 0 && $imagen['size'] > 300 * 1024) {
            $error = "La imagen debe ser menor de 300 KB.";
        } else {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if ($imagen['size'] > 0 && !in_array($imagen['type'], $allowedMimeTypes)) {
                $error = "El tipo de archivo de la imagen no es permitido. Solo se permiten JPG, PNG o GIF.";
            } else {
                // Procesar y subir la imagen si se ha proporcionado una nueva
                $nuevoNombre = null;
                if ($imagen['size'] > 0) {
                    $imageInfo = getimagesize($imagen['tmp_name']);
                    if ($imageInfo[0] > 200 || $imageInfo[1] > 200) {
                        $error = "La imagen no debe tener dimensiones mayores que 200x200 píxeles.";
                    } else {
                        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/PDO_5_MVC/public/uploads/articulos/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $nuevoNombre = uniqid() . '_' . basename($imagen['name']);
                        $uploadPath = $uploadDir . $nuevoNombre;

                        if (!move_uploaded_file($imagen['tmp_name'], $uploadPath)) {
                            $error = "Error al subir la imagen.";
                        }
                    }
                }

                // Si no hay errores, proceder con la actualización
                if (empty($error)) {
                    try {
                        $articulo->editarArticulo($id, $codigo, $nombre, $descripcion, $categoria, $precio, $nuevoNombre);
                        $exito = "Artículo actualizado exitosamente.";
                        // Obtener los datos actualizados del artículo
                        $datos_articulo = $articulo->obtenerArticuloPorId($id);
                    } catch (Exception $e) {
                        $error = "Error al actualizar el artículo: " . $e->getMessage();
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    $error = "Error al obtener los datos del artículo: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/styles.css">
    <title>Editar Artículo</title>
</head>
<body>
<div class="edit-container">
    <h2>Editar Artículo</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="exito"><?php echo htmlspecialchars($exito); ?></div>
    <?php endif; ?>

    <?php if (!empty($datos_articulo)): ?>
        <form action="/PDO_5_MVC/public/index.php?page=editor/editar_articulo&id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo" value="<?php echo htmlspecialchars($datos_articulo['codigo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos_articulo['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($datos_articulo['descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($datos_articulo['categoria']); ?>" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" id="precio" name="precio" value="<?php echo htmlspecialchars($datos_articulo['precio']); ?>" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen (dejar en blanco para mantener la imagen actual):</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png, image/gif">
            </div>
            <button type="submit" class="btn-primary">Actualizar Artículo</button>
        </form>
    <?php endif; ?>

    <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard" class="btn-secondary">Volver al Dashboard del Editor</a>
</div>
<?php
require_once '../views/includes/footer.php'; // Importar el pie de página común
?>
</body>
</html>

