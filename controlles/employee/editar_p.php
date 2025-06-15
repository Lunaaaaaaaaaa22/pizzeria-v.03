<?php
require_once '../../models/conexion.php';
require_once '../../models/funciones.php';

$con = conectar();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener promoción existente
$sql = "SELECT * FROM promociones WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$promo = $result->fetch_assoc();

if (!$promo) {
    header("Location: ../../views/employee/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizarInput($con, $_POST['nombre']);
    $descripcion = sanitizarInput($con, $_POST['descripcion']);
    $fecha_fin = sanitizarInput($con, $_POST['fecha_fin']);
    $precio = floatval($_POST['precio']);
    $imagen_url = $promo['imagen_url'];
    
    // Procesar nueva imagen si se subió
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nueva_imagen = subirImagen($_FILES['imagen']);
        if ($nueva_imagen) {
            // Eliminar imagen anterior si existe
            if (file_exists("../" . $promo['imagen_url'])) {
                unlink("../" . $promo['imagen_url']);
            }
            $imagen_url = $nueva_imagen;
        }
    }
    
    $sql = "UPDATE promociones SET 
            nombre = ?, 
            descripcion = ?, 
            imagen_url = ?, 
            fecha_fin = ?,
            precio = ?
            WHERE id = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssdi", $nombre, $descripcion, $imagen_url, $fecha_fin, $precio, $id);
    
    if ($stmt->execute()) {
        header("Location: ../views/employee/dashboard.php?success=1");
        exit();
    } else {
        header("Location: editar_p.php?id=$id&error=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Promoción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Editar Promoción</h1>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Error al actualizar la promoción. Inténtalo de nuevo.</div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Promoción</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($promo['nombre']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($promo['descripcion']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="precio" class="form-label">Precio ($)</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" 
                       value="<?= htmlspecialchars($promo['precio']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="fecha_fin" class="form-label">Fecha de Finalización</label>
                <input type="text" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= $promo['fecha_fin'] ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen de la Promoción</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                <div class="mt-2">
                    <img src="/<?= $promo['imagen_url'] ?>" alt="Imagen actual" style="max-width: 200px;">
                    <p class="text-muted mt-1">Imagen actual</p>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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