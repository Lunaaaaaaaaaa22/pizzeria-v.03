<?php
require_once '../../models/conexion.php';

$con = conectar();

// Verificar acción
$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? 0;

// Procesar acción de agregar
if ($action === 'agregar') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $tipo = $_POST['tipo'] ?? 'Otros';
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;

    if (!empty($nombre)) {
        $sql = "INSERT INTO inventario (nombre, descripcion, cantidad, tipo, fecha_vencimiento, activo) 
                VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssiss", $nombre, $descripcion, $cantidad, $tipo, $fecha_vencimiento);
        
        if ($stmt->execute()) {
            header("Location: ../../views/employee/inventario.php?success=Item agregado correctamente");
        } else {
            header("Location: ../../views/employee/inventario.php?error=Error al agregar el item");
        }
    } else {
        header("Location: ../../views/employee/inventario.php?error=El nombre es requerido");
    }
    exit();
}

// Procesar acción de editar
if ($action === 'editar') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $tipo = $_POST['tipo'] ?? 'Otros';
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;

    if (!empty($nombre)) {
        $sql = "UPDATE inventario SET 
                nombre = ?, 
                descripcion = ?, 
                cantidad = ?, 
                tipo = ?, 
                fecha_vencimiento = ?,
                actualizado = CURRENT_TIMESTAMP
                WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssissi", $nombre, $descripcion, $cantidad, $tipo, $fecha_vencimiento, $id);
        
        if ($stmt->execute()) {
            header("Location: ../../views/employee/inventario.php?success=Item actualizado correctamente");
        } else {
            header("Location: ../../views/employee/inventario.php?error=Error al actualizar el item");
        }
    } else {
        header("Location: ../../views/employee/inventario.php?error=El nombre es requerido");
    }
    exit();
}

// Procesar acción de eliminar (borrado lógico)
if ($action === 'eliminar') {
    $sql = "UPDATE inventario SET activo = 0 WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: ../../views/employee/inventario.php?success=Item eliminado correctamente");
    } else {
        header("Location: ../../views/employee/inventario.php?error=Error al eliminar el item");
    }
    exit();
}

// Si no se reconoce la acción
header("Location: ../../views/employee/inventario.php?error=Acción no válida");
$con->close();
?>