<?php
session_start();
require_once 'includes/conexion.php';

// Validar permisos (Solo Admin o Vendedor)
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 4)) { 
    die("No tienes permisos para realizar esta acción."); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_producto  = $_POST['id_producto'];
    $cantidad     = (int)$_POST['cantidad'];
    $observacion  = trim($_POST['observacion']);
    $id_usuario   = $_SESSION['id_usuario']; 

    try {
        $pdo->beginTransaction();

        // 1. Verificar que el producto exista y tenga stock suficiente
        $stmtVerificar = $pdo->prepare("SELECT precio_venta, stock_actual, nombre FROM productos WHERE id_producto = ?");
        $stmtVerificar->execute([$id_producto]);
        $producto = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

        if (!$producto || $producto['stock_actual'] < $cantidad) {
            // Si el cliente pide más de lo que hay, abortar transacción
            $pdo->rollBack();
            header("Location: nueva_venta.php?msg=stock_insuficiente");
            exit;
        }

        // Realizar cálculos de precios
        $precio_unitario = $producto['precio_venta'];
        $subtotal = $precio_unitario * $cantidad;
        $total = $subtotal; // Como es un solo producto por ahora, el subtotal es igual al total.

        // 2. Insertar la Cabecera en la tabla 'ventas'
        $stmtVenta = $pdo->prepare("INSERT INTO ventas (id_usuario, total, observacion) VALUES (?, ?, ?)");
        $stmtVenta->execute([$id_usuario, $total, $observacion]);
        $id_venta_generada = $pdo->lastInsertId();

        // 3. Insertar en la tabla 'detalle_venta'
        $stmtDetalle = $pdo->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtDetalle->execute([$id_venta_generada, $id_producto, $cantidad, $precio_unitario, $subtotal]);

        // 4. Actualizar (Descontar) el Stock en la tabla 'productos'
        $stmtUpdateStock = $pdo->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?");
        $stmtUpdateStock->execute([$cantidad, $id_producto]);

        // 5. Registrar en la tabla 'movimientos' (Historial general)
        $desc_movimiento = "Venta mostrador. Ref: Ticket #" . $id_venta_generada;
        $stmtMovimiento = $pdo->prepare("INSERT INTO movimientos (id_producto, id_usuario, tipo_movimiento, cantidad, descripcion) VALUES (?, ?, 'SALIDA', ?, ?)");
        $stmtMovimiento->execute([$id_producto, $id_usuario, $cantidad, $desc_movimiento]);

        // Guardar todo de forma definitiva
        $pdo->commit();

        header("Location: nueva_venta.php?msg=exito");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: nueva_venta.php?msg=error");
        exit;
    }
} else {
    header("Location: nueva_venta.php");
}
?>