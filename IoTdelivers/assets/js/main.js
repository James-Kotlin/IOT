/**
 * IoTdelivers - Main JavaScript File
 * Cart functionality, animations, and utility functions
 */

// ==================== CART MANAGEMENT ====================

/**
 * Add item to cart
 */
function addToCart(productId, productName, price) {
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    
    // Check if product already in cart
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            product_id: productId,
            product_name: productName,
            price: parseFloat(price),
            quantity: 1
        });
    }
    
    localStorage.setItem('iotdelivers_cart', JSON.stringify(cart));
    updateCartCount();
    showNotification('Added to cart!', 'success');
}

/**
 * Remove item from cart
 */
function removeFromCart(productId) {
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    cart = cart.filter(item => item.product_id !== productId);
    localStorage.setItem('iotdelivers_cart', JSON.stringify(cart));
    updateCartCount();
    
    // Reload cart display if on cart page
    if (typeof loadCart === 'function') {
        loadCart();
    }
}

/**
 * Update cart item quantity
 */
function updateCartQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    const item = cart.find(item => item.product_id === productId);
    
    if (item) {
        item.quantity = quantity;
        localStorage.setItem('iotdelivers_cart', JSON.stringify(cart));
        
        if (typeof loadCart === 'function') {
            loadCart();
        }
    }
}

/**
 * Clear entire cart
 */
function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        localStorage.removeItem('iotdelivers_cart');
        updateCartCount();
        showNotification('Cart cleared!', 'info');
        
        if (typeof loadCart === 'function') {
            loadCart();
        }
    }
}

/**
 * Get cart total
 */
function getCartTotal() {
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

/**
 * Get cart item count
 */
function getCartCount() {
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    return cart.reduce((count, item) => count + item.quantity, 0);
}

/**
 * Update cart count display in navbar
 */
function updateCartCount() {
    const countElement = document.getElementById('cart-count');
    if (countElement) {
        const count = getCartCount();
        countElement.textContent = count;
        countElement.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

// ==================== NOTIFICATIONS ====================

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 70px; right: 20px; z-index: 1050; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const container = document.createElement('div');
    container.innerHTML = alertHtml;
    document.body.appendChild(container.firstElementChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert.show:last-of-type');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// ==================== FORM VALIDATION ====================

/**
 * Validate email
 */
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validate phone number (Kenyan format)
 */
function validatePhoneNumber(phone) {
    const regex = /^(\+254|0)[0-9]{9}$/;
    return regex.test(phone.replace(/\s/g, ''));
}

/**
 * Validate form field
 */
function validateField(fieldId, validationType = 'text') {
    const field = document.getElementById(fieldId);
    if (!field) return false;
    
    const value = field.value.trim();
    
    if (!value) {
        showFieldError(fieldId, 'This field is required');
        return false;
    }
    
    switch (validationType) {
        case 'email':
            if (!validateEmail(value)) {
                showFieldError(fieldId, 'Invalid email address');
                return false;
            }
            break;
        case 'phone':
            if (!validatePhoneNumber(value)) {
                showFieldError(fieldId, 'Invalid phone number');
                return false;
            }
            break;
        case 'number':
            if (isNaN(value) || value < 0) {
                showFieldError(fieldId, 'Invalid number');
                return false;
            }
            break;
    }
    
    clearFieldError(fieldId);
    return true;
}

/**
 * Show field error
 */
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.add('is-invalid');
    clearFieldError(fieldId); // Clear existing errors
    
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback d-block';
    feedback.textContent = message;
    feedback.id = fieldId + '-error';
    
    field.parentElement.appendChild(feedback);
}

/**
 * Clear field error
 */
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.remove('is-invalid');
    const feedback = document.getElementById(fieldId + '-error');
    if (feedback) {
        feedback.remove();
    }
}

// ==================== FORMATTING ====================

/**
 * Format currency for display
 */
function formatCurrency(amount) {
    return 'KES ' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-KE', options);
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Escape HTML characters
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('Failed to copy', 'error');
    });
}

/**
 * Debounce function
 */
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay);
    };
}

/**
 * Check if user is online
 */
function isOnline() {
    return navigator.onLine;
}

// ==================== PAGE LOAD ====================

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    updateCartCount();
    
    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Add loading state to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }
        });
    });
});

// ==================== WINDOW EVENTS ====================

/**
 * Handle online/offline status
 */
window.addEventListener('online', () => {
    showNotification('Connection restored!', 'success');
});

window.addEventListener('offline', () => {
    showNotification('No internet connection', 'warning');
});

/**
 * Handle before unload
 */
window.addEventListener('beforeunload', (e) => {
    const cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    if (cart.length > 0) {
        e.preventDefault();
        e.returnValue = 'You have items in your cart. Are you sure you want to leave?';
    }
});

// ==================== SEARCH & FILTER ====================

/**
 * Search products
 */
function searchProducts(query) {
    const cards = document.querySelectorAll('.product-card');
    const lowerQuery = query.toLowerCase();
    
    cards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        
        if (title.includes(lowerQuery) || description.includes(lowerQuery)) {
            card.parentElement.style.display = '';
        } else {
            card.parentElement.style.display = 'none';
        }
    });
}

/**
 * Debounced search
 */
const debouncedSearch = debounce(searchProducts, 300);

// Add search input listener
const searchInput = document.getElementById('search-input');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        debouncedSearch(e.target.value);
    });
}

// ==================== ANIMATION UTILITIES ====================

/**
 * Animate element on scroll
 */
function animateOnScroll() {
    const elements = document.querySelectorAll('.fade-in, .slide-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'none';
                setTimeout(() => {
                    entry.target.style.animation = '';
                }, 10);
            }
        });
    }, { threshold: 0.1 });
    
    elements.forEach(el => observer.observe(el));
}

// ==================== CONSOLE UTILITIES ====================

// Suppress console in production
if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
    console.log = function() {};
    console.warn = function() {};
}

console.log('%c IoTdelivers ', 'background: #6A0DAD; color: white; font-weight: bold; padding: 5px 10px; border-radius: 3px;');
console.log('%c Welcome to IoTdelivers! ', 'font-size: 14px; color: #6A0DAD;');
