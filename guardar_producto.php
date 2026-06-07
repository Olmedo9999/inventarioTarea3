<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_SESSION['id_rol']) && ($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3))) {
    $codigo_barras = trim($_POST['codigo_barras']);
    $nombre        = trim($_POST['nombre']);
    $descripcion   = trim($_POST['descripcion']); // NUEVO CAMPO
    $id_categoria  = $_POST['id_categoria'];
    $precio_venta  = $_POST['precio_venta'];
    $stock_minimo  = $_POST['stock_minimo'];
    $stock_actual  = $_POST['stock_actual']; // Stock inicial

    try {
        $sql = "INSERT INTO productos (codigo_barras, nombre, descripcion, id_categoria, precio_venta, stock_minimo, stock_actual, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$codigo_barras, $nombre, $descripcion, $id_categoria, $precio_venta, $stock_minimo, $stock_actual]);

        header("Location: productos.php?msg=creado");
        exit;
    } catch (PDOException $e) {
        die("Error al guardar el producto: " . $e->getMessage());
    }
} else {
    header("Location: productos.php");
}
?>