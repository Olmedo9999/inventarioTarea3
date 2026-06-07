<?php
session_start();
// Solo Admin (1) o Vendedor (4) pueden acceder a ventas
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 4)) { 
    header("Location: index.php"); exit; 
}
require_once 'includes/conexion.php';

// Obtener solo los productos activos que tengan stock disponible
$query = "SELECT id_producto, codigo_barras, nombre, precio_venta, stock_actual 
          FROM productos 
          WHERE estado = 1 AND stock_actual > 0 
          ORDER BY nombre ASC";
$stmtProd = $pdo->query($query);
$productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta - Mostrador</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f9;}
        .form-container { max-width: 600px; background: white; padding: 30px; border-radius: 8px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        /* Estilos para alinear el botón y el título en la parte superior */
        .header-form { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px;}
        .header-form h2 { margin: 0; font-size: 22px; color: #333;}
        .btn-volver-top { background: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: bold;}
        .btn-volver-top:hover { background: #5a6268; }

        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #17a2b8; color: white; padding: 12px; border: none; width: 100%; cursor: pointer; font-size: 16px; border-radius: 4px; font-weight: bold;}
        .btn-submit:hover { background: #138496; }
        
        /* Botón de cancelar ahora en rojo para consistencia */
        .btn-cancel { background: #dc3545; color: white; text-decoration: none; padding: 10px; display: block; text-align: center; margin-top: 10px; border-radius: 4px;}
        
        .alerta-exito { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;}
        .alerta-error { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;}
    </style>
</head>
<body>
    <div class="form-container">
        
        <div class="header-form">
            <h2>🛒 Registrar Nueva Venta</h2>
            <a href="index.php" class="btn-volver-top">⬅ Volver al Menú</a>
        </div>
        
        <?php if($mensaje == 'exito') echo "<div class='alerta-exito'>✅ Venta registrada. Stock descontado exitosamente.</div>"; ?>
        <?php if($mensaje == 'stock_insuficiente') echo "<div class='alerta-error'>⚠ Error: La cantidad solicitada supera el stock disponible.</div>"; ?>
        <?php if($mensaje == 'error') echo "<div class='alerta-error'>❌ Ocurrió un error al procesar la venta.</div>"; ?>

        <form action="guardar_venta.php" method="POST">
            
            <label>Seleccionar Producto:</label>
            <select name="id_producto" required>
                <option value="">-- Seleccione el Producto --</option>
                <?php foreach($productos as $prod): ?>
                    <option value="<?php echo $prod['id_producto']; ?>">
                        [<?php echo htmlspecialchars($prod['codigo_barras']); ?>] - <?php echo htmlspecialchars($prod['nombre']); ?> 
                        (Stock: <?php echo $prod['stock_actual']; ?> | Precio: $<?php echo number_format($prod['precio_venta'], 2); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Cantidad a Vender:</label>
            <input type="number" name="cantidad" min="1" required placeholder="Ej: 2">

            <label>Observaciones (Opcional):</label>
            <textarea name="observacion" rows="2" placeholder="Ej. Cliente solicitó factura con datos específicos..."></textarea>

            <button type="submit" class="btn-submit">Procesar Venta y Descontar Stock</button>
            <a href="index.php" class="btn-cancel">Cancelar Operación</a>
        </form>
    </div>
</body>
</html>