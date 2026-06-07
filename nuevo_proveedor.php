<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) { 
    header("Location: proveedores.php"); exit; 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Proveedor</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f9;}
        .form-container { max-width: 500px; background: white; padding: 30px; border-radius: 8px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #28a745; color: white; padding: 10px; border: none; width: 100%; cursor: pointer; font-size: 16px; border-radius: 4px;}
        .btn-cancel { background: #dc3545; color: white; text-decoration: none; padding: 10px; display: block; text-align: center; margin-top: 10px; border-radius: 4px;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2>🏢 Registrar Proveedor</h2>
        <form action="guardar_proveedor.php" method="POST">
            <label>Nombre de la Empresa:</label>
            <input type="text" name="nombre_empresa" required>

            <label>Nombre del Contacto:</label>
            <input type="text" name="contacto">

            <label>Teléfono:</label>
            <input type="text" name="telefono">

            <label>Correo Electrónico:</label>
            <input type="email" name="correo">

            <label>Dirección:</label>
            <input type="text" name="direccion">

            <button type="submit" class="btn-submit">💾 Guardar Proveedor</button>
            <a href="proveedores.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>