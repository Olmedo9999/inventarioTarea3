<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit; }
require_once 'includes/conexion.php';

// Obtener la lista de proveedores
$query = "SELECT * FROM proveedores ORDER BY nombre_empresa ASC";
$stmt = $pdo->query($query);
$proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Capturar mensajes de éxito
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores - Santa Elena</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; display: inline-block;}
        .btn-nuevo { background: #28a745; }
        .btn-volver { background: #6c757d; }
        .btn-editar { background: #ffc107; color: #333; font-size: 13px; }
        .btn-eliminar { background: #dc3545; color: white; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;}
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left;}
        .alerta { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold;}
        .alerta-creado { background: #d4edda; color: #155724; }
        .alerta-actualizado { background: #cce5ff; color: #004085; }
        .alerta-eliminado { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h2>🏢 Catálogo de Proveedores</h2>
            <div>
                <a href="index.php" class="btn btn-volver">⬅ Volver al Menú</a>
                <?php if($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3): ?>
                    <a href="nuevo_proveedor.php" class="btn btn-nuevo">➕ Agregar Proveedor</a>
                <?php endif; ?>
            </div>
        </div>

        <?php 
        if($mensaje == 'creado') echo "<div class='alerta alerta-creado'>✅ Proveedor registrado exitosamente.</div>"; 
        if($mensaje == 'actualizado') echo "<div class='alerta alerta-actualizado'>🔄 Proveedor actualizado correctamente.</div>"; 
        if($mensaje == 'eliminado') echo "<div class='alerta alerta-eliminado'>🗑️ Proveedor eliminado (inactivado).</div>"; 
        ?>

        <table>
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($proveedores) > 0): ?>
                    <?php foreach ($proveedores as $prov): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($prov['nombre_empresa']); ?></strong></td>
                            <td><?php echo htmlspecialchars($prov['contacto']); ?></td>
                            <td><?php echo htmlspecialchars($prov['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($prov['correo']); ?></td>
                            <td><?php echo $prov['estado'] ? '🟢 Activo' : '🔴 Inactivo'; ?></td>
                            <td>
                                <?php if($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3): ?>
                                    <a href="editar_proveedor.php?id=<?php echo $prov['id_proveedor']; ?>" class="btn btn-editar">✏️ Editar</a>
                                    <?php if($prov['estado'] == 1): ?>
                                        <a href="eliminar_proveedor.php?id=<?php echo $prov['id_proveedor']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Seguro que deseas inactivar a este proveedor?');">🗑️ Eliminar</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #999;">Solo lectura</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No hay proveedores registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>