<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    header("Location: proveedores.php"); exit; 
}
require_once 'includes/conexion.php';

if (!isset($_GET['id'])) { header("Location: proveedores.php"); exit; }

$id_proveedor = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
$stmt->execute([$id_proveedor]);
$proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proveedor) { header("Location: proveedores.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proveedor</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f9;}
        .form-container { max-width: 500px; background: white; padding: 30px; border-radius: 8px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #ffc107; color: #333; padding: 10px; border: none; width: 100%; cursor: pointer; font-size: 16px; border-radius: 4px; font-weight: bold;}
        .btn-cancel { background: #6c757d; color: white; text-decoration: none; padding: 10px; display: block; text-align: center; margin-top: 10px; border-radius: 4px;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2>✏️ Editar Proveedor</h2>
        <form action="actualizar_proveedor.php" method="POST">
            <input type="hidden" name="id_proveedor" value="<?php echo $proveedor['id_proveedor']; ?>">
            
            <label>Nombre de la Empresa:</label>
            <input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($proveedor['nombre_empresa']); ?>" required>

            <label>Nombre del Contacto:</label>
            <input type="text" name="contacto" value="<?php echo htmlspecialchars($proveedor['contacto']); ?>">

            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($proveedor['telefono']); ?>">

            <label>Correo Electrónico:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($proveedor['correo']); ?>">

            <label>Dirección:</label>
            <input type="text" name="direccion" value="<?php echo htmlspecialchars($proveedor['direccion']); ?>">

            <?php if ($proveedor['estado'] == 0): ?>
                <div style="background: #ffeeba; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ffdf7e;">
                    <label style="color: #856404; font-weight: bold; cursor: pointer;">
                        <input type="checkbox" name="reactivar" value="1"> 
                        ☑️ Este proveedor está Inactivo. Marca esta casilla si deseas REACTIVARLO.
                    </label>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">🔄 Actualizar Proveedor</button>
            <a href="proveedores.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>