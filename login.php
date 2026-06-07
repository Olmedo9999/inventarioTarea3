<?php
// login.php
session_start();
require_once 'includes/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Consulta para verificar el usuario en la base de datos
    $stmt = $pdo->prepare("SELECT id_usuario, nombre, apellido, id_rol, contrasena FROM usuarios WHERE correo = ? AND estado = 1");
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificación de credenciales
    if ($user && $contrasena === $user['contrasena']) {
        // Guardar datos en la sesión
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['nombre'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['id_rol'] = $user['id_rol'];
        
        // Redirigir a la pantalla principal
        header("Location: index.php");
        exit;
    } else {
        $error = "Credenciales incorrectas o usuario inactivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventario Santa Elena</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; max-width: 350px; }
        .login-box h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #004494; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2> Acceso al Sistema</h2>
        <?php if($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>
        
        <form method="POST" action="">
            <label for="correo">Correo Electrónico:</label>
            <input  id="correo" name="correo" required placeholder="ejemplo@gmail.com.com">
            
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required placeholder="Ingresa tu contraseña">
            
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>