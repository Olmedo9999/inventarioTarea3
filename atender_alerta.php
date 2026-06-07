<?php
session_start();
require_once 'includes/conexion.php';

// Validar seguridad
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    die("Acceso denegado."); 
}

if (isset($_GET['id'])) {
    $id_alerta = $_GET['id'];

    try {
        // Cambiar el estado de PENDIENTE a ATENDIDA
        $sql = "UPDATE alertas SET estado = 'ATENDIDA' WHERE id_alerta = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_alerta]);

        // Regresar a la pantalla de alertas
        header("Location: alertas.php?msg=atendida");
        exit;
        
    } catch (PDOException $e) {
        die("Error al actualizar la alerta: " . $e->getMessage());
    }
} else {
    header("Location: alertas.php");
}
?>