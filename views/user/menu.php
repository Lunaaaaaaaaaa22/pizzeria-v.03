<?php
include('../../models/conexion.php');
$conn = conectar();


$sql = "SELECT p.id AS pizza_id, p.imagen_url, i.nombre, i.descripcion, 
               pp.tamaño, pp.precio
        FROM pizza p
        JOIN inventario i ON p.inventario_id = i.id
        JOIN pizza_precio pp ON p.id = pp.pizza_id
        WHERE p.activo = 1 AND i.activo = 1
        ORDER BY p.id, pp.tamaño";
$resultado = mysqli_query($conn, $sql);

$pizzas = [];
while ($row = mysqli_fetch_assoc($resultado)) {
    $pizzas[$row['pizza_id']]['nombre'] = $row['nombre'];
    $pizzas[$row['pizza_id']]['descripcion'] = $row['descripcion'];
    $pizzas[$row['pizza_id']]['imagen_url'] = $row['imagen_url']; // <-- aquí agregas la imagen
    $pizzas[$row['pizza_id']]['precios'][$row['tamaño']] = $row['precio'];
}

session_start();
// Verificar si hay mensajes de error
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Completo - El Sabor Auténtico de Italia</title>
    <link rel="icon" href="../../assets/img/public/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/cs/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Barra de navegación superior con carrito -->
    <nav class="top-nav">
        <div class="nav-left">
            <a href="../../index.php">
                <span class="logo">Multiplazas</span>
            </a>
        </div>
        <div class="nav-center">
            <a href="inicio.php" class="nav-link ">Inicio</a>
            <a href="menu.php" class="nav-link active">Menú</a>
        </div>
        <div class="nav-right">
            <!-- Carrito en el nav -->
            <div class="nav-cart-container">
                <div class="cart-icon" id="cartIcon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </div>
                <div class="cart-dropdown" id="cartDropdown">
                    <h3>Tu Carrito</h3>
                    <div class="cart-items">
                        <p class="empty-cart">Tu carrito está vacío</p>
                    </div>
                    <div class="cart-total">
                        <span>Total: $0.00</span>
                    </div>
                    <button class="checkout-btn">Finalizar Compra</button>
                </div>
            </div>
            
            <!-- Sistema de Login/Usuario -->
            <div class="user-dropdown-container">
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <!-- Mostrar cuando el usuario está logueado -->
                    <button class="user-btn" id="userBtn">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="perfil.php">Mi Perfil</a>
                        <a href="historial.php">Mis Pedidos</a>
                        <a href="../../controlles/logout.php">Cerrar Sesión</a>
                    </div>
                <?php else: ?>
                    <!-- Mostrar cuando no hay sesión -->
                    <button class="login-btn" id="loginBtn">
                        <i class="fas fa-user"></i>
                        <span>Login</span>
                    </button>
                    <div class="login-dropdown" id="loginDropdown">
                        <?php if($error): ?>
                            <div class="error-message">
                                <?php echo $error === '1' ? 'Usuario o contraseña incorrectos' : ''; ?>
                            </div>
                        <?php endif; ?>
                        <form action="../../controlles/login.php" method="POST">
                            <input type="text" name="username" placeholder="Usuario" required>
                            <input type="password" name="password" placeholder="Contraseña" required>
                            <button type="submit">Ingresar</button>
                            <a href="../../controlles/create.php" class="register-link">Crear cuenta</a>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="menu-header">
        <div class="header-content">
            <h1>Menú Completo</h1>
            <p>Explora nuestra amplia selección de pizzas artesanales</p>
        </div>
    </header>

    <!-- Contenido principal del menú -->
    <main class="menu-container">
        <section class="pizza-grid">
            <?php foreach ($pizzas as $pizza_id => $pizza) : ?>
                <div class="pizza-card">
                    <div class="pizza-image">
                        <img src="../<?= htmlspecialchars($pizza['imagen_url']) ?>" alt="<?= htmlspecialchars($pizza['nombre']) ?>">                    </div>
                    <div class="pizza-info">
                        <h3><?= htmlspecialchars($pizza['nombre']) ?></h3>
                        <p class="pizza-description"><?= htmlspecialchars($pizza['descripcion']) ?></p>

                        <!-- Obtener ingredientes -->
                        <div class="ingredients">
                            <h4>Ingredientes:</h4>
                            <ul>
                            <?php
                                $sql_ingredientes = "SELECT nombre_ingrediente FROM pizza_ingredientes_manual WHERE pizza_id = $pizza_id";
                                $res_ing = mysqli_query($conn, $sql_ingredientes);
                                while ($ing = mysqli_fetch_assoc($res_ing)) {
                                    echo "<li>" . htmlspecialchars($ing['nombre_ingrediente']) . "</li>";
                                }
                            ?>
                            </ul>
                        </div>

                        <!-- Selector de tamaño -->
                        <div class="size-selector">
                            <select class="pizza-size" data-pizza-id="<?= $pizza_id ?>">
                                <?php foreach ($pizza['precios'] as $tamaño => $precio) : ?>
                                    <option value="<?= $tamaño ?>" data-price="<?= $precio ?>">
                                        <?= ucfirst($tamaño) ?> - $<?= number_format($precio, 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="pizza-footer">
                            <button class="add-to-cart" 
                                    data-pizza-id="<?= $pizza_id ?>"
                                    data-name="<?= htmlspecialchars($pizza['nombre']) ?>">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h2>Multiplaza</h2>
                <p>Las mejores pizzas artesanales con ingredientes frescos y de calidad.</p>
            </div>
            
            <div class="footer-links">
                <h3>Enlaces</h3>
                <ul>
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="menu.php">Menú</a></li>
                    <li><a href="about.php">Sobre Nosotros</a></li>
                </ul>
            </div>
            
            <div class="footer-hours">
                <h3>Horario</h3>
                <p>Lunes – Viernes: 11:00 - 22:00</p>
                <p>Sábado – Domingo: 12:00 - 23:00</p>
            </div>
            
            <div class="footer-contact">
                <h3>Contacto</h3>
                <address>
                    <p><i class="fas fa-map-marker-alt"></i> Av. Siempre Viva 123</p>
                    <p><i class="fas fa-phone"></i> +51 939 977 756</p>
                    <p><i class="fas fa-envelope"></i> multiplazainfo@delirioso.com</p>
                </address>
                <div class="social-links">
                    <a href="https://www.facebook.com/multipizzaa/?locale=es_LA" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com/@multipizza2" target="_blank"><i class="fab fa-tiktok"></i></a>
                    <a href="https://wa.me/939977756" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2025 Delirioso. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../../assets/js/menu.js"></script>
</body>
</html>