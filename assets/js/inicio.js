document.addEventListener('DOMContentLoaded', function() {
    // ========== ELEMENTOS DEL DOM ==========
    // Carrito
    const cartIcon = document.getElementById('cartIcon');
    const cartDropdown = document.getElementById('cartDropdown');
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartTotal = document.querySelector('.cart-total span');
    const cartCount = document.querySelector('.cart-count');
    const checkoutBtn = document.querySelector('.checkout-btn');
    
    // Login/Usuario
    const loginBtn = document.getElementById('loginBtn');
    const userBtn = document.getElementById('userBtn');
    const loginDropdown = document.getElementById('loginDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    // Slider
    const sliderContainer = document.getElementById('promoSlider');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
    
    // ========== ESTADO ==========
    let cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
    let currentSlide = 0;
    let slideInterval;

    // ========== INICIALIZACIÓN ==========
    // Asegurar que el carrito está cerrado al inicio
    if(cartDropdown) {
        cartDropdown.classList.remove('show-dropdown');
    }
    
    updateCartDisplay();
    initSlider();
    setupPromoButtons();

    // ========== EVENT LISTENERS ==========
    // Carrito
    if(cartIcon) {
        cartIcon.addEventListener('click', toggleCart);
        cartIcon.addEventListener('touchend', toggleCart); // Para dispositivos táctiles
    }
    
    if(checkoutBtn) checkoutBtn.addEventListener('click', processOrder);
    document.addEventListener('click', closeCartOnClickOutside);
    
    // Login/Usuario
    if(loginBtn) loginBtn.addEventListener('click', (e) => toggleDropdown(e, loginDropdown));
    if(userBtn) userBtn.addEventListener('click', (e) => toggleDropdown(e, userDropdown));
    document.addEventListener('click', closeAllDropdowns);
    
    // Slider
    if(prevBtn) prevBtn.addEventListener('click', () => moveSlide(-1));
    if(nextBtn) nextBtn.addEventListener('click', () => moveSlide(1));

    // ========== FUNCIONES DEL SLIDER ==========
    function initSlider() {
        if(slides.length > 0) {
            showSlide(currentSlide);
            startAutoSlide();
            
            if(sliderContainer) {
                sliderContainer.addEventListener('mouseenter', pauseAutoSlide);
                sliderContainer.addEventListener('mouseleave', startAutoSlide);
            }
        }
    }

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.style.display = i === index ? 'block' : 'none';
        });
    }

    function moveSlide(direction) {
        currentSlide = (currentSlide + direction + slides.length) % slides.length;
        showSlide(currentSlide);
        resetAutoSlide();
    }

    function startAutoSlide() {
        slideInterval = setInterval(() => moveSlide(1), 6000);
    }

    function pauseAutoSlide() {
        clearInterval(slideInterval);
    }

    function resetAutoSlide() {
        pauseAutoSlide();
        startAutoSlide();
    }

    function setupPromoButtons() {
        document.querySelectorAll('.add-promo-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                addPromoToCart(this);
            });
        });
    }

    function addPromoToCart(button) {
        const promo = {
            name: button.getAttribute('data-name'),
            price: parseFloat(button.getAttribute('data-price')),
            image: button.getAttribute('data-image') || '../../assets/img/public/pizza-default.jpg'
        };
        
        addToCart(promo);
        showNotification(`${promo.name} añadida al carrito`, 'success');
        animateAddButton(button);
    }

    function animateAddButton(button) {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Añadido';
        button.classList.add('added');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('added');
        }, 2000);
    }

    // ========== FUNCIONES DEL CARRITO ==========
    function toggleCart(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Cerrar otros dropdowns primero
        closeAllDropdowns();
        
        // Alternar visibilidad del carrito
        cartDropdown.classList.toggle('show-dropdown');
    }

    function closeCartOnClickOutside(e) {
        const clickedInsideCart = [cartIcon, cartDropdown].some(element => 
            element && element.contains(e.target)
        );
        
        if (!clickedInsideCart) {
            cartDropdown.classList.remove('show-dropdown');
        }
    }

    function addToCart(item) {
        const existingItem = cart.find(i => i.name === item.name);
        
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                id: Date.now().toString(),
                name: item.name,
                price: item.price,
                image: item.image,
                quantity: 1
            });
        }
        
        updateCartDisplay();
    }

    function removeFromCart(itemId) {
        cart = cart.filter(item => item.id !== itemId);
        updateCartDisplay();
        showNotification('Item removido del carrito', 'warning');
    }

    function adjustQuantity(itemId, change) {
        const item = cart.find(item => item.id === itemId);
        if (item) {
            item.quantity += change;
            if (item.quantity < 1) {
                removeFromCart(itemId);
            } else {
                updateCartDisplay();
            }
        }
    }

    function processOrder() {
        if (cart.length === 0) {
            showNotification('El carrito está vacío', 'error');
            return;
        }
        
        showNotification(`Pedido realizado! Total: ${cartTotal.textContent}`, 'success');
        cart = [];
        updateCartDisplay();
    }

    function updateCartDisplay() {
        // Actualizar contador
        if(cartCount) {
            cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
        }
        
        // Actualizar items del carrito
        if(cartItemsContainer) {
            cartItemsContainer.innerHTML = cart.length === 0
                ? '<p class="empty-cart">Tu carrito está vacío</p>'
                : cart.map(item => `
                    <div class="cart-item">
                        <div class="item-info">
                            <span class="item-name">${item.name}</span>
                            <span class="item-price">S/ ${(item.price * item.quantity).toFixed(2)}</span>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn minus" data-id="${item.id}">-</button>
                            <span>${item.quantity}</span>
                            <button class="quantity-btn plus" data-id="${item.id}">+</button>
                            <button class="remove-btn" data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
        }

        // Actualizar total
        if(cartTotal) {
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = `S/ ${total.toFixed(2)}`;
        }
        
        // Mostrar/ocultar botón de checkout
        if(checkoutBtn) {
            checkoutBtn.style.display = cart.length ? 'block' : 'none';
        }
        
        // Guardar en localStorage
        localStorage.setItem('pizzaCart', JSON.stringify(cart));
    }

    // Manejar acciones del carrito
    if(cartItemsContainer) {
        cartItemsContainer.addEventListener('click', function(e) {
            const target = e.target.closest('.remove-btn') || e.target.closest('.quantity-btn');
            
            if (!target) return;
            
            if (target.classList.contains('remove-btn')) {
                removeFromCart(target.getAttribute('data-id'));
            } else if (target.classList.contains('quantity-btn')) {
                adjustQuantity(
                    target.getAttribute('data-id'),
                    target.classList.contains('plus') ? 1 : -1
                );
            }
        });
    }

    // ========== FUNCIONES DE LOGIN/USUARIO ==========
    function toggleDropdown(e, dropdown) {
        e.stopPropagation();
        
        // Cerrar el carrito si está abierto
        if(cartDropdown) {
            cartDropdown.classList.remove('show-dropdown');
        }
        
        dropdown.classList.toggle('show-dropdown');
        
        // Cerrar otros dropdowns
        if(dropdown === loginDropdown && userDropdown) userDropdown.classList.remove('show-dropdown');
        if(dropdown === userDropdown && loginDropdown) loginDropdown.classList.remove('show-dropdown');
    }

    function closeAllDropdowns() {
        // Cerrar todos los dropdowns
        const allDropdowns = [loginDropdown, userDropdown, cartDropdown];
        allDropdowns.forEach(dropdown => {
            if(dropdown) dropdown.classList.remove('show-dropdown');
        });
    }

    // ========== FUNCIONES DE NOTIFICACIÓN ==========
    function showNotification(message, type = 'success', duration = 3000) {
        // Cerrar notificaciones anteriores
        const oldNotifications = document.querySelectorAll('.cart-notification');
        oldNotifications.forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = `cart-notification ${type}`;
        
        const icon = type === 'error' ? 'fa-times' : 
                    type === 'warning' ? 'fa-exclamation' : 'fa-check';
        
        notification.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Animación de entrada
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 10);
        
        // Ocultar después de la duración
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(20px)';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
});