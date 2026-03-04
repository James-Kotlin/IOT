<?php
/**
 * Header Include File
 * Navigation and common header HTML
 */

if (!isset($conn)) {
    require_once __DIR__ . '/config.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IoTdelivers - Buy IoT Devices, Laptops, CCTV Cameras & Installation Services in Kenya">
    <meta name="keywords" content="IoT devices, laptops, CCTV cameras, Kenya, smart home">
    <meta name="author" content="James - CEO & Founder">
    <meta property="og:title" content="IoTdelivers - Smart Technology Solutions">
    <meta property="og:description" content="Buy IoT devices, laptops, CCTV cameras and professional installation services">
    <meta property="og:type" content="website">
    
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | ' : ''; ?>IoTdelivers - Internet of Things Solutions & Digital Security</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../' : ''; ?>assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container-fluid">
            <!-- Brand Logo -->
            <a class="navbar-brand fw-bold" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../index.php' : 'index.php'; ?>" style="color: <?php echo PRIMARY_COLOR; ?>;">
                <i class="fas fa-router"></i> IoTdelivers
            </a>
            
            <!-- Hamburger Menu -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../index.php' : 'index.php'; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../shop.php' : 'shop.php'; ?>">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../services.php' : 'services.php'; ?>">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../contact.php' : 'contact.php'; ?>">Contact</a>
                    </li>
                    
                    <?php if (isAdminLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">Logout</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (!isAdminLoggedIn() && strpos($_SERVER['REQUEST_URI'], '/admin') === false): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            Cart <span class="badge bg-danger" id="cart-count">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Floating WhatsApp Button -->
    <div class="floating-whatsapp">
        <a href="https://wa.me/254XXXXXXXXX" target="_blank" title="Chat with us on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
