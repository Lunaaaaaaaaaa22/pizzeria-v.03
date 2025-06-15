<?php
require_once '../../models/conexion.php';
require_once '../../models/funciones.php';

$con = conectar();

// 1. Estadísticas de promociones
$promociones_activas = $con->query("SELECT COUNT(*) as total FROM promociones WHERE estado = 'activa'")->fetch_assoc();
$promociones_inactivas = $con->query("SELECT COUNT(*) as total FROM promociones WHERE estado = 'inactiva'")->fetch_assoc();
$promociones_proximas = $con->query("SELECT COUNT(*) as total FROM promociones WHERE fecha_inicio > CURDATE()")->fetch_assoc();

// 2. Estadísticas de inventario
$inventario_total = $con->query("SELECT COUNT(*) as total FROM inventario WHERE activo = 1")->fetch_assoc();
$inventario_bajo_stock = $con->query("SELECT COUNT(*) as total FROM inventario WHERE cantidad < 10 AND activo = 1")->fetch_assoc();
$inventario_critico = $con->query("SELECT COUNT(*) as total FROM inventario WHERE cantidad < 5 AND activo = 1")->fetch_assoc();

// 3. Últimos items del inventario (para vista previa)
$sql_inventario = "SELECT * FROM inventario WHERE activo = 1 ORDER BY cantidad ASC LIMIT 5";
$inventario_items = $con->query($sql_inventario);

// 4. Últimas promociones (para vista previa)
$sql_promociones = "SELECT * FROM promociones ORDER BY fecha_inicio DESC LIMIT 3";
$ultimas_promociones = $con->query($sql_promociones);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pizzería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/cs/dashboard.css">
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
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="promociones.php">
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
                <li class="nav-item mt-3">
                    <a class="nav-link" href="#">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <!-- Welcome Card -->
                <div class="card welcome-card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">Bienvenido al Panel de Control</h2>
                        <p class="card-text">Gestión integral de promociones e inventario</p>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <!-- Promociones -->
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card" style="background-color: var(--primary-color);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Promociones Activas</h5>
                                        <h2><?= $promociones_activas['total'] ?></h2>
                                    </div>
                                    <i class="fas fa-tags stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card" style="background-color: var(--warning-color); color: #000;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Promociones Inactivas</h5>
                                        <h2><?= $promociones_inactivas['total'] ?></h2>
                                    </div>
                                    <i class="fas fa-eye-slash stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card" style="background-color: var(--info-color);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Próximas Promociones</h5>
                                        <h2><?= $promociones_proximas['total'] ?></h2>
                                    </div>
                                    <i class="fas fa-calendar-alt stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Inventario Stats -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card" style="background-color: var(--dark-color);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Items en Inventario</h5>
                                        <h2><?= $inventario_total['total'] ?></h2>
                                    </div>
                                    <i class="fas fa-boxes stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card" style="background-color: var(--warning-color); color: #000;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Bajo Stock</h5>
                                        <h2><?= $inventario_bajo_stock['total'] ?></h2>
                                    </div>
                                    <i class="fas fa-exclamation-triangle stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card" style="background-color: var(--primary-color);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Stock Crítico</h5>
                                        <h2><?= $inventario_critico['total'] ?></h2>
                                    </div>
                                    <i class="fas fa-skull-crossbones stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <h4 class="mb-3">Acciones Rápidas</h4>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-tags text-primary mb-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title">Agregar Promoción</h5>
                                <a href="../../controlles/agregar_p.php" class="btn btn-sm btn-outline-primary mt-2">Crear</a>
                            </div>
                        </div>
                    </div>                    
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-list-alt text-info mb-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title">Ver Inventario</h5>
                                <a href="inventario.php" class="btn btn-sm btn-outline-info mt-2">Ver Todo</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-file-export text-warning mb-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title">Generar Reporte</h5>
                                <a href="../../controlles/reporte_inventario.php" class="btn btn-sm btn-outline-warning mt-2">Exportar</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vista Previa: Últimas Promociones -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Últimas Promociones</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php if ($ultimas_promociones->num_rows > 0): ?>
                                        <?php while($promo = $ultimas_promociones->fetch_assoc()): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card preview-card h-100">
                                                <img src="../../<?= $promo['imagen_url'] ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?= $promo['nombre'] ?>">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?= $promo['nombre'] ?></h6>
                                                    <p class="card-text small"><?= substr($promo['descripcion'], 0, 60) ?>...</p>
                                                    <span class="badge bg-<?= $promo['estado'] == 'activa' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($promo['estado']) ?>
                                                    </span>
                                                </div>
                                                <div class="card-footer bg-white">
                                                    <a href="../../controlles/editar_p.php?id=<?= $promo['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <p class="text-muted text-center">No hay promociones recientes</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vista Previa: Inventario Bajo Stock -->

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $con->close(); ?>