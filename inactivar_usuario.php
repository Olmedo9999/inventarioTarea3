<?php
session_start();
require_once 'includes/conexion.php';
if ($_SESSION['id_rol'] == 1 && isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE usuarios SET estado = 0 WHERE id_usuario = ?");
    $stmt->execute([$_GET['id']]);
}
header("Location: usuarios.php");
?>