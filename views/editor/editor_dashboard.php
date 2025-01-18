<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../views/includes/header.php';
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Articulo.php'; // Importar la clase Usuario

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
    <link rel="stylesheet" href="/PDO_5_MVC/public/css/styles.css">
    <title>Panel de Editor</title>
</head>
<body>
    <div class="main-content">
        <h2>Panel del Editor</h2>

        <!-- Formulario de búsqueda -->
        <form action="/PDO_5_MVC/public/index.php" method="get">
            <input type="hidden" name="page" value="editor/editor_dashboard">
            <input type="text" name="buscar" placeholder="Buscar artículo" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">Buscar</button>
        </form>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="exito"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Tabla de artículos -->
        <table>
            <thead>
                <tr>
                    <th>
                        <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard&orden=codigo&direccion=<?php echo ($orden === 'codigo' && $direccion === 'ASC') ? 'DESC' : 'ASC'; ?>&buscar=<?php echo urlencode($busqueda); ?>&pagina=<?php echo $pagina; ?>">
                            Código
                            <?php if ($orden === 'codigo'): ?>
                                <i class="fas fa-arrow-<?php echo $direccion === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard&orden=nombre&direccion=<?php echo ($orden === 'nombre' && $direccion === 'ASC') ? 'DESC' : 'ASC'; ?>&buscar=<?php echo urlencode($busqueda); ?>&pagina=<?php echo $pagina; ?>">
                            Nombre
                            <?php if ($orden === 'nombre'): ?>
                                <i class="fas fa-arrow-<?php echo $direccion === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>Descripción</th>
                    <th>
                        <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard&orden=categoria&direccion=<?php echo ($orden === 'categoria' && $direccion === 'ASC') ? 'DESC' : 'ASC'; ?>&buscar=<?php echo urlencode($busqueda); ?>&pagina=<?php echo $pagina; ?>">
                            Categoría
                            <?php if ($orden === 'categoria'): ?>
                                <i class="fas fa-arrow-<?php echo $direccion === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard&orden=precio&direccion=<?php echo ($orden === 'precio' && $direccion === 'ASC') ? 'DESC' : 'ASC'; ?>&buscar=<?php echo urlencode($busqueda); ?>&pagina=<?php echo $pagina; ?>">
                            Precio
                            <?php if ($orden === 'precio'): ?>
                                <i class="fas fa-arrow-<?php echo $direccion === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
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
                                <img src="/PDO_5_MVC/public/uploads/articulos/<?php echo htmlspecialchars($articulo['imagen']); ?>" 
                                class="table-img" 
                                alt="Imagen del Artículo"
                                data-enlargeable>
                            </td>
                            <td class="actions">
                                <div class="actions-container">
                                    <a href="/PDO_5_MVC/public/index.php?page=articulos/editar_articulo&id=<?php echo urlencode($articulo['id']); ?>" class="editar">Editar</a>
                                    <a href="/PDO_5_MVC/public/index.php?page=articulos/borrar_articulo&id=<?php echo urlencode($articulo['id']); ?>" class="borrar" onclick="return confirm('¿Está seguro de que desea eliminar este artículo?');">Eliminar</a>
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
        
        <!-- Paginación -->
        <div class="paginacion">
            <?php if ($pagina > 1): ?>
                <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard&pagina=<?php echo $pagina - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>" class="btn btn-anterior">
                    <i class="fas fa-chevron-left"></i> Anterior
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard&pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>" 
                    class="btn <?php echo $i == $pagina ? 'activo' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina < $totalPaginas): ?>
                <a href="/PDO_5_MVC/public/index.php?page=editor/editor_dashboard&pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>" class="btn btn-siguiente">
                    Siguiente <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="button-container">
            <a href="/PDO_5_MVC/public/index.php?page=articulos/alta_articulo" class="btn-primary center-button">Añadir Nuevo Artículo</a>
            <?php if ($_SESSION['usuario_rol'] !== 'administrador'): ?>
                <a href="/PDO_5_MVC/public/index.php?page=usuarios/editar_editor" class="editar-datos">Editar Mis Datos</a>
            <?php endif; ?>

        </div>
    </div>

    <?php
    require_once '../views/includes/footer.php'; // Importar el pie de página común
    ?>
    
    <script src="/PDO_5_MVC/public/js/script.js"></script>

</body>
</html>

