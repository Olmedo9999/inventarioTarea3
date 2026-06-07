<?php
session_start();
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) { 
    header("Location: index.php"); 
    exit; 
}
require_once 'includes/conexion.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) { die("Usuario no encontrado."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - Santa Elena</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos específicos para esta vista */
        hr { border: 0; border-top: 1px solid #ddd; margin: 25px 0; }
        
        .nota-ayuda {
            color: #6c757d; 
            font-size: 14px; 
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #17a2b8;
        }

        /* Estilo para la caja de reactivación */
        .alerta-reactivar {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alerta-reactivar label {
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            margin: 0;
            font-size: 15px;
        }

        /* Ajuste del checkbox para que no se vea aplastado */
        .alerta-reactivar input[type="checkbox"] {
            width: auto;
            margin: 0 10px 0 0;
            transform: scale(1.3);
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <div class="top-bar">
            <h2>✏️ Editar Usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></h2>
            <a href="usuarios.php" class="btn btn-volver">⬅ Volver</a>
        </div>
        
        <form action="actualizar_usuario.php" method="POST" onsubmit="return validarPasswords()">
            <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
            
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            
            <label>Apellido:</label>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
            
            <label>Correo Electrónico:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
            
            <label>Rol del Sistema:</label>
            <select name="id_rol" required>
                <?php 
                $roles = $pdo->query("SELECT * FROM roles");
                foreach($roles as $r) {
                    $sel = ($r['id_rol'] == $usuario['id_rol']) ? 'selected' : '';
                    echo "<option value='{$r['id_rol']}' $sel>{$r['nombre_rol']}</option>";
                }
                ?>
            </select>

            
            <div class="nota-ayuda">
                💡 <strong>Seguridad:</strong> Deje los campos de contraseña vacíos si no desea cambiar la credencial actual del usuario.
            </div>

            <label>Nueva Contraseña:</label>
            <input type="password" name="contrasena" id="pass1" placeholder="Escriba solo si desea cambiarla">
            
            <label>Confirmar Nueva Contraseña:</label>
            <input type="password" name="conf_contrasena" id="pass2" placeholder="Repita la nueva contraseña">

            <?php if($usuario['estado'] == 0): ?>
                <div class="alerta-reactivar">
                    <label>
                        <input type="checkbox" name="reactivar" value="1"> 
                        ⚠️ Este usuario está INACTIVO. Marque esta casilla para REACTIVARLO.
                    </label>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </form>
    </div>

    <script>
        function validarPasswords() {
            let p1 = document.getElementById('pass1').value;
            let p2 = document.getElementById('pass2').value;
            if(p1 !== p2) {
                alert("⚠ Error: Las contraseñas no coinciden. Por favor, verifique.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>