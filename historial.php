<?php
session_start();

// Validar que el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) { 
    header("Location: login.php"); 
    exit; 
}
require_once 'includes/conexion.php';

// Consulta SQL con INNER JOIN para traer los datos completos
$query = "SELECT m.id_movimiento, p.codigo_barras, p.nombre AS nombre_producto, 
                 CONCAT(u.nombre, ' ', u.apellido) AS nombre_usuario, 
                 m.tipo_movimiento, m.cantidad, m.fecha_movimiento, m.descripcion 
          FROM movimientos m
          INNER JOIN productos p ON m.id_producto = p.id_producto
          INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
          ORDER BY m.fecha_movimiento DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos - Santa Elena</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .btn-volver { background: #6c757d; padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; display: inline-block;}
        .btn-volver:hover { background: #5a6268; }
        
        table { width: 100%; border-collapse: collapse; font-size: 14px;}
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left;}
        th { background-color: #f8f9fa; color: #333; }
        
        /* Etiquetas de colores para identificar rápido si entró o salió mercancía */
        .badge-ingreso { background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;}
        .badge-salida { background-color: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h2>📖 Historial General de Movimientos</h2>
            <a href="index.php" class="btn-volver">⬅ Volver al Menú</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Tipo</th>
                    <th>Producto (Código)</th>
                    <th>Cantidad</th>
                    <th>Usuario Responsable</th>
                    <th>Descripción / Ref.</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($movimientos) > 0): ?>
                    <?php foreach ($movimientos as $mov): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($mov['fecha_movimiento'])); ?></td>
                            <td>
                                <?php if($mov['tipo_movimiento'] == 'INGRESO'): ?>
                                    <span class="badge-ingreso">📥 INGRESO</span>
                                <?php else: ?>
                                    <span class="badge-salida">🛒 SALIDA</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($mov['codigo_barras']); ?></strong><br>
                                <span style="color: #666; font-size: 12px;"><?php echo htmlspecialchars($mov['nombre_producto']); ?></span>
                            </td>
                            <td style="font-weight: bold; font-size: 16px;">
                                <?php 
                                    // Mostrar un "+" o un "-" dependiendo del tipo de movimiento
                                    echo ($mov['tipo_movimiento'] == 'INGRESO' ? '+' : '-') . $mov['cantidad']; 
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($mov['nombre_usuario']); ?></td>
                            <td style="font-style: italic; color: #555;"><?php echo htmlspecialchars($mov['descripcion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding: 20px;">No hay movimientos registrados en el sistema.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>