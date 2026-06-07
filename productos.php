<?php
session_start();

// Validar que el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) { 
    header("Location: login.php"); 
    exit; 
}
require_once 'includes/conexion.php';

// 1. Obtener todas las categorías para llenar el filtro desplegable
$stmtCat = $pdo->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// 2. Capturar los valores de búsqueda si el usuario envió el formulario
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoria_id = isset($_GET['categoria_id']) ? $_GET['categoria_id'] : '';

// 3. Construir la consulta SQL dinámica
$query = "SELECT p.id_producto, p.codigo_barras, p.nombre, p.descripcion, c.nombre_categoria, p.precio_venta, p.stock_actual, p.stock_minimo, p.estado 
          FROM productos p 
          INNER JOIN categorias c ON p.id_categoria = c.id_categoria
          WHERE 1=1";

$params = [];

// Si hay texto en el buscador (busca por nombre o código de barras)
if ($search !== '') {
    $query .= " AND (p.nombre LIKE ? OR p.codigo_barras LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Si se seleccionó una categoría específica
if ($categoria_id !== '') {
    $query .= " AND p.id_categoria = ?";
    $params[] = $categoria_id;
}

$query .= " ORDER BY p.nombre ASC";

// Ejecutar la consulta con los parámetros de los filtros
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensajes de éxito/error provenientes de otras páginas
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Santa Elena</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;}
        
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; display: inline-block;}
        .btn-nuevo { background: #28a745; font-weight: bold;}
        .btn-volver { background: #6c757d; font-weight: bold;}
        .btn-editar { background: #ffc107; color: #333; font-size: 13px; font-weight: bold;}
        .btn-eliminar { background: #dc3545; color: white; font-size: 13px; font-weight: bold;}
        
        /* Estilos de la Barra de Búsqueda y Filtros Moderna */
        .filter-bar { 
            background: #ffffff; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Grupo del Buscador (Input + Lupa) */
        .search-group {
            display: flex;
            flex-grow: 1;
            max-width: 500px;
        }
        .search-group input[type="text"] { 
            width: 100%; 
            padding: 10px 15px; 
            border: 1px solid #ccc; 
            border-right: none; /* Quitamos el borde para fusionarlo con el botón */
            border-radius: 4px 0 0 4px; 
            font-size: 14px;
            outline: none;
        }
        .search-group input[type="text"]:focus {
            border-color: #0056b3;
        }
        .btn-lupa { 
            background: #0056b3; 
            color: white; 
            padding: 10px 15px; 
            border: 1px solid #0056b3; 
            border-radius: 0 4px 4px 0; 
            cursor: pointer; 
            font-size: 16px;
        }
        .btn-lupa:hover { background: #004494; }

        /* Grupo de Filtros (Select + Limpiar) */
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-group select { 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            font-size: 14px;
            background-color: #f8f9fa;
            cursor: pointer;
            outline: none;
        }
        .filter-group select:focus {
            border-color: #0056b3;
        }
        .btn-limpiar { 
            background-color: #f8f9fa; 
            color: #dc3545; 
            padding: 10px 15px; 
            text-decoration: none; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s;
        }
        .btn-limpiar:hover { background-color: #e2e6ea; color: #c82333; }

        /* Estilos de la tabla */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px;}
        th, td { padding: 12px 10px; border-bottom: 1px solid #ddd; text-align: left;}
        th { background-color: #f8f9fa; }
        
        /* Alertas de mensajes */
        .alerta { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;}
        .alerta-info { background: #cce5ff; color: #004085; padding: 10px; margin-bottom: 15px; border-radius: 4px;}
        
        table td:nth-child(2), 
        table th:nth-child(2) {
            max-width: 140px;
            word-wrap: break-word;
            white-space: normal;
        }

        table td:nth-child(4), 
        table th:nth-child(4) {
            max-width: 140px;
            word-wrap: break-word;
            white-space: normal;
        }
        @media (max-width: 768px) {
        table td:nth-child(4),
        table th:nth-child(4) {
            display: none; /* oculta descripción en móvil */
            }
        }
        @media (max-width: 768px) {
            table td:nth-child(2),
            table th:nth-child(2) {
                display: none; /* oculta descripción en móvil */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="top-bar">
            <h2>📑 Catálogo de Productos</h2>
            <div>
                <a href="index.php" class="btn btn-volver">⬅ Volver al Menú</a>
                <?php if($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3): ?>
                    <a href="nuevo_producto.php" class="btn btn-nuevo">➕ Agregar Producto</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if($mensaje == 'creado') echo "<div class='alerta'>✅ Producto guardado exitosamente.</div>"; ?>
        <?php if($mensaje == 'eliminado') echo "<div class='alerta'>✅ Producto eliminado (inactivado).</div>"; ?>
        <?php if($mensaje == 'actualizado') echo "<div class='alerta-info'>🔄 Producto actualizado correctamente.</div>"; ?>

        <form method="GET" action="productos.php" class="filter-bar">
            
            <div class="search-group">
                <input type="text" name="search" placeholder="Buscar por nombre o código de barras..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-lupa" title="Buscar">🔍</button>
            </div>
            
            <div class="filter-group">
                <label style="font-size: 14px; color: #555; font-weight: bold;">Categoría:</label>
                <select name="categoria_id" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($categoria_id == $cat['id_categoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <?php if($search !== '' || $categoria_id !== ''): ?>
                    <a href="productos.php" class="btn-limpiar" title="Limpiar búsqueda y filtros">✖ Limpiar</a>
                <?php endif; ?>
            </div>

        </form>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($prod['codigo_barras']); ?></strong></td>
                            <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($prod['nombre_categoria']); ?></td>
                            <td><?php echo htmlspecialchars($prod['descripcion']); ?></td>
                            <td>$<?php echo number_format($prod['precio_venta'], 2); ?></td>
                            <td>
                                <?php 
                                    // Resaltar en rojo si está por debajo o igual al stock mínimo
                                    if ($prod['stock_actual'] <= $prod['stock_minimo']) {
                                        echo "<span style='color: red; font-weight: bold;'>" . $prod['stock_actual'] . "</span>";
                                    } else {
                                        echo $prod['stock_actual'];
                                    }
                                ?>
                            </td>
                            <td><?php echo $prod['estado'] ? '🟢 Activo' : '🔴 Inactivo'; ?></td>
                            <td>
                                <?php if($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3): ?>
                                    <a href="editar_producto.php?id=<?php echo $prod['id_producto']; ?>" class="btn btn-editar">✏️ Editar</a>
                                    
                                    <?php if($prod['estado'] == 1): ?>
                                        <a href="eliminar_producto.php?id=<?php echo $prod['id_producto']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Seguro que deseas inactivar este producto?');">🗑️ Eliminar</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color:#999; font-size:12px;">Solo lectura</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 40px; font-size: 16px; color: #666;">
                            No se encontraron productos que coincidan con la búsqueda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>