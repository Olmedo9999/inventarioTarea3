<?php
// index.php
session_start();

// Validar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/conexion.php';

// Obtener los productos y sus categorías para la tabla principal
$query = "SELECT p.id_producto, p.codigo_barras, p.nombre, c.nombre_categoria, p.precio_venta, p.stock_actual, p.stock_minimo 
          FROM productos p 
          INNER JOIN categorias c ON p.id_categoria = c.id_categoria
          WHERE p.estado = 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determinar el nombre del rol según el ID
$roles = [
    1 => 'Administrador',
    2 => 'Jefe de Bodega',
    3 => 'Gerente de Compras',
    4 => 'Vendedor'
];
$rol_actual = $roles[$_SESSION['id_rol']] ?? 'Usuario';

// Consultar cuántas alertas pendientes hay (Solo para Admin y Compras)
$alertas_pendientes = 0;
if ($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3) {
    $stmtAlertas = $pdo->query("SELECT COUNT(*) FROM alertas WHERE estado = 'PENDIENTE'");
    $alertas_pendientes = $stmtAlertas->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - Inventario Santa Elena</title>
    <style>
        /* Estilos Generales */
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .header { background-color: #0056b3; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;}
        .header h2 { margin: 0; font-size: 20px; }
        .user-info { font-size: 14px; display: flex; align-items: center;}
        .btn-logout { background-color: #dc3545; color: white; text-decoration: none; padding: 8px 12px; border-radius: 4px; font-weight: bold; margin-left: 15px;}
        .btn-logout:hover { background-color: #c82333; }
        
        .container { max-width: 1100px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        /* Sistema de Cuadrícula Responsiva para los Botones */
        .acciones { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .grid-botones {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        /* Estilos de los Botones del Menú */
        .btn-menu {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            text-align: center;
            transition: transform 0.2s, filter 0.2s;
            position: relative;
        }
        .btn-menu:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }
        .btn-menu span { font-size: 24px; margin-bottom: 5px; } /* Tamaño del Emoji/Icono */

        /* Colores por módulo */
        .bg-productos { background: #6f42c1; }
        .bg-proveedores { background: #fd7e14; }
        .bg-bodega { background: #28a745; }
        .bg-ventas { background: #17a2b8; }
        .bg-historial { background: #343a40; }
        .bg-alertas { background: #dc3545; }

        /* Burbuja de Notificación */
        .burbuja {
            background-color: white;
            color: #dc3545;
            border: 2px solid #dc3545;
            font-size: 12px;
            font-weight: 900;
            padding: 4px 8px;
            border-radius: 50%;
            position: absolute;
            top: -10px;
            right: -10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Estilos de la Tabla */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #333; }
        .alerta-stock { color: #dc3545; font-weight: bold; background: #ffeeba; padding: 3px 6px; border-radius: 3px;}
        
        /* Hacer la tabla responsiva */
        .table-responsive { overflow-x: auto; }
    </style>
</head>
<body>

    <div class="header">
        <h2>💻 Sistema de Inventario - Santa Elena</h2>
        <div class="user-info">
            Bienvenido, &nbsp;<strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>&nbsp; 
            (<?php echo $rol_actual; ?>)
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        
        <div class="acciones">
            <h3 style="margin-top:0;"> Panel de Control</h3>
            <div class="grid-botones">
                
                <?php if($_SESSION['id_rol'] == 1): ?>
                    <a href="usuarios.php" class="btn-menu" style="background: #5c6fcd;">
                        <span>👥</span> Gestión de Usuarios
                    </a>
                <?php endif; ?>

                <a href="productos.php" class="btn-menu bg-productos">
                    <span>📑</span> Catálogo de Productos
                </a>

                <a href="proveedores.php" class="btn-menu bg-proveedores">
                    <span>🏢</span> Directorio Proveedores
                </a>

                <?php if($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 2): ?>
                    <a href="nuevo_ingreso.php" class="btn-menu bg-bodega">
                        <span>📥</span> Registrar Ingreso
                    </a>
                <?php endif; ?>
                
                <?php if($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 4): ?>
                    <a href="nueva_venta.php" class="btn-menu bg-ventas">
                        <span>🛒</span> Nueva Venta
                    </a>
                <?php endif; ?>

                
                <?php if($_SESSION['id_rol'] != 4): ?>
                    <a href="historial.php" class="btn-menu bg-historial">
                        <span>📖</span> Historial Movimientos
                    </a>
                <?php endif; ?>

                <?php if($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3): ?>
                    <a href="alertas.php" class="btn-menu bg-alertas">
                        <span>⚠️</span> Alertas de Stock
                        <?php if($alertas_pendientes > 0): ?>
                            <div class="burbuja"><?php echo $alertas_pendientes; ?></div>
                        <?php endif; ?>
                
                    </a>
                <?php endif; ?>

            </div>
        </div>
        
        <h3>📊 Resumen de Disponibilidad</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio Venta</th>
                        <th>Stock Actual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($productos) > 0): ?>
                        <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($prod['codigo_barras']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($prod['nombre_categoria']); ?></td>
                                <td>$<?php echo number_format($prod['precio_venta'], 2); ?></td>
                                <td>
                                    <?php 
                                    if ($prod['stock_actual'] <= $prod['stock_minimo']) {
                                        echo "<span class='alerta-stock'>" . $prod['stock_actual'] . " (¡Bajo!) ⚠️</span>";
                                    } else {
                                        echo "<span style='color:green; font-weight:bold;'>" . $prod['stock_actual'] . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 20px;">No hay productos activos en el sistema.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>