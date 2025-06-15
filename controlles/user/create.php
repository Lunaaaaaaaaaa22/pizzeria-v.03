<?php
session_start();

// Incluir conexión a la base de datos
include '../../models/conexion.php';

// Procesar formulario si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y limpiar datos
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    $direccion = trim($_POST['direccion']);

    // Validaciones
    $errors = [];
    
    if (empty($nombre) || empty($apellido) || empty($username) || empty($password) || empty($direccion)) {
        $errors[] = "Todos los campos son obligatorios";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden";
    }

    // Si no hay errores, registrar usuario
    if (empty($errors)) {
        $con = conectar();
        
        // Verificar si usuario ya existe
        $stmt = $con->prepare("SELECT id FROM usuario WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "El nombre de usuario ya existe";
        } else {
            // Hash de la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar usuario
            $stmt = $con->prepare("INSERT INTO usuario (username, password, rol) VALUES (?, ?, 'cliente')");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $user_id = $con->insert_id;
                
                // Insertar cliente
                $stmt = $con->prepare("INSERT INTO cliente (usuario_id, nombre, apellido, direccion) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $user_id, $nombre, $apellido, $direccion);
                
                if ($stmt->execute()) {
                    // Registro exitoso
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['rol'] = 'cliente';
                    
                    header("Location: ../views/user/inicio.php ");
                    exit();
                }
            }
            $errors[] = "Error al registrar el usuario";
        }
    }
    
    // Si hay errores, guardar para mostrar
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Pizza Mass</title>
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/cs/style.css">
</head>
<body>
    <div class="background-layer"></div>
    
    <div class="auth-container">
        <div class="auth-header">
            <h1 class="auth-title">Crear Cuenta</h1>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <form id="registerForm" action="create.php" method="POST">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required 
                       value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" id="apellido" name="apellido" class="form-control" required 
                       value="<?= isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="username" class="form-label">Nombre de Usuario</label>
                <input type="text" id="username" name="username" class="form-control" required 
                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Contraseña (mínimo 8 caracteres)</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="8">
            </div>
            
            <div class="form-group">
                <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required minlength="8">
            </div>
            
            <div class="form-group">
                <label for="direccion" class="form-label">Dirección de Entrega</label>
                <textarea id="direccion" name="direccion" class="form-control" required><?= isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : '' ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Registrarse</button>
            
            <p class="auth-link">¿Ya tienes cuenta? <a href="../../views/user/inicio.php">Inicia Sesión</a></p>
        </form>
    </div>

    <script>
        // Validación de contraseñas coincidentes
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                document.getElementById('confirmPassword').focus();
            }
        });
    </script>
</body>
</html>