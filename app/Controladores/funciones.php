<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función para manejar errores
function manejar_error($mensaje) {
    echo "<script>alert('$mensaje'); window.location.href='login.php';</script>";
    exit;
}

// Función para verificar sesión y rol
function verificar_sesion_y_rol($roles) {
    // Verificar si la sesión está iniciada y si el rol es válido
    if (!isset($_SESSION['usuario_id']) || !in_array(strtolower(trim($_SESSION['usuario_rol'])), array_map('strtolower', $roles))) {
        echo "Error: No tienes el rol necesario para acceder a esta página."; // Para depurar
        exit;
        // header("Location: login.php");
        // exit;
    }
}




// Verificar si el usuario es editor
function es_editor() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'editor';
}

// Verificar acceso de editor y redirigir si no tiene permiso
function verificar_acceso_editor() {
    if (!es_editor()) {
        header("Location: no_autorizado.php");
        exit;
    }
}


