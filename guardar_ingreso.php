<?php
session_start();
require_once 'includes/conexion.php';

// Validar que el usuario tenga sesión y permisos
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) { 
    die("No tienes permisos para realizar esta acción."); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $id_proveedor = $_POST['id_proveedor'];
    $id_producto  = $_POST['id_producto'];
    $cantidad     = (int)$_POST['cantidad'];
    $precio_costo = (float)$_POST['precio_costo'];
    $observacion  = trim($_POST['observacion']);
    $id_usuario   = $_SESSION['id_usuario']; // Quien registra

    try {
        // INICIAR LA TRANSACCIÓN: Todo o nada
        $pdo->beginTransaction();

        // 1. Insertar la Cabecera en la tabla 'ingresos'
        $stmtIngreso = $pdo->prepare("INSERT INTO ingresos (id_proveedor, id_usuario, observacion) VALUES (?, ?, ?)");
        $stmtIngreso->execute([$id_proveedor, $id_usuario, $observacion]);
        
        // Obtener el ID del ingreso que se acaba de crear
        $id_ingreso_generado = $pdo->lastInsertId();

        // 2. Insertar en la tabla 'detalle_ingreso'
        $stmtDetalle = $pdo->prepare("INSERT INTO detalle_ingreso (id_ingreso, id_producto, cantidad, precio_costo) VALUES (?, ?, ?, ?)");
        $stmtDetalle->execute([$id_ingreso_generado, $id_producto, $cantidad, $precio_costo]);

        // 3. Actualizar el Stock en la tabla 'productos' (Se suma la cantidad)
        $stmtUpdateStock = $pdo->prepare("UPDATE productos SET stock_actual = stock_actual + ? WHERE id_producto = ?");
        $stmtUpdateStock->execute([$cantidad, $id_producto]);

        // 4. Registrar en la tabla 'movimientos' para el historial general
        $desc_movimiento = "Ingreso de bodega. Ref: Ingreso #" . $id_ingreso_generado;
        $stmtMovimiento = $pdo->prepare("INSERT INTO movimientos (id_producto, id_usuario, tipo_movimiento, cantidad, descripcion) VALUES (?, ?, 'INGRESO', ?, ?)");
        $stmtMovimiento->execute([$id_producto, $id_usuario, $cantidad, $desc_movimiento]);

        // CONFIRMAR TRANSACCIÓN: Si todo salió bien, guardamos en la base de datos
        $pdo->commit();

        // Redirigir con éxito
        header("Location: nuevo_ingreso.php?msg=exito");
        exit;

    } catch (Exception $e) {
        // REVERTIR TRANSACCIÓN: Si hubo algún error, deshacemos todos los cambios
        $pdo->rollBack();
        // Puedes cambiar esto a un die() temporalmente si quieres ver exactamente qué error de DB ocurre
        header("Location: nuevo_ingreso.php?msg=error");
        exit;
    }
} else {
    header("Location: nuevo_ingreso.php");
}
?>