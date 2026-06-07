<?php
session_start();
require_once 'includes/conexion.php';

// Verificar permisos
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    die("No tienes permisos para realizar esta acción."); 
}

// Verificar que se haya enviado un ID
if (isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    try {
        // Borrado Lógico: Cambiamos el estado a 0 (Falso/Inactivo)
        $sql = "UPDATE productos SET estado = 0 WHERE id_producto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_producto]);

        // Redirigir con mensaje de éxito
        header("Location: productos.php?msg=eliminado");
        exit;
        
    } catch (PDOException $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
}
?>