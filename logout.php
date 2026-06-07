<?php
// logout.php
session_start();
session_unset(); // Libera todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirigir al usuario de vuelta al login
header("Location: login.php");
exit;
?>