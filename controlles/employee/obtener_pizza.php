<?php
include '../../models/conexion.php';

$con = conectar();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Consulta para obtener todos los datos de la pizza
    $query = "SELECT p.id, p.imagen_url, i.nombre, i.descripcion, 
              GROUP_CONCAT(pim.nombre_ingrediente SEPARATOR ', ') AS ingredientes,
              pp_ch.precio AS precio_chica, 
              pp_med.precio AS precio_mediana, 
              pp_gran.precio AS precio_grande
              FROM pizza p
              JOIN inventario i ON p.inventario_id = i.id
              LEFT JOIN pizza_ingredientes_manual pim ON p.id = pim.pizza_id
              LEFT JOIN pizza_precio pp_ch ON p.id = pp_ch.pizza_id AND pp_ch.tamaño = 'chica'
              LEFT JOIN pizza_precio pp_med ON p.id = pp_med.pizza_id AND pp_med.tamaño = 'mediana'
              LEFT JOIN pizza_precio pp_gran ON p.id = pp_gran.pizza_id AND pp_gran.tamaño = 'grande'
              WHERE p.id = ?
              GROUP BY p.id";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Pizza no encontrada']);
    }
}

mysqli_close($con);
?>