<?php
session_start();
require_once 'includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    die("No tienes permisos para realizar esta acción."); 
}

if (isset($_GET['id'])) {
    $id_proveedor = $_GET['id'];

    try {
        $sql = "UPDATE proveedores SET estado = 0 WHERE id_proveedor = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_proveedor]);

        header("Location: proveedores.php?msg=eliminado");
        exit;
    } catch (PDOException $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
}
?>