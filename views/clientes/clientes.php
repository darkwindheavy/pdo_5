<?php
require_once '../app/Controladores/funciones.php'; // Importar funciones
require_once '../app/Controladores/config.php';
require_once '../views/includes/header.php'; // Importar la cabecera
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);
$error ='';

// Inicializar variables
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$orden = isset($_GET['orden']) && in_array(strtoupper($_GET['orden']), ['ASC', 'DESC']) ? strtoupper($_GET['orden']) : 'ASC';
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 10;
$offset = ($paginaActual - 1) * $limite;

try {
    // Filtros para solo usuarios
    $rolesPermitidos = ['usuario'];

    // Obtener usuarios con paginación
    $usuarios = $usuario->listarUsuariosConPaginacion($rolesPermitidos, $busqueda, $orden, $limite, $offset);

    // Total de usuarios
    $totalUsuarios = $usuario->contarUsuarios($rolesPermitidos, $busqueda);

    // Calcular el número total de páginas
    $totalPaginas = ceil($totalUsuarios / $limite);
} catch (Exception $e) {
    die("Error al obtener los usuarios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="s<?php echo BASE_URL; ?>css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<h2>Lista de Usuarios</h2>

<form action="<?php echo BASE_URL; ?>index.php" method="get">
    <input type="hidden" name="page" value="clientes/clientes">
    <input type="text" name="busqueda" placeholder="Buscar usuario" value="<?php echo htmlspecialchars($busqueda); ?>">
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
                <a href="<?php echo BASE_URL; ?>index.php?page=clientes/clientes&orden=<?php echo ($orden === 'ASC') ? 'DESC' : 'ASC'; ?>&busqueda=<?php echo urlencode($busqueda); ?>" class="ordenar">
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
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($usuarios)): ?>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['dni']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['direccion']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['localidad']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['provincia']); ?></td>
                    <td class="actions">
                        <a href="<?php echo BASE_URL; ?>index.php?page=clientes/editar_cliente&id=<?php echo $usuario['id']; ?>" class="editar">Editar</a>
                        <?php if ($_SESSION['usuario_id'] !== $usuario['id']): ?>
                            <a href="<?php echo BASE_URL; ?>index.php?page=clientes/borrar_cliente&id=<?php echo $usuario['id']; ?>" class="borrar" onclick="return confirm('¿Está seguro de que desea eliminar este usuario?');">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No se encontraron usuarios.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="paginacion">
    <?php if ($paginaActual > 1): ?>
        <a href="<?php echo BASE_URL; ?>index.php?page=clientes/clientes&pagina=<?php echo $paginaActual - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>&orden=<?php echo $orden; ?>" class="prev">Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="<?php echo BASE_URL; ?>index.php?page=clientes/clientes&pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>&orden=<?php echo $orden; ?>" class="<?php echo ($i === $paginaActual) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($paginaActual < $totalPaginas): ?>
        <a href="<?php echo BASE_URL; ?>index.php?page=clientes/clientes&pagina=<?php echo $paginaActual + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>&orden=<?php echo $orden; ?>" class="next">Siguiente</a>
    <?php endif; ?>
</div>

<div class="add-client">
    <a href="<?php echo BASE_URL; ?>index.php?page=clientes/cliente_nuevo" class="add">Añadir Nuevo Usuario</a>
</div>

<?php
require_once '../views/includes/footer.php'; // Importar el pie de página común
?>

</body>
</html>


            
                
