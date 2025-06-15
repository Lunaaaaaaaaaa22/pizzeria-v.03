<?php
include '../../models/conexion.php';
include '../../models/imagen_pizza.php';

$con = conectar();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

try {
    // Iniciar transacción para integridad de datos
    mysqli_begin_transaction($con);
    
    switch($accion) {
        case 'agregar':
            // Procesar imagen si se subió
            $imagen_url = null;
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $imagen_url = subirImagenPizza($_FILES['imagen']);
            }
            
            // Validar datos requeridos
            if(empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_POST['ingredientes'])) {
                throw new Exception("Todos los campos son obligatorios");
            }
            
            // Validar precios
            if(!is_numeric($_POST['precio_chica']) || $_POST['precio_chica'] < 0 ||
               !is_numeric($_POST['precio_mediana']) || $_POST['precio_mediana'] < 0 ||
               !is_numeric($_POST['precio_grande']) || $_POST['precio_grande'] < 0) {
                throw new Exception("Los precios deben ser números positivos");
            }
            
            // Insertar en inventario primero
            $queryInventario = "INSERT INTO inventario (nombre, descripcion, cantidad, tipo) 
                               VALUES (?, ?, 0, 'pizza')";
            $stmt = mysqli_prepare($con, $queryInventario);
            if(!$stmt) throw new Exception("Error al preparar consulta de inventario: " . mysqli_error($con));
            
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            mysqli_stmt_bind_param($stmt, "ss", $nombre, $descripcion);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al insertar en inventario: " . mysqli_stmt_error($stmt));
            }
            $inventario_id = mysqli_insert_id($con);
            mysqli_stmt_close($stmt);
            
            // Insertar en tabla pizza
            $queryPizza = "INSERT INTO pizza (inventario_id, imagen_url) VALUES (?, ?)";
            $stmt = mysqli_prepare($con, $queryPizza);
            if(!$stmt) throw new Exception("Error al preparar consulta de pizza: " . mysqli_error($con));
            
            mysqli_stmt_bind_param($stmt, "is", $inventario_id, $imagen_url);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al insertar pizza: " . mysqli_stmt_error($stmt));
            }
            $pizza_id = mysqli_insert_id($con);
            mysqli_stmt_close($stmt);
            
            // Insertar ingredientes manuales
            $ingredientes = explode(",", $_POST['ingredientes']);
            $queryIngredientes = "INSERT INTO pizza_ingredientes_manual (pizza_id, nombre_ingrediente) VALUES (?, ?)";
            $stmt = mysqli_prepare($con, $queryIngredientes);
            if(!$stmt) throw new Exception("Error al preparar consulta de ingredientes: " . mysqli_error($con));
            
            foreach($ingredientes as $ing) {
                $ingrediente_limpio = trim($ing);
                if(!empty($ingrediente_limpio)) {
                    $param_pizza_id = $pizza_id;
                    $param_ingrediente = $ingrediente_limpio;
                    
                    mysqli_stmt_bind_param($stmt, "is", $param_pizza_id, $param_ingrediente);
                    if(!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Error al insertar ingrediente: " . mysqli_stmt_error($stmt));
                    }
                }
            }
            mysqli_stmt_close($stmt);
            
            // Insertar precios
            $queryPrecios = "INSERT INTO pizza_precio (pizza_id, tamaño, precio) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($con, $queryPrecios);
            if(!$stmt) throw new Exception("Error al preparar consulta de precios: " . mysqli_error($con));
            
            // Precio chica
            $tamano_chica = 'chica';
            $precio_chica = (float)$_POST['precio_chica'];
            mysqli_stmt_bind_param($stmt, "isd", $pizza_id, $tamano_chica, $precio_chica);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al insertar precio chica: " . mysqli_stmt_error($stmt));
            }
            
            // Precio mediana
            $tamano_mediana = 'mediana';
            $precio_mediana = (float)$_POST['precio_mediana'];
            mysqli_stmt_bind_param($stmt, "isd", $pizza_id, $tamano_mediana, $precio_mediana);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al insertar precio mediana: " . mysqli_stmt_error($stmt));
            }
            
            // Precio grande
            $tamano_grande = 'grande';
            $precio_grande = (float)$_POST['precio_grande'];
            mysqli_stmt_bind_param($stmt, "isd", $pizza_id, $tamano_grande, $precio_grande);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al insertar precio grande: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            
            mysqli_commit($con);
            header("Location: ../../views/employee/pizzas.php?success=1");
            break;
            
        case 'editar':
            // Validar ID
            if(empty($_POST['id']) || !is_numeric($_POST['id'])) {
                throw new Exception("ID de pizza inválido");
            }
            
            // Procesar imagen si se subió una nueva
            $imagen_url = $_POST['imagen_actual'];
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $imagen_url = subirImagenPizza($_FILES['imagen']);
            }
            
            // Validar datos requeridos
            if(empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_POST['ingredientes'])) {
                throw new Exception("Todos los campos son obligatorios");
            }
            
            // Validar precios
            if(!is_numeric($_POST['precio_chica']) || $_POST['precio_chica'] < 0 ||
               !is_numeric($_POST['precio_mediana']) || $_POST['precio_mediana'] < 0 ||
               !is_numeric($_POST['precio_grande']) || $_POST['precio_grande'] < 0) {
                throw new Exception("Los precios deben ser números positivos");
            }
            
            // Obtener inventario_id de la pizza
            $queryGetInv = "SELECT inventario_id FROM pizza WHERE id = ?";
            $stmt = mysqli_prepare($con, $queryGetInv);
            if(!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($con));
            
            $pizza_id = (int)$_POST['id'];
            mysqli_stmt_bind_param($stmt, "i", $pizza_id);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al obtener inventario_id: " . mysqli_stmt_error($stmt));
            }
            
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            $inventario_id = $row['inventario_id'];
            mysqli_stmt_close($stmt);
            
            // Actualizar inventario
            $queryUpdateInv = "UPDATE inventario SET 
                              nombre = ?,
                              descripcion = ?
                              WHERE id = ?";
            $stmt = mysqli_prepare($con, $queryUpdateInv);
            if(!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($con));
            
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            mysqli_stmt_bind_param($stmt, "ssi", $nombre, $descripcion, $inventario_id);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar inventario: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            
            // Actualizar pizza (imagen)
            $queryUpdatePizza = "UPDATE pizza SET imagen_url = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $queryUpdatePizza);
            if(!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($con));
            
            mysqli_stmt_bind_param($stmt, "si", $imagen_url, $pizza_id);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar pizza: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            
            // Borrar ingredientes antiguos
            $queryDeleteIng = "DELETE FROM pizza_ingredientes_manual WHERE pizza_id = ?";
            $stmt = mysqli_prepare($con, $queryDeleteIng);
            if(!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($con));
            
            mysqli_stmt_bind_param($stmt, "i", $pizza_id);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al borrar ingredientes: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            
            // Insertar nuevos ingredientes
            $ingredientes = explode(",", $_POST['ingredientes']);
            $queryInsertIng = "INSERT INTO pizza_ingredientes_manual (pizza_id, nombre_ingrediente) VALUES (?, ?)";
            $stmt = mysqli_prepare($con, $queryInsertIng);
            if(!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($con));
            
            foreach($ingredientes as $ing) {
                $ingrediente_limpio = trim($ing);
                if(!empty($ingrediente_limpio)) {
                    $param_pizza_id = $pizza_id;
                    $param_ingrediente = $ingrediente_limpio;
                    
                    mysqli_stmt_bind_param($stmt, "is", $param_pizza_id, $param_ingrediente);
                    if(!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Error al insertar ingrediente: " . mysqli_stmt_error($stmt));
                    }
                }
            }
            mysqli_stmt_close($stmt);
            
            // Actualizar precios
            $queryUpdatePrecio = "UPDATE pizza_precio SET precio = ? WHERE pizza_id = ? AND tamaño = ?";
            $stmt = mysqli_prepare($con, $queryUpdatePrecio);
            if(!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($con));
            
            // Precio chica
            $tamano_chica = 'chica';
            $precio_chica = (float)$_POST['precio_chica'];
            mysqli_stmt_bind_param($stmt, "dis", $precio_chica, $pizza_id, $tamano_chica);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar precio chica: " . mysqli_stmt_error($stmt));
            }
            
            // Precio mediana
            $tamano_mediana = 'mediana';
            $precio_mediana = (float)$_POST['precio_mediana'];
            mysqli_stmt_bind_param($stmt, "dis", $precio_mediana, $pizza_id, $tamano_mediana);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar precio mediana: " . mysqli_stmt_error($stmt));
            }
            
            // Precio grande
            $tamano_grande = 'grande';
            $precio_grande = (float)$_POST['precio_grande'];
            mysqli_stmt_bind_param($stmt, "dis", $precio_grande, $pizza_id, $tamano_grande);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar precio grande: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            
            mysqli_commit($con);
            header("Location: ../../views/employee/pizzas.php?success=2");
            break;
            
        case 'eliminar':
            // Validar ID
            if(empty($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception("ID de pizza inválido");
            }
            
            // Cambiar estado activo a 0 (eliminación lógica)
            $query = "UPDATE pizza SET activo = 0 WHERE id = ?";
            $stmt = mysqli_prepare($con, $query);
            if(!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($con));
            
            $pizza_id = (int)$_GET['id'];
            mysqli_stmt_bind_param($stmt, "i", $pizza_id);
            if(!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al desactivar pizza: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            
            mysqli_commit($con);
            header("Location: ../../views/employee/pizzas.php?success=3");
            break;
            
        default:
            throw new Exception("Acción no válida");
    }
} catch(Exception $e) {
    // Revertir transacción en caso de error
    if(isset($con) && get_class($con) === 'mysqli') {
        mysqli_rollback($con);
    }
    header("Location: ../../views/employee/pizzas.php?error=" . urlencode($e->getMessage()));
} finally {
    // Cerrar conexión si existe
    if(isset($con) && get_class($con) === 'mysqli') {
        mysqli_close($con);
    }
}