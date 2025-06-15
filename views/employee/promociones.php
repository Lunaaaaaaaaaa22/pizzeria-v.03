<?php
require_once '../../models/conexion.php';
require_once '../../models/funciones.php';

$con = conectar();
$sql = "SELECT * FROM promociones ORDER BY estado DESC, fecha_fin DESC";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Promociones - Pizzería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/cs/promociones.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3">
            <div class="text-center mb-4">
                <h3><i class="fas fa-pizza-slice me-2"></i>Pizzería</h3>
                <p class="text-muted">Panel de Empleados</p>
            </div>
            
                        <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="promociones.php">
                        <i class="fas fa-tags"></i> Promociones
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="inventario.php">
                        <i class="fas fa-warehouse"></i> Inventario
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pizzas.php">
                        <i class="fas fa-pizza-slice"></i> Pizzas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-tags me-2"></i>Gestión de Promociones</h1>
                    <a href="../../controlles/agregar_p.php" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Agregar Promoción
                    </a>
                </div>
                
                <div class="row">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($promo = $result->fetch_assoc()): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card card-promo h-100 <?= $promo['estado'] == 'inactiva' ? 'inactiva' : '' ?>">
                                <img src="../../<?= $promo['imagen_url'] ?>" class="promo-img" alt="<?= $promo['nombre'] ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $promo['nombre'] ?></h5>
                                    <p class="card-text"><?= $promo['descripcion'] ?></p>
                                    <p class="precio-promo mb-2">$<?= number_format($promo['precio'], 2) ?></p>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= date('d/m/Y', strtotime($promo['fecha_inicio'])) ?> - 
                                        <?= date('d/m/Y', strtotime($promo['fecha_fin'])) ?>
                                    </p>
                                    <span class="badge bg-<?= $promo['estado'] == 'activa' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($promo['estado']) ?>
                                    </span>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="../../controlles/editar_p.php?id=<?= $promo['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <?php if($promo['estado'] == 'activa'): ?>
                                            <a href="../../controlles/acciones_p.php?action=desactivar&id=<?= $promo['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-eye-slash"></i> Desactivar
                                            </a>
                                        <?php else: ?>
                                            <a href="../../controlles/acciones_p.php?action=activar&id=<?= $promo['id'] ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-eye"></i> Activar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                No hay promociones registradas. <a href="../../controlles/agregar_p.php" class="alert-link">Crear una nueva promoción</a>.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $con->close(); ?>