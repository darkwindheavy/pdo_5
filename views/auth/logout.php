<?php
session_start();
session_unset();
session_destroy();
header("Location: /PDO_5_MVC/public/index.php?page=auth/login");
exit;



