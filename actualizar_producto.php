<?php
session_start();
require_once 'includes/conexion.php';

// Validar que el usuario tenga permisos (Seguridad)
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    die("No tienes permisos para realizar esta acción."); 
}

// Validar que los datos vengan por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $id_producto  = $_POST['id_producto'];
    $codigo       = trim($_POST['codigo_barras']);
    $nombre       = trim($_POST['nombre']);
    $descripcion  = trim($_POST['descripcion']); // NUEVO CAMPO AÑADIDO
    $categoria    = $_POST['id_categoria'];
    $precio       = $_POST['precio_venta'];
    
    // Recibimos el stock actual y el stock mínimo
    $stock_actual = $_POST['stock_actual']; 
    $stock_minimo = $_POST['stock_minimo'];
    
    // Verificar si se marcó la casilla de reactivar
    $reactivar = isset($_POST['reactivar']) ? 1 : 0;

    try {
        if ($reactivar == 1) {
            // Si se marcó reactivar, actualizamos los datos, la descripción, el stock actual y el estado a 1
            $sql = "UPDATE productos 
                    SET codigo_barras = ?, nombre = ?, descripcion = ?, id_categoria = ?, precio_venta = ?, stock_actual = ?, stock_minimo = ?, estado = 1 
                    WHERE id_producto = ?";
        } else {
            // Si NO se marcó, actualizamos todo (incluida la descripción y el stock) sin tocar el estado
            $sql = "UPDATE productos 
                    SET codigo_barras = ?, nombre = ?, descripcion = ?, id_categoria = ?, precio_venta = ?, stock_actual = ?, stock_minimo = ? 
                    WHERE id_producto = ?";
        }
                
        $stmt = $pdo->prepare($sql);
        // ATENCIÓN: El orden en el array debe coincidir exactamente con los signos de interrogación (?) del SQL
        $stmt->execute([$codigo, $nombre, $descripcion, $categoria, $precio, $stock_actual, $stock_minimo, $id_producto]);

        // Redirigir de vuelta a la lista de productos con un mensaje de éxito
        header("Location: productos.php?msg=actualizado");
        exit;
        
    } catch (PDOException $e) {
        die("Error al actualizar el producto: " . $e->getMessage());
    }
} else {
    header("Location: productos.php");
    exit;
}
?>