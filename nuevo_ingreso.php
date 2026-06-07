<?php
session_start();
// Solo Admin (1) o Jefe de Bodega (2) pueden acceder
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) { 
    header("Location: index.php"); exit; 
}
require_once 'includes/conexion.php';

// Obtener proveedores activos
$stmtProv = $pdo->query("SELECT id_proveedor, nombre_empresa FROM proveedores WHERE estado = 1");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos activos
$stmtProd = $pdo->query("SELECT id_producto, nombre, codigo_barras FROM productos WHERE estado = 1");
$productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Ingreso - Bodega</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f9;}
        .form-container { max-width: 600px; background: white; padding: 30px; border-radius: 8px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        /* Estilos para alinear el botón y el título en la parte superior */
        .header-form { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px;}
        .header-form h2 { margin: 0; font-size: 22px; color: #333;}
        .btn-volver-top { background: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: bold;}
        .btn-volver-top:hover { background: #5a6268; }

        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #0056b3; color: white; padding: 12px; border: none; width: 100%; cursor: pointer; font-size: 16px; border-radius: 4px; font-weight: bold;}
        .btn-submit:hover { background: #004494; }
        .btn-cancel { background: #dc3545; color: white; text-decoration: none; padding: 10px; display: block; text-align: center; margin-top: 10px; border-radius: 4px;}
        .alerta { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;}
    </style>
</head>
<body>
    <div class="form-container">
        
        <div class="header-form">
            <h2>📥 Registrar Ingreso</h2>
            <a href="index.php" class="btn-volver-top">⬅ Volver al Menú</a>
        </div>

        <?php if($mensaje == 'exito') echo "<div class='alerta'>✅ Ingreso registrado y stock actualizado.</div>"; ?>
        <?php if($mensaje == 'error') echo "<div class='alerta' style='background:#f8d7da; color:#721c24;'>❌ Ocurrió un error al registrar el ingreso.</div>"; ?>

        <form action="guardar_ingreso.php" method="POST">
            
            <label>Proveedor:</label>
            <select name="id_proveedor" required>
                <option value="">-- Seleccione el Proveedor --</option>
                <?php foreach($proveedores as $prov): ?>
                    <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo htmlspecialchars($prov['nombre_empresa']); ?></option>
                <?php endforeach; ?>
            </select>

            <label>Producto a Ingresar:</label>
            <select name="id_producto" required>
                <option value="">-- Seleccione el Producto --</option>
                <?php foreach($productos as $prod): ?>
                    <option value="<?php echo $prod['id_producto']; ?>">[<?php echo htmlspecialchars($prod['codigo_barras']); ?>] - <?php echo htmlspecialchars($prod['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <label>Cantidad (Unidades):</label>
            <input type="number" name="cantidad" min="1" required>

            <label>Precio de Costo Unitario ($):</label>
            <input type="number" step="0.01" name="precio_costo" min="0" required>

            <label>Observaciones (Opcional):</label>
            <textarea name="observacion" rows="3" placeholder="Ej. Lote entregado por transporte externo..."></textarea>

            <button type="submit" class="btn-submit">Registrar Ingreso y Actualizar Stock</button>
            <a href="index.php" class="btn-cancel">Cancelar Operación</a>
        </form>
    </div>
</body>
</html>