<?php
session_start();
include '../../models/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $con = conectar();

    // Verifica si la conexión se estableció correctamente
    if ($con->connect_error) {
        die("Error de conexión: " . $con->connect_error);
    }

    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Consulta SQL corregida (usa 'username' en lugar de 'user')
    $sql = "SELECT id, password, rol FROM usuario WHERE username = ? AND activo = 1";
    $stmt = $con->prepare($sql);
    
    // Verifica si la preparación fue exitosa
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $con->error);
    }

    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash, $rol);
        $stmt->fetch();

        if (password_verify($pass, $hash)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['rol'] = $rol;
            $_SESSION['username'] = $user;

            // Redirigir según el rol
            if ($rol === 'cliente') {
                header("Location: ../Pag_Usuarios_Inicio/Pagina_Usuarios_Inicio.php");
            } else if($rol === 'empleado'){
                header("Location: ../views/employe/dashboard.php");
            }else{
                header("Location: . ../views/admin/dashboard.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Contraseña incorrecta";
        }
    } else {
        $_SESSION['error'] = "Usuario no encontrado o cuenta inactiva";
    }

    $stmt->close();
    $con->close();
    
    // Redirigir de vuelta al login con mensaje de error
    header("Location: login.php?error=1");
    exit();
}
?>