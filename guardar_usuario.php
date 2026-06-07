<?php
session_start();
require_once 'includes/conexion.php';

// Seguridad: Solo el Administrador (rol 1) puede guardar nuevos usuarios
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    die("Acceso denegado: No tienes permisos para realizar esta acción.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura de datos del formulario
    $nombre     = trim($_POST['nombre']);
    $apellido   = trim($_POST['apellido']);
    $correo     = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $id_rol     = $_POST['id_rol'];

    try {
        // Cifrado de contraseña (Bcrypt)
        // Esto es un estándar de seguridad profesional: 
        // nunca guardamos la contraseña tal cual la escribe el usuario.
        $hash_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);

        // Preparar la consulta SQL
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, contrasena, id_rol, estado) 
                VALUES (?, ?, ?, ?, ?, 1)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $correo, $hash_contrasena, $id_rol]);

        // Redirigir al listado de usuarios con un mensaje de éxito
        header("Location: usuarios.php?msg=usuario_creado");
        exit;

    } catch (PDOException $e) {
        // Manejo de errores (por ejemplo, si el correo ya está registrado)
        if ($e->getCode() == 23000) {
            die("Error: El correo electrónico ya está registrado en el sistema. <a href='nuevo_usuario.php'>Intentar de nuevo</a>");
        } else {
            die("Error al registrar el usuario: " . $e->getMessage());
        }
    }
} else {
    // Si intentan acceder a este archivo directamente sin enviar el formulario
    header("Location: usuarios.php");
    exit;
}
?>