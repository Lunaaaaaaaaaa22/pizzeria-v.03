document.addEventListener('DOMContentLoaded', function() {
    // ========== ELEMENTOS DEL DOM ==========
    const cartIcon = document.getElementById('cartIcon');
    const cartDropdown = document.getElementById('cartDropdown');
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartTotal = document.querySelector('.cart-total span');
    const cartCount = document.querySelector('.cart-count');
    const checkoutBtn = document.querySelector('.checkout-btn');

    // ========== ELEMENTOS PARA LOGIN ==========
    const loginBtn = document.getElementById('loginBtn');
    const userBtn = document.getElementById('userBtn');
    const loginDropdown = document.getElementById('loginDropdown');
    const userDropdown = document.getElementById('userDropdown');

    // Verificación de elementos (para depuración)
    console.log('Elementos encontrados:', {
        cartIcon,
        cartDropdown,
        loginBtn,
        userBtn,
        loginDropdown,
        userDropdown
    });

    // ========== EVENT LISTENERS PARA LOGIN/USUARIO ==========
    if(loginBtn && loginDropdown) {
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Botón login clickeado');
            
            loginDropdown.classList.toggle('show-dropdown');
            if(userDropdown) userDropdown.classList.remove('show-dropdown');
            if(cartDropdown) cartDropdown.style.display = 'none';
        });
    } else {
        console.error('No se encontró loginBtn o loginDropdown');
    }

    if(userBtn && userDropdown) {
        userBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            userDropdown.classList.toggle('show-dropdown');
            if(loginDropdown) loginDropdown.classList.remove('show-dropdown');
        });
    }

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container') && !e.target.closest('.nav-cart-container')) {
            if(loginDropdown) loginDropdown.classList.remove('show-dropdown');
            if(userDropdown) userDropdown.classList.remove('show-dropdown');
        }
    });

    // ========== ESTADO DEL CARRITO ==========
    let cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];

    // ========== INICIALIZACIÓN ==========
    updateCartDisplay();

    // ========== EVENT LISTENERS DEL CARRITO ==========
    if(cartIcon && cartDropdown) {
        cartIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleCart();
            if(loginDropdown) loginDropdown.classList.remove('show-dropdown');
        });
    }

    // Cerrar carrito si clic fuera del carrito o icono
    document.addEventListener('click', function(e) {
        if (cartDropdown && cartIcon && 
            !cartDropdown.contains(e.target) && 
            !cartIcon.contains(e.target)) {
            cartDropdown.style.display = 'none';
        }
    });

    // Delegación para agregar pizzas del menú
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-cart')) {
            const button = e.target;
            const pizzaCard = button.closest('.pizza-card');
            const sizeSelect = pizzaCard.querySelector('.pizza-size');
            const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];

            const pizzaId = button.getAttribute('data-pizza-id');
            const pizzaName = button.getAttribute('data-name');
            const pizzaSize = selectedOption.value;
            const pizzaPrice = parseFloat(selectedOption.getAttribute('data-price'));
            const pizzaImg = pizzaCard.querySelector('.pizza-image img').src;

            addToCart({
                id: `menu_${pizzaId}_${pizzaSize}`,
                name: pizzaName,
                size: pizzaSize,
                price: pizzaPrice,
                quantity: 1,
                image: pizzaImg,
            });
            
            animateAddToCart(button);
        }
    });

    // Botones dentro del carrito
    if(cartItemsContainer) {
        cartItemsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-btn') || e.target.closest('.remove-btn')) {
                const button = e.target.classList.contains('remove-btn') ? e.target : e.target.closest('.remove-btn');
                removeFromCart(button.getAttribute('data-id'));
            } else if (e.target.classList.contains('quantity-btn') || e.target.closest('.quantity-btn')) {
                const button = e.target.classList.contains('quantity-btn') ? e.target : e.target.closest('.quantity-btn');
                adjustQuantity(
                    button.getAttribute('data-id'),
                    button.classList.contains('plus') ? 1 : -1
                );
            }
        });
    }

    if(checkoutBtn) {
        checkoutBtn.addEventListener('click', processOrder);
    }

    // ========== FUNCIONES PRINCIPALES ==========
    function toggleCart() {
        cartDropdown.style.display = cartDropdown.style.display === 'block' ? 'none' : 'block';
    }

    function addToCart(item) {
        const existingItem = cart.find(i => i.id === item.id);
        
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push(item);
        }

        updateCartDisplay();
        showNotification(`${item.name} (${item.size}) añadida`, 'success');
    }

    function removeFromCart(itemId) {
        cart = cart.filter(item => item.id !== itemId);
        updateCartDisplay();
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
            showNotification('Tu carrito está vacío', 'error');
            return;
        }

        showNotification(`Pedido realizado! Total: ${cartTotal.textContent}`, 'success');
        cart = [];
        updateCartDisplay();
        cartDropdown.style.display = 'none';
    }

    // ========== FUNCIONES DE UI ==========
    function updateCartDisplay() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        if(cartCount) cartCount.textContent = totalItems;

        if (cartItemsContainer) {
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<p class="empty-cart">Tu carrito está vacío</p>';
                if(checkoutBtn) checkoutBtn.style.display = 'none';
                if(cartTotal) cartTotal.textContent = '$0.00';
            } else {
                cartItemsContainer.innerHTML = cart.map(item => `
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
                
                if(checkoutBtn) checkoutBtn.style.display = 'block';
                
                const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                if(cartTotal) cartTotal.textContent = `S/ ${total.toFixed(2)}`;
            }
        }

        localStorage.setItem('pizzaCart', JSON.stringify(cart));
    }

    function animateAddToCart(button) {
        const originalText = button.textContent;
        button.textContent = '✓ Agregado';
        button.style.backgroundColor = '#2ecc71';

        setTimeout(() => {
            button.textContent = originalText;
            button.style.backgroundColor = '';
        }, 2000);
    }

    function showNotification(message, type = 'success', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `cart-notification ${type}`;
        
        const icon = type === 'error' ? 'fa-times' : 
                    type === 'warning' ? 'fa-exclamation' : 'fa-check';
        
        notification.innerHTML = `
            <span>${message}</span>
            <i class="fas ${icon}"></i>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, duration);
    }
});