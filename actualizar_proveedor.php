<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_SESSION['id_rol']) && ($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3))) {
    $id_proveedor = $_POST['id_proveedor'];
    $empresa      = trim($_POST['nombre_empresa']);
    $contacto     = trim($_POST['contacto']);
    $telefono     = trim($_POST['telefono']);
    $correo       = trim($_POST['correo']);
    $direccion    = trim($_POST['direccion']);
    
    $reactivar = isset($_POST['reactivar']) ? 1 : 0;

    try {
        if ($reactivar == 1) {
            $sql = "UPDATE proveedores SET nombre_empresa = ?, contacto = ?, telefono = ?, correo = ?, direccion = ?, estado = 1 WHERE id_proveedor = ?";
        } else {
            $sql = "UPDATE proveedores SET nombre_empresa = ?, contacto = ?, telefono = ?, correo = ?, direccion = ? WHERE id_proveedor = ?";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empresa, $contacto, $telefono, $correo, $direccion, $id_proveedor]);

        header("Location: proveedores.php?msg=actualizado");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar el proveedor: " . $e->getMessage());
    }
} else {
    header("Location: proveedores.php");
}
?>