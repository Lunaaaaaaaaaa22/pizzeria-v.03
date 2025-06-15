<?php
function subirImagenPizza($imagen) {
    $directorio = "../../assets/img/pizzas/";
    
    // Crear directorio si no existe
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Generar nombre único para evitar sobrescrituras
    $nombreArchivo = basename($imagen['name']);
    $rutaDestino = $directorio . $nombreArchivo;
    
    // Validar extensión
    $extension = strtolower(pathinfo($rutaDestino, PATHINFO_EXTENSION));
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($extension, $extensionesPermitidas)) {
        // Validar tamaño (máximo 2MB)
        if ($imagen['size'] <= 2097152) {
            if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                return '../assets/img/pizzas/' . $nombreArchivo;
            }
        } else {
            throw new Exception("El archivo es demasiado grande. Máximo 2MB permitidos.");
        }
    } else {
        throw new Exception("Formato de archivo no permitido. Use JPG, JPEG, PNG o GIF.");
    }
    
    return null;
}

function sanitizarInput($con, $input) {
    return $con->real_escape_string(trim($input));
}
?>