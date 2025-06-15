<?php
require_once '../../models/conexion.php';

$con = conectar();

// Obtener todos los items de inventario activos
$sql = "SELECT * FROM inventario WHERE activo = 1 ORDER BY tipo, nombre";
$result = $con->query($sql);

// Verificar items con bajo stock
$sql_alerta = "SELECT COUNT(*) as total FROM inventario WHERE cantidad < 10 AND activo = 1";
$alerta_stock = $con->query($sql_alerta)->fetch_assoc();

// Obtener items con bajo stock para el modal
$sql_low_stock = "SELECT * FROM inventario WHERE cantidad < 10 AND activo = 1 ORDER BY cantidad ASC";
$low_stock_result = $con->query($sql_low_stock);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Pizzería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/cs/inventario.css">
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
                    <a class="nav-link" href="promociones.php">
                        <i class="fas fa-tags"></i> Promociones
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="inventario.php">
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
                    <h1><i class="fas fa-warehouse me-2"></i>Gestión de Inventario</h1>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#agregarItemModal">
                        <i class="fas fa-plus me-1"></i> Nuevo Item
                    </button>
                </div>
                
                <?php if ($alerta_stock['total'] > 0): ?>
                <div class="stock-alert">
                    <button class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#alertaStockModal">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= $alerta_stock['total'] ?> items con stock bajo
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Descripción</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while($item = $result->fetch_assoc()): ?>
                                        <tr class="<?= $item['cantidad'] < 5 ? 'critical-stock' : ($item['cantidad'] < 10 ? 'low-stock' : '') ?>">
                                            <td><?= htmlspecialchars($item['nombre']) ?></td>
                                            <td>
                                                <?php 
                                                $badge_class = '';
                                                switch($item['tipo']) {
                                                    case 'ingrediente': $badge_class = 'badge-ingrediente'; break;
                                                    case 'bebida': $badge_class = 'badge-bebida'; break;
                                                    case 'pizza': $badge_class = 'badge-pizza'; break;
                                                    default: $badge_class = 'badge-otros';
                                                }
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= ucfirst($item['tipo']) ?></span>
                                            </td>
                                            <td>
                                                <?= $item['cantidad'] ?>
                                                <?php if($item['cantidad'] < 10): ?>
                                                    <i class="fas fa-exclamation-circle text-danger ms-1"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars(substr($item['descripcion'], 0, 50)) . (strlen($item['descripcion']) > 50 ? '...' : '') ?></td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-sm btn-primary me-2" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editarItemModal"
                                                            data-id="<?= $item['id'] ?>"
                                                            data-nombre="<?= htmlspecialchars($item['nombre']) ?>"
                                                            data-descripcion="<?= htmlspecialchars($item['descripcion']) ?>"
                                                            data-cantidad="<?= $item['cantidad'] ?>"
                                                            data-tipo="<?= $item['tipo'] ?>"
                                                            data-fecha="<?= $item['fecha_vencimiento'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#eliminarItemModal"
                                                            data-id="<?= $item['id'] ?>"
                                                            data-nombre="<?= htmlspecialchars($item['nombre']) ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No hay items en el inventario</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar nuevo item -->
    <div class="modal fade" id="agregarItemModal" tabindex="-1" aria-labelledby="agregarItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../../controlles/acciones_inventario.php" method="POST">
                    <input type="hidden" name="action" value="agregar">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="agregarItemModalLabel"><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Item</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="0" value="0" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="pizza">Pizza</option>
                                    <option value="ingrediente">Ingrediente</option>
                                    <option value="bebida">Bebida</option>
                                    <option value="Otros" selected>Otros</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento (opcional)</label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar item -->
    <div class="modal fade" id="editarItemModal" tabindex="-1" aria-labelledby="editarItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../../controlles/acciones_inventario.php" method="POST">
                    <input type="hidden" name="action" value="editar">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editarItemModalLabel"><i class="fas fa-edit me-2"></i>Editar Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre del Item</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="edit_cantidad" name="cantidad" min="0" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="edit_tipo" name="tipo" required>
                                    <option value="pizza">Pizza</option>
                                    <option value="ingrediente">Ingrediente</option>
                                    <option value="bebida">Bebida</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_fecha" class="form-label">Fecha de Vencimiento (opcional)</label>
                            <input type="date" class="form-control" id="edit_fecha" name="fecha_vencimiento">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar item -->
    <div class="modal fade" id="eliminarItemModal" tabindex="-1" aria-labelledby="eliminarItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../../controlles/acciones_inventario.php" method="POST">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="eliminarItemModalLabel"><i class="fas fa-trash-alt me-2"></i>Eliminar Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar el item <strong id="delete_nombre"></strong>?</p>
                        <p class="text-muted">Esta acción desactivará el item pero no lo borrará permanentemente.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para alerta de stock bajo -->
    <div class="modal fade" id="alertaStockModal" tabindex="-1" aria-labelledby="alertaStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="alertaStockModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Alerta de Stock Bajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Hay <?= $alerta_stock['total'] ?> items con stock bajo (menos de 10 unidades).
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($low_stock_result->num_rows > 0): ?>
                                    <?php while($item = $low_stock_result->fetch_assoc()): ?>
                                    <tr class="<?= $item['cantidad'] < 5 ? 'critical-stock' : 'low-stock' ?>">
                                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                                        <td><?= ucfirst($item['tipo']) ?></td>
                                        <td><?= $item['cantidad'] ?></td>
                                        <td><?= htmlspecialchars(substr($item['descripcion'], 0, 50)) . (strlen($item['descripcion']) > 50 ? '...' : '') ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No hay items con stock bajo</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configurar modal de edición con los datos del item
        document.getElementById('editarItemModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            const descripcion = button.getAttribute('data-descripcion');
            const cantidad = button.getAttribute('data-cantidad');
            const tipo = button.getAttribute('data-tipo');
            const fecha = button.getAttribute('data-fecha');
            
            const modal = this;
            modal.querySelector('#edit_id').value = id;
            modal.querySelector('#edit_nombre').value = nombre;
            modal.querySelector('#edit_descripcion').value = descripcion;
            modal.querySelector('#edit_cantidad').value = cantidad;
            modal.querySelector('#edit_tipo').value = tipo;
            modal.querySelector('#edit_fecha').value = fecha;
        });
        
        // Configurar modal de eliminación con los datos del item
        document.getElementById('eliminarItemModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            
            const modal = this;
            modal.querySelector('#delete_id').value = id;
            modal.querySelector('#delete_nombre').textContent = nombre;
        });
    </script>
</body>
</html>
<?php $con->close(); ?>