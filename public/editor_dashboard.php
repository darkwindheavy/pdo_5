<?php
session_start();
require_once '../includes/funciones.php';
require_once '../includes/header.php';
require_once '../includes/BaseDeDatos.php';
require_once '../includes/Articulo.php';

verificar_sesion_y_rol(['editor', 'administrador']);

$db = new BaseDeDatos();
$articulo = new Articulo($db);

$error = '';

// Paginación y búsqueda
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 10; // Artículos por página
$offset = ($pagina - 1) * $limite;

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$orden = isset($_GET['orden']) ? trim($_GET['orden']) : 'nombre';
$direccion = isset($_GET['direccion']) ? strtoupper(trim($_GET['direccion'])) : 'ASC';

try {
    // Obtener artículos con búsqueda y paginación
    $articulos = $articulo->listarArticulos($busqueda, $limite, $offset, $orden, $direccion);
    $totalArticulos = $articulo->contarArticulos($busqueda);
    $totalPaginas = ceil($totalArticulos / $limite);
} catch (Exception $e) {
    $error = "Error al obtener la lista de artículos: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Panel de Editor</title>
</head>
<body>
    <h2>Panel del Editor</h2>

    <form action="editor_dashboard.php" method="get">
        <input type="text" name="buscar" placeholder="Buscar artículo" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="exito"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php endif; ?>


    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($articulos)): ?>
    <?php foreach ($articulos as $articulo): ?>
        <tr>
            <td><?php echo htmlspecialchars($articulo['codigo']); ?></td>
            <td><?php echo htmlspecialchars($articulo['nombre']); ?></td>
            <td><?php echo htmlspecialchars($articulo['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($articulo['categoria']); ?></td>
            <td><?php echo htmlspecialchars($articulo['precio']); ?></td>
            <td>
                <img src="../uploads/articulos/<?php echo htmlspecialchars($articulo['imagen']); ?>" class="table-img" alt="Imagen del Artículo">
            </td>
            <td class="actions">
                <div class="actions-container">
                    <a href="editar_articulo.php?id=<?php echo urlencode($articulo['id']); ?>" class="editar">Editar</a>
                    <a href="borrar_articulo.php?id=<?php echo urlencode($articulo['id']); ?>" class="borrar" onclick="return confirm('¿Está seguro de que desea eliminar este artículo?');">Eliminar</a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7">No se encontraron artículos.</td>
    </tr>
<?php endif; ?>
        </tbody>
    </table>

    <div class="paginacion">
    <?php if ($pagina > 1): ?>
        <a href="?pagina=<?php echo $pagina - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>" class="btn btn-anterior">
            <i class="fas fa-chevron-left"></i> Anterior
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>" 
            class="btn <?php echo $i == $pagina ? 'activo' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
        <a href="?pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>" class="btn btn-siguiente">
            Siguiente <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</div>


    <div class="button-container">
        <a href="alta_articulo.php" class="btn-primary center-button">Añadir Nuevo Artículo</a>
        <a href="editar_editor.php" class="editar-datos">Editar Mis Datos</a>
    </div>


    <?php
    require_once '../includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>
