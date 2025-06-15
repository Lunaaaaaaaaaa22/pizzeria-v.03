<?php
session_start();
// Verificar si hay mensajes de error
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Sabor Auténtico de Italia</title>
    <link rel="stylesheet" href="../../assets/cs/inicio.css">
    <link rel="icon" href="../../assets/img/public/favicon.ico" type="image/x-icon">
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
            <a href="inicio.php" class="nav-link active">Inicio</a>
            <a href="menu.php" class="nav-link">Menú</a>
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

    <header>
        <section class="banner">
            <img src="../../assets/img/public/header.png" alt="Pizzas recién horneadas">
        </section>
    </header>

    <main>
        <!-- Sección de Promociones -->
        <!-- Sección de Promociones -->

            <section class="promotions">
                <h2>Nuestras Promociones</h2>
                <p>Disfruta de nuestras ofertas especiales</p>
                
                <div class="promo-slider">
                    <div class="slider-container" id="promoSlider">
                        <?php
                        require_once '../../models/conexion.php';
                        $con = conectar();
                        
                        $query = "SELECT * FROM promociones WHERE estado = 'activa' AND fecha_fin >= CURDATE()";
                        $result = mysqli_query($con, $query);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while($promo = mysqli_fetch_assoc($result)) {
                                echo '<div class="slide">';
                                echo '<img src="../../' . htmlspecialchars($promo['imagen_url']) . '" alt="' . htmlspecialchars($promo['nombre']) . '">';
                                echo '<div class="promo-info">';
                                echo '<h3>' . htmlspecialchars($promo['nombre']) . '</h3>';
                                echo '<p>' . htmlspecialchars($promo['descripcion']) . '</p>';
                                echo '<div class="promo-footer">';
                                echo '<span class="promo-price">S/ ' . number_format($promo['precio'], 2) . '</span>';
                                echo '<button class="add-promo-btn" 
                                            data-name="' . htmlspecialchars($promo['nombre']) . '"
                                            data-price="' . htmlspecialchars($promo['precio']) . '"
                                            data-image="' . htmlspecialchars($promo['imagen_url']) . '">';
                                echo '<i class="fas fa-cart-plus"></i> Añadir';
                                echo '</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="no-promos">';
                            echo '<p>No hay promociones disponibles en este momento</p>';
                            echo '</div>';
                        }
                        
                        mysqli_close($con);
                        ?>
                    </div>
                    <button class="slider-btn prev" aria-label="Anterior">&#10094;</button>
                    <button class="slider-btn next" aria-label="Siguiente">&#10095;</button>
                </div>
                
                <div class="view-menu-container">
                    <a href="menu.php" class="view-menu-btn">
                        <i class="fas fa-book-open"></i> Ver Menú Completo
                    </a>
                </div>
            </section>
            
        
        <section class="maps-section">
            <div class="maps-container">
                <h2 class="maps-title">Nuestras Ubicaciones</h2>
                <div class="maps-wrapper">
                    <div class="map-box">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7807.232234793578!2d-77.05868453022461!3d-11.9317817!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105d1a80aff1a1d%3A0x729d5d2b8ff615be!2sMultipizzas!5e0!3m2!1ses!2spe!4v1747346266579!5m2!1ses!2spe" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="map-box">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7806.85392923979!2d-77.06980433022463!3d-11.944912999999984!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105d1d3c8ed25b9%3A0x6a141f9bc7f763c0!2sMuLti%20Pizza!5e0!3m2!1ses!2spe!4v1747346529145!5m2!1ses!2spe" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../../assets/js/inicio.js"></script>

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
</body>
</html>