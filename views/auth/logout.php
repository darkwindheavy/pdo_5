<?php
require_once '../app/Controladores/config.php';

session_start();
session_unset();
session_destroy();
header("Location: " . BASE_URL . "index.php?page=auth/login");

exit;



