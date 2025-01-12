<?php
require_once '../includes/funciones.php';

// DNI correctos
$dnis_correctos = ['12345678Z', '87654321H'];
foreach ($dnis_correctos as $dni) {
    echo "Probando $dni: " . (validar_dni($dni) ? "Válido" : "Inválido") . "<br>";
}

// DNI incorrectos
$dnis_incorrectos = ['12345678A', '87654321B', '00000000T'];
foreach ($dnis_incorrectos as $dni) {
    echo "Probando $dni: " . (validar_dni($dni) ? "Válido" : "Inválido") . "<br>";
}

