<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pizzas - Pizzería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/cs/pizzas.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3">
            <div class="text-center mb-4">
                <h3><i class="fas fa-pizza-slice me-2"></i>Pizzería</h3>
                <p class="text-muted">Panel de Administración</p>
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
                    <a class="nav-link" href="inventario.php">
                        <i class="fas fa-warehouse"></i> Inventario
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="pizzas.php">
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
                    <h2><i class="fas fa-pizza-slice me-2"></i> Gestión de Pizzas</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarPizzaModal">
                        <i class="fas fa-plus me-2"></i>Agregar Pizza
                    </button>
                </div>
                
                <!-- Mensajes de éxito/error -->
                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                            switch($_GET['success']) {
                                case '1': echo "Pizza agregada correctamente"; break;
                                case '2': echo "Pizza actualizada correctamente"; break;
                                case '3': echo "Pizza desactivada correctamente"; break;
                            }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error: <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Tabla de Pizzas -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Ingredientes</th>
                                        <th>Precios</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include '../../models/conexion.php';
                                    include '../../models/imagen_pizza.php';
                                    
                                    $con = conectar();
                                    
                                    // Consulta para obtener pizzas activas con información completa
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
                                              WHERE p.activo = 1
                                              GROUP BY p.id";
                                    
                                    $result = mysqli_query($con, $query);
                                    
                                    if (!$result) {
                                        echo "<tr><td colspan='7'>Error en la consulta: " . mysqli_error($con) . "</td></tr>";
                                    } else if (mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>".$row['id']."</td>";
                                            echo "<td>";
                                            if($row['imagen_url']) {
                                                echo "<img src='".$row['imagen_url']."' class='pizza-img' alt='".$row['nombre']."'>";
                                            } else {
                                                echo "<img src='assets/img/pizzas/default.png' class='pizza-img' alt='Pizza default'>";
                                            }
                                            echo "</td>";
                                            echo "<td>".$row['nombre']."</td>";
                                            echo "<td>".$row['descripcion']."</td>";
                                            echo "<td><div class='ingredientes-list'>";
                                            $ingredientes = explode(", ", $row['ingredientes']);
                                            foreach($ingredientes as $ing) {
                                                echo "<span class='badge badge-ingrediente'>".$ing."</span>";
                                            }
                                            echo "</div></td>";
                                            echo "<td>";
                                            echo "<span class='badge price-badge'>Chica: $".$row['precio_chica']."</span>";
                                            echo "<span class='badge price-badge'>Mediana: $".$row['precio_mediana']."</span>";
                                            echo "<span class='badge price-badge'>Grande: $".$row['precio_grande']."</span>";
                                            echo "</td>";
                                            echo "<td>";
                                            echo "<button class='btn btn-sm btn-warning action-btn' title='Editar' data-bs-toggle='modal' data-bs-target='#editarPizzaModal' data-id='".$row['id']."'>
                                                    <i class='fas fa-edit'></i>
                                                  </button>";
                                            echo "<button class='btn btn-sm btn-danger action-btn' title='Eliminar' onclick='confirmarEliminar(".$row['id'].")'>
                                                    <i class='fas fa-trash'></i>
                                                  </button>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No hay pizzas activas</td></tr>";
                                    }
                                    
                                    mysqli_close($con);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Agregar Pizza -->
    <div class="modal fade" id="agregarPizzaModal" tabindex="-1" aria-labelledby="agregarPizzaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="agregarPizzaModalLabel">Agregar Nueva Pizza</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../controlles/employee/procesar_pizza.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="agregar">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre de la Pizza</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ingredientes" class="form-label">Ingredientes (separados por comas)</label>
                            <textarea class="form-control" id="ingredientes" name="ingredientes" rows="3" required 
                                      placeholder="Ej: Queso mozzarella, Pepperoni, Champiñones, Aceitunas"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label for="precio_chica" class="form-label">Precio Chica</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="precio_chica" name="precio_chica" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="precio_mediana" class="form-label">Precio Mediana</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="precio_mediana" name="precio_mediana" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="precio_grande" class="form-label">Precio Grande</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="precio_grande" name="precio_grande" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Pizza</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Pizza -->
    <div class="modal fade" id="editarPizzaModal" tabindex="-1" aria-labelledby="editarPizzaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editarPizzaModalLabel">Editar Pizza</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../../controlles/employee/procesar_pizza.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" id="editar_id">
                        <input type="hidden" name="imagen_actual" id="imagen_actual">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editar_nombre" class="form-label">Nombre de la Pizza</label>
                                <input type="text" class="form-control" id="editar_nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editar_imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="editar_imagen" name="imagen" accept="image/*">
                                <div class="mt-2" id="preview_imagen"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="2" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_ingredientes" class="form-label">Ingredientes (separados por comas)</label>
                            <textarea class="form-control" id="editar_ingredientes" name="ingredientes" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label for="editar_precio_chica" class="form-label">Precio Chica</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="editar_precio_chica" name="precio_chica" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="editar_precio_mediana" class="form-label">Precio Mediana</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="editar_precio_mediana" name="precio_mediana" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="editar_precio_grande" class="form-label">Precio Grande</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="editar_precio_grande" name="precio_grande" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar Pizza</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Cargar datos en modal de edición
        $('#editarPizzaModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            
            // Hacer petición AJAX para obtener los datos de la pizza
            $.ajax({
                url: '../../controlles/employee/obtener_pizza.php',
                type: 'GET',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    $('#editar_id').val(response.id);
                    $('#editar_nombre').val(response.nombre);
                    $('#editar_descripcion').val(response.descripcion);
                    $('#editar_ingredientes').val(response.ingredientes);
                    $('#editar_precio_chica').val(response.precio_chica);
                    $('#editar_precio_mediana').val(response.precio_mediana);
                    $('#editar_precio_grande').val(response.precio_grande);
                    $('#imagen_actual').val(response.imagen_url);
                    
                    // Mostrar preview de la imagen actual
                    if(response.imagen_url) {
                        $('#preview_imagen').html('<img src="'+response.imagen_url+'" class="pizza-img mt-2" alt="Imagen actual">');
                    } else {
                        $('#preview_imagen').html('<div class="alert alert-info mt-2">No hay imagen actual</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener datos de la pizza:", error);
                    alert("Error al cargar los datos de la pizza. Por favor, intente nuevamente.");
                }
            });
        });
        
        // Confirmar eliminación
        function confirmarEliminar(id) {
            if(confirm('¿Estás seguro de que deseas desactivar esta pizza? No se borrará permanentemente, solo se desactivará.')) {
                window.location.href = '../../controlles/employee/procesar_pizza.php?accion=eliminar&id=' + id;
            }
        }
    </script>
</body>
</html>