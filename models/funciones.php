<?php
function subirImagen($imagen) {
    $directorio = "../assets/img/promociones/";
    
    // Crear directorio si no existe
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = basename($imagen['name']);
    $rutaDestino = $directorio . $nombreArchivo;
    
    $extension = strtolower(pathinfo($rutaDestino, PATHINFO_EXTENSION));
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($extension, $extensionesPermitidas)) {
        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            return 'assets/img/promociones/' . $nombreArchivo;
        }
    }
    
    return null;
}

function sanitizarInput($con, $input) {
    return $con->real_escape_string(trim($input));
}
?>