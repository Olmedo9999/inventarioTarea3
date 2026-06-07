<?php
session_start();

// Validar que el usuario tenga sesión y permisos (Solo Admin o Compras)
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    header("Location: productos.php"); 
    exit; 
}

require_once 'includes/conexion.php';

// Validar que se haya enviado un ID por la URL
if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$id_producto = $_GET['id'];

// Obtener los datos actuales del producto
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el producto no existe, regresar a la lista
if (!$producto) {
    header("Location: productos.php");
    exit;
}

// Obtener todas las categorías para pintar el menú desplegable
$queryCat = "SELECT id_categoria, nombre_categoria FROM categorias";
$stmtCat = $pdo->query($queryCat);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f9;}
        .form-container { max-width: 500px; background: white; padding: 30px; border-radius: 8px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #ffc107; color: #333; padding: 10px; border: none; width: 100%; cursor: pointer; font-size: 16px; border-radius: 4px; font-weight: bold;}
        .btn-submit:hover { background: #e0a800; }
        .btn-cancel { background: #6c757d; color: white; text-decoration: none; padding: 10px; display: block; text-align: center; margin-top: 10px; border-radius: 4px;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2>✏️ Editar Producto</h2>
        <form action="actualizar_producto.php" method="POST">
            
            <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
            
            <label>Código de Barras:</label>
            <input type="text" name="codigo_barras" value="<?php echo htmlspecialchars($producto['codigo_barras']); ?>" required>

            <label>Nombre del Producto:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

            <label>Categoría:</label>
            <select name="id_categoria" required>
                <option value="">-- Seleccione --</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>" 
                        <?php echo ($cat['id_categoria'] == $producto['id_categoria']) ? 'selected' : ''; ?>>
                        <?php echo $cat['nombre_categoria']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label>Descripción del Producto:</label>
            <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            
            <label>Precio de Venta ($):</label>
            <input type="number" step="0.01" name="precio_venta" value="<?php echo $producto['precio_venta']; ?>" required>

            <label>Stock Actual:</label>
            <input type="number" name="stock_actual" value="<?php echo $producto['stock_actual']; ?>" required>
            
            <label>Stock Mínimo (Alerta):</label>
            <input type="number" name="stock_minimo" value="<?php echo $producto['stock_minimo']; ?>" required>

            <?php if ($producto['estado'] == 0): ?>
                <div style="background: #ffeeba; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ffdf7e;">
                    <label style="color: #856404; font-weight: bold; cursor: pointer;">
                        <input type="checkbox" name="reactivar" value="1"> 
                        ☑️ Este producto está Inactivo. Marca esta casilla si deseas REACTIVARLO.
                    </label>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">🔄 Actualizar Producto</button>
            <a href="productos.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>