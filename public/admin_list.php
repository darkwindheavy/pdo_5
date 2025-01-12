<?php
session_start();
require_once '../includes/funciones.php'; // Importar funciones reutilizables
require_once '../includes/header.php'; // Importar la cabecera común
require_once '../includes/BaseDeDatos.php'; // Importar la clase BaseDeDatos
require_once '../includes/Usuario.php'; // Importar la clase Usuario

// Verificar si el usuario tiene el rol adecuado
verificar_sesion_y_rol(['administrador']);

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);
$error = '';

// Inicializar variables de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$orden = isset($_GET['orden']) && in_array(strtoupper($_GET['orden']), ['ASC', 'DESC']) ? strtoupper($_GET['orden']) : 'ASC';
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 10;
$offset = ($paginaActual - 1) * $limite;


try {
    // Definir los roles permitidos
    $rolesPermitidos = ['administrador', 'editor'];

    // Obtener usuarios con paginación
    $usuarios = $usuario->listarUsuariosConPaginacion($rolesPermitidos, $busqueda, $orden, $limite, $offset);

    // Total de usuarios
    $totalUsuarios = $usuario->contarUsuarios($rolesPermitidos, $busqueda);

    // Calcular el número total de páginas
    $totalPaginas = ceil($totalUsuarios / $limite);
} catch (Exception $e) {
    $error = "Error al obtener los usuarios: " . $e->getMessage();
}

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Lista de Administradores</title>
</head>
<body>
    <h2>Lista de Administradores y editores</h2>

    <form action="admin_list.php" method="get">
        <input type="text" name="busqueda" placeholder="Buscar administrador o editor" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="exito"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
            <th>
                <a href="?orden=<?php echo ($orden === 'ASC') ? 'DESC' : 'ASC'; ?>&busqueda=<?php echo urlencode($busqueda); ?>" class="ordenar">
                Nombre
                        <?php if ($orden === 'ASC'): ?>
                            <i class="fas fa-arrow-up"></i>
                        <?php else: ?>
                            <i class="fas fa-arrow-down"></i>
                        <?php endif; ?>
                </a>
            </th>
                <th>DNI</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Localidad</th>
                <th>Provincia</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
    <?php if (!empty($usuarios)): ?>
        <?php foreach ($usuarios as $admin): ?>
            <tr>
                <td><?php echo htmlspecialchars($admin['nombre']); ?></td>
                <td><?php echo htmlspecialchars($admin['dni']); ?></td>
                <td><?php echo htmlspecialchars($admin['correo']); ?></td>
                <td><?php echo htmlspecialchars($admin['telefono']); ?></td>
                <td><?php echo htmlspecialchars($admin['direccion']); ?></td>
                <td><?php echo htmlspecialchars($admin['localidad']); ?></td>
                <td><?php echo htmlspecialchars($admin['provincia']); ?></td>
                <td><?php echo htmlspecialchars($admin['rol']); ?></td>
                <td class="actions">
                    <?php if ($admin['rol'] !== 'superadministrador'): ?>
                        <a href="editar_admin.php?id=<?php echo $admin['id']; ?>" class="editar">Editar</a>
                        <a href="borrar_admin.php?id=<?php echo $admin['id']; ?>" class="borrar" onclick="return confirm('¿Está seguro de que desea eliminar este administrador?');">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">No se encontraron administradores o editores.</td>
        </tr>
    <?php endif; ?>
        </tbody>
    </table>

    <div class="paginacion">
    <?php if ($paginaActual > 1): ?>
        <a href="?pagina=<?php echo $paginaActual - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>&orden=<?php echo $orden; ?>" class="prev">Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>&orden=<?php echo $orden; ?>" class="<?php echo ($i === $paginaActual) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($paginaActual < $totalPaginas): ?>
        <a href="?pagina=<?php echo $paginaActual + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>&orden=<?php echo $orden; ?>" class="next">Siguiente</a>
    <?php endif; ?>
</div>


    <div class="add-client">
        <a href="admin_nuevo.php" class="add">Añadir Nuevo Administrador o editor</a>
        <a href="editor_dashboard.php" class="add">Artículos</a>
    </div>

    <?php
    require_once '../includes/footer.php'; // Importar el pie de página común
    ?>
</body>
</html>                      

