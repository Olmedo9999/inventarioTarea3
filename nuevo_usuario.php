<?php
session_start();
// Solo el Administrador (1) puede registrar nuevos usuarios
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) { 
    header("Location: index.php"); exit; 
}
// ¡Aquí estaba el error! Faltaba este archivo
require_once 'includes/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario - Santa Elena</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container form-container">
        <div class="top-bar">
            <h2>👤 Registrar Usuario</h2>
            <a href="usuarios.php" class="btn btn-volver">⬅ Volver</a>
        </div>

        <form action="guardar_usuario.php" method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
            
            <label>Apellido:</label>
            <input type="text" name="apellido" required>
            
            <label>Correo Electrónico:</label>
            <input type="email" name="correo" required>
            
            <label>Contraseña:</label>
            <input type="password" name="contrasena" required>
            
            <label>Asignar Rol:</label>
            <select name="id_rol" required>
                <option value="">-- Seleccione un rol --</option>
                <?php
                // Ahora $pdo está definido gracias al require_once de arriba
                $roles = $pdo->query("SELECT * FROM roles");
                foreach($roles as $r) {
                    echo "<option value='{$r['id_rol']}'>{$r['nombre_rol']}</option>";
                }
                ?>
            </select>
            
            <button type="submit" class="btn-submit">Guardar Usuario</button>
        </form>
    </div>
</body>
</html>