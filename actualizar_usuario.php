<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['id_rol'] == 1) {
    $id       = $_POST['id_usuario'];
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo   = trim($_POST['correo']);
    $id_rol   = $_POST['id_rol'];
    $reactivar = isset($_POST['reactivar']) ? ", estado = 1" : "";

    // Si hay una nueva contraseña, actualizamos el campo
    if (!empty($_POST['contrasena'])) {
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, correo=?, id_rol=?, contrasena=? $reactivar WHERE id_usuario=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $correo, $id_rol, $_POST['contrasena'], $id]);
    } else {
        // Si no hay contraseña nueva, actualizamos todo excepto la contraseña
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, correo=?, id_rol=? $reactivar WHERE id_usuario=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $correo, $id_rol, $id]);
    }

    header("Location: usuarios.php");
    exit;
}
?>