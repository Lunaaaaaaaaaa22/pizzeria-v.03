<?php
require_once '../../models/conexion.php';
require_once '../../models/funciones.php';

$con = conectar();
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

switch ($action) {
    case 'activar':
        $sql = "UPDATE promociones SET estado = 'activa' WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        break;
        
    case 'desactivar':
        $sql = "UPDATE promociones SET estado = 'inactiva' WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        break;
        
    default:
        header("Location: ../../views/employee/promociones.php");
        exit();
}

if ($stmt->execute()) {
    header("Location: ../../views/employee/promociones.php?success=1");
} else {
    header("Location: ../../views/employee/promociones.php?error=1");
}

$con->close();
?>