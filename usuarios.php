<?php
session_start();
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) { header("Location: index.php"); exit; }
require_once 'includes/conexion.php';

$query = "SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol ORDER BY u.id_usuario DESC";
$usuarios = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="container">
    <div class="top-bar">
        <h2>👥 Gestión de Usuarios</h2>
        <a href="index.php" class="btn btn-volver">⬅ Volver</a>
    </div>
    <a href="nuevo_usuario.php" class="btn btn-nuevo">➕ Agregar Usuario</a>
    <table>
        <thead>
            <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?></td>
                <td><?php echo htmlspecialchars($u['correo']); ?></td>
                <td><?php echo $u['nombre_rol']; ?></td>
                <td><?php echo $u['estado'] ? '🟢 Activo' : '🔴 Inactivo'; ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn btn-editar">✏️ Editar</a>
                    <?php if($u['estado'] == 1): ?>
                        <a href="inactivar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Inactivar usuario?');">🗑️ Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>