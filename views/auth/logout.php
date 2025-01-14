<?php
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión

// Redirigir al usuario a la página de inicio de sesión
header("Location: /PDO_5_MVC/public/index.php?page=auth/login");
exit;



