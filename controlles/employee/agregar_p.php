<?php
require_once '../../models/conexion.php';
require_once '../../models/funciones.php';

$con = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizarInput($con, $_POST['nombre']);
    $descripcion = sanitizarInput($con, $_POST['descripcion']);
    $fecha_fin = sanitizarInput($con, $_POST['fecha_fin']);
    $imagen_url = '';
    $precio = floatval($_POST['precio']);

    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_url = subirImagen($_FILES['imagen']);
    }
    
    if ($imagen_url) {
        $sql = "INSERT INTO promociones (nombre, descripcion, imagen_url, fecha_inicio, fecha_fin, estado, precio) 
        VALUES (?, ?, ?, CURDATE(), ?, 'activa', ?)";
        
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssd", $nombre, $descripcion, $imagen_url, $fecha_fin, $precio);
        
        if ($stmt->execute()) {
            header("Location: ../../views/employee/dashboard.php?success=1");
            exit();
        }
    }
    
    header("Location: agregar.php?error=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Promoción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Agregar Nueva Promoción</h1>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Error al agregar la promoción. Inténtalo de nuevo.</div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Promoción</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio ($)</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
            </div>
            
            <div class="mb-3">
                <label for="fecha_fin" class="form-label">Fecha de Finalización</label>
                <input type="text" class="form-control" id="fecha_fin" name="fecha_fin" required>
            </div>
            
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen de la Promoción</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Promoción</button>
            <a href="../views/employee/dashboard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        flatpickr("#fecha_fin", {
            locale: "es",
            minDate: "today",
            dateFormat: "Y-m-d"
        });
    </script>
</body>
</html>
<?php $con->close(); ?>