<?php
// includes/conexion.php
$host = '127.0.0.1';
$dbname = 'db_inventario';
$user = 'root'; // Usuario por defecto en XAMPP
$pass = '';     // Contraseña por defecto en XAMPP (suele estar vacía)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
?>