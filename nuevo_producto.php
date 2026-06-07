<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    header("Location: productos.php"); exit; 
}
require_once 'includes/conexion.php';

// Obtener categorías para el menú desplegable
$query = "SELECT id_categoria, nombre_categoria FROM categorias";
$stmt = $pdo->query($query);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f9;}
        .form-container { max-width: 500px; background: white; padding: 30px; border-radius: 8px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #28a745; color: white; padding: 10px; border: none; width: 100%; cursor: pointer; font-size: 16px; border-radius: 4px;}
        .btn-cancel { background: #dc3545; color: white; text-decoration: none; padding: 10px; display: block; text-align: center; margin-top: 10px; border-radius: 4px;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2>📦 Registrar Nuevo Producto</h2>
        <form action="guardar_producto.php" method="POST">
            
            <label>Código de Barras:</label>
            <input type="text" name="codigo_barras" required>

            <label>Nombre del Producto:</label>
            <input type="text" name="nombre" required>

            <label>Categoría:</label>
            <select name="id_categoria" required>
                <option value="">-- Seleccione --</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>"><?php echo $cat['nombre_categoria']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>Descripción del Producto:</label>
            <textarea name="descripcion" rows="3" placeholder="Ej: Core i5, 8GB RAM, 256GB SSD..."></textarea>
            
            <label>Precio de Venta ($):</label>
            <input type="number" step="0.01" name="precio_venta" required>

            <label>Stock Actual (Inicial):</label>
            <input type="number" name="stock_actual" required>

            <label>Stock Mínimo (Alerta):</label>
            <input type="number" name="stock_minimo" required>

            <button type="submit" class="btn-submit">💾 Guardar Producto</button>
            <a href="productos.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>