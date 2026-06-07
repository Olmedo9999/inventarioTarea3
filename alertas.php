<?php
session_start();

// Solo Administrador (1) y Gerente de Compras (3) pueden gestionar alertas
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    header("Location: index.php"); 
    exit; 
}
require_once 'includes/conexion.php';

try {
    // 1. LÓGICA DE AUTOGENERACIÓN DE ALERTAS (RF-06)
    // Buscamos productos activos cuyo stock haya caído a nivel mínimo o inferior
    $sqlLowStock = "SELECT id_producto, nombre, stock_actual, stock_minimo FROM productos 
                    WHERE estado = 1 AND stock_actual <= stock_minimo";
    $stmtLow = $pdo->query($sqlLowStock);
    $productosBajos = $stmtLow->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productosBajos as $prod) {
        // Verificar si ya existe una alerta 'PENDIENTE' para este producto para no duplicar
        $stmtCheck = $pdo->prepare("SELECT id_alerta FROM alertas WHERE id_producto = ? AND estado = 'PENDIENTE'");
        $stmtCheck->execute([$prod['id_producto']]);
        
        if ($stmtCheck->rowCount() == 0) {
            // Si no existe, creamos la alerta automáticamente
            $mensaje = "Stock crítico para {$prod['nombre']}. Quedan {$prod['stock_actual']} (Mínimo: {$prod['stock_minimo']}).";
            $stmtInsert = $pdo->prepare("INSERT INTO alertas (id_producto, mensaje, estado) VALUES (?, ?, 'PENDIENTE')");
            $stmtInsert->execute([$prod['id_producto'], $mensaje]);
        }
    }

    // 2. OBTENER LAS ALERTAS PENDIENTES PARA MOSTRARLAS
    $queryAlertas = "SELECT a.id_alerta, a.fecha_alerta, a.mensaje, p.codigo_barras, p.nombre 
                     FROM alertas a
                     INNER JOIN productos p ON a.id_producto = p.id_producto
                     WHERE a.estado = 'PENDIENTE'
                     ORDER BY a.fecha_alerta DESC";
    $stmtAlertas = $pdo->query($queryAlertas);
    $alertas = $stmtAlertas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al procesar alertas: " . $e->getMessage());
}

$mensaje_exito = isset($_GET['msg']) && $_GET['msg'] == 'atendida' ? true : false;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alertas de Stock - Santa Elena</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .btn-volver { background: #6c757d; padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white;}
        
        /* === CORRECCIÓN DEL BOTÓN AQUÍ === */
        .btn-atender { 
            background: #28a745; 
            color: white; 
            padding: 8px 15px; /* Un poco más de padding para que respire */
            text-decoration: none; 
            border-radius: 4px; 
            font-size: 13px; 
            font-weight: bold;
            display: inline-block; /* La magia que lo convierte en un bloque sólido */
            white-space: nowrap; /* Evita que el texto se rompa en varias líneas */
            box-shadow: 0 2px 4px rgba(0,0,0,0.15); /* Una pequeña sombra para darle volumen */
            transition: background 0.3s, transform 0.2s; /* Animación suave */
        }
        .btn-atender:hover {
            background: #218838; /* Verde más oscuro al pasar el mouse */
            transform: translateY(-2px); /* Se levanta un poquito al pasar el mouse */
        }
        /* ================================== */
        
        table { width: 100%; border-collapse: collapse; font-size: 14px;}
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: middle;}
        th { background-color: #dc3545; color: white; } 
        
        .alerta-box { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;}
        .icono-alerta { font-size: 20px; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h2 style="color: #dc3545;">⚠️ Alertas de Reposición de Stock</h2>
            <a href="index.php" class="btn-volver">⬅ Volver al Menú</a>
        </div>

        <?php if($mensaje_exito): ?>
            <div class="alerta-box">✅ Alerta marcada como "ATENDIDA".</div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Fecha de Alerta</th>
                    <th>Producto</th>
                    <th>Mensaje del Sistema</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($alertas) > 0): ?>
                    <?php foreach ($alertas as $alerta): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($alerta['fecha_alerta'])); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($alerta['codigo_barras']); ?></strong><br>
                                <?php echo htmlspecialchars($alerta['nombre']); ?>
                            </td>
                            <td style="color: #dc3545; font-weight: bold;">
                                <span class="icono-alerta">📉</span> <?php echo htmlspecialchars($alerta['mensaje']); ?>
                            </td>
                            <td>
                                <a href="atender_alerta.php?id=<?php echo $alerta['id_alerta']; ?>" class="btn-atender" onclick="return confirm('¿Confirmas que ya solicitaste stock para este producto?');">✓ Marcar Como Atendida</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding: 30px; font-size: 16px; color: #28a745;"><strong>🎉 ¡Todo en orden! No hay alertas de stock bajo en este momento.</strong></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>