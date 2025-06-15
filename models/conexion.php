<?php
function conectar() {
    $host = "localhost";
    $db = "pizzeria";
    $user = "root";
    $pass = "";

    $con = mysqli_connect($host, $user, $pass, $db); // Agrega $db directamente aquí

    // Verificar conexión
    if (!$con) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    return $con;
}
?>