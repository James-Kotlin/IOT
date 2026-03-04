<?php
/**
 * Footer Include File
 * Footer content and closing HTML tags
 */
?>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-5">
        <div class="container">
            <div class="row mb-4">
                <!-- About Section -->
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3" style="color: <?php echo PRIMARY_COLOR; ?>;">
                        <i class="fas fa-router"></i> IoTdelivers
                    </h5>
                    <p class="small text-muted">
                        Leading provider of IoT devices, laptops, CCTV cameras and professional installation services in Kenya.
                    </p>
                    <p class="small text-muted">
                        Owner: <strong>James</strong> - CEO & Founder
                    </p>
                    <div class="mt-3">
                        <a href="https://facebook.com/iotdelivers" target="_blank" class="text-white me-3">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com/iotdelivers" target="_blank" class="text-white me-3">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://instagram.com/iotdelivers" target="_blank" class="text-white me-3">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://linkedin.com/company/iotdelivers" target="_blank" class="text-white">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled small">
                        <li><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="shop.php" class="text-muted text-decoration-none">Shop</a></li>
                        <li><a href="services.php" class="text-muted text-decoration-none">Services</a></li>
                        <li><a href="contact.php" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li><a href="login.php" class="text-muted text-decoration-none">Login</a></li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Customer Service</h5>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-muted text-decoration-none">Track Order</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Returns & Refunds</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Shipping Info</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Contact Us</h5>
                    <p class="small text-muted mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:+254700000000" class="text-muted text-decoration-none">+254 700 000 000</a>
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:james@iotdelivers.com" class="text-muted text-decoration-none">james@iotdelivers.com</a>
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Nairobi, Kenya
                    </p>
                    <p class="small text-muted">
                        <i class="fas fa-clock me-2"></i>
                        Mon - Fri: 9:00 AM - 6:00 PM
                    </p>
                </div>
            </div>
            
            <hr class="bg-secondary">
            
            <!-- Bottom Footer -->
            <div class="row">
                <div class="col-md-6">
                    <p class="small text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> IoTdelivers. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small text-muted mb-0">
                        Payment Methods: <i class="fab fa-cc-visa"></i> <i class="fab fa-cc-mastercard"></i> 
                        <span style="color: <?php echo PRIMARY_COLOR; ?>;">M-Pesa</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../' : ''; ?>assets/js/main.js"></script>

    <script>
        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        function updateCartCount() {
            // Get cart from session storage or localStorage
            const cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
            const count = cart.length;
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.textContent = count;
            }
        }
    </script>
</body>
</html>
