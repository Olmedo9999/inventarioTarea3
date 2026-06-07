<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_SESSION['id_rol']) && ($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3))) {
    $empresa   = trim($_POST['nombre_empresa']);
    $contacto  = trim($_POST['contacto']);
    $telefono  = trim($_POST['telefono']);
    $correo    = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);

    try {
        $sql = "INSERT INTO proveedores (nombre_empresa, contacto, telefono, correo, direccion, estado) 
                VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empresa, $contacto, $telefono, $correo, $direccion]);

        header("Location: proveedores.php?msg=creado");
        exit;
    } catch (PDOException $e) {
        die("Error al guardar el proveedor: " . $e->getMessage());
    }
} else {
    header("Location: proveedores.php");
}
?>