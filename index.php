<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Express - Delivery Rápido</title>
    <link rel="stylesheet" href="assets/cs/index.css">
</head>
<body>
    <header class="header">
        <div class="delivery-phone">
            <i>📞</i> Llama al: 555-123-4567
        </div>
        <div class="logo">Pizza<span>Express</span></div>
        <div class="slogan">¡El sabor llega a tu puerta en 30 minutos o menos!</div>
    </header>
    
    <div class="slider-container">
        <div class="slider">
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Pizza Margherita">
                <div class="slide-content">
                    <h2 class="slide-title">Margherita Clásica</h2>
                    <p class="slide-desc">La auténtica pizza italiana con tomate, mozzarella fresca y albahaca</p>
                </div>
            </div>
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1544982503-9f984c14501a?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Pizza Pepperoni">
                <div class="slide-content">
                    <h2 class="slide-title">Pepperoni Picante</h2>
                    <p class="slide-desc">Deliciosa pizza con pepperoni, queso mozzarella y nuestra salsa picante especial</p>
                </div>
            </div>
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1574126154517-d1e0d89ef734?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Pizza Cuatro Quesos">
                <div class="slide-content">
                    <h2 class="slide-title">Cuatro Quesos</h2>
                    <p class="slide-desc">Una explosión de sabores con mozzarella, gorgonzola, parmesano y fontina</p>
                </div>
            </div>
        </div>
        
        <div class="slider-nav">
            <button class="prev-btn">❮</button>
            <button class="next-btn">❯</button>
        </div>
        
        <div class="slider-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
    
    <div class="auth-buttons">
        <button class="auth-btn login-btn" onclick="window.location.href='views/user/inicio.php'">Pedir Pizza</button>
        <button class="auth-btn signup-btn" onclick="window.location.href='controlles/user/create.php'">Crear Cuenta</button>
    </div>
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
    <footer>
        <div class="footer-links">
            <a href="#">Términos y condiciones</a>
            <a href="#">Política de privacidad</a>
            <a href="#">Contacto</a>
        </div>
        <div class="copyright">
            &copy; 2023 PizzaExpress. Todos los derechos reservados.
        </div>
    </footer>
    
    <script>
        // Slider functionality
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const dots = document.querySelectorAll('.dot');
        
        let currentSlide = 0;
        const slideCount = slides.length;
        
        function goToSlide(slideIndex) {
            slider.style.transform = `translateX(-${slideIndex * 100}%)`;
            currentSlide = slideIndex;
            
            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slideCount;
            goToSlide(currentSlide);
        }
        
        function prevSlide() {
            currentSlide = (currentSlide - 1 + slideCount) % slideCount;
            goToSlide(currentSlide);
        }
        
        // Event listeners
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);
        
        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });
        
        // Auto-slide
        let slideInterval = setInterval(nextSlide, 5000);
        
        // Pause on hover
        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
        sliderContainer.addEventListener('mouseleave', () => {
            slideInterval = setInterval(nextSlide, 5000);
        });
    </script>
</body>
</html>