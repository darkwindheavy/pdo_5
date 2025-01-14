<?php
require_once '../app/Modelos/BaseDeDatos.php';
require_once '../app/Modelos/Usuario.php'; // Importar la clase Usuario

// Inicializar la conexión a la base de datos y la clase Usuario
$db = new BaseDeDatos();
$usuario = new Usuario($db);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=clientes.csv');

// Abrir un flujo de salida para escribir el CSV
$output = fopen('php://output', 'w');

// Escribir la cabecera del CSV (los nombres de las columnas)
fputcsv($output, array('ID', 'DNI', 'Nombre', 'Correo', 'Contraseña', 'Rol', 'Teléfono', 'Dirección', 'Localidad', 'Provincia', 'Fecha Creación'));

// Obtener todos los clientes
$clientes = $usuario->listarUsuarios(['usuario', 'administrador'], '', 'ASC');

// Escribir cada registro en el archivo CSV
foreach ($clientes as $cliente) {
    fputcsv($output, $cliente);
}

// Cerrar el flujo de salida
fclose($output);

