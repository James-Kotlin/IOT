<?php
/**
 * IoTdelivers - Homepage
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

$page_title = 'Home - IoT Devices, Laptops & CCTV Solutions';

// Get featured products
$featured_products = getProducts(null, 6, 0, true);

// Get all categories
$categories = getAllCategories();
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);">
    <div class="container h-100 d-flex align-items-center justify-content-center">
        <div class="text-center text-white py-5">
            <h1 class="display-3 fw-bold mb-3">Welcome to IoTdelivers</h1>
            <p class="lead mb-4">Smart Technology Solutions for Home & Business</p>
            <p class="mb-4">IoT Devices • Laptops • CCTV Cameras • Installation Services</p>
            <a href="shop.php" class="btn btn-light btn-lg me-2">
                <i class="fas fa-shopping-bag"></i> Start Shopping
            </a>
            <a href="#services" class="btn btn-outline-light btn-lg">
                <i class="fas fa-wrench"></i> Our Services
            </a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">
            <span style="color: <?php echo PRIMARY_COLOR; ?>;">Browse</span> Our Categories
        </h2>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-md-6 col-lg-3">
                <a href="shop.php?category=<?php echo $category['id']; ?>" class="card category-card h-100 shadow-sm text-decoration-none text-dark">
                    <div class="card-body text-center py-4">
                        <?php
                        $icon_map = [
                            'IoT Devices' => 'fa-router',
                            'Laptops' => 'fa-laptop',
                            'CCTV Cameras' => 'fa-camera',
                            'Accessories' => 'fa-microchip'
                        ];
                        $icon = isset($icon_map[$category['name']]) ? $icon_map[$category['name']] : 'fa-box';
                        ?>
                        <i class="fas <?php echo $icon; ?> fa-3x mb-3" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($category['name']); ?></h5>
                        <p class="card-text small text-muted"><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">
            <span style="color: <?php echo PRIMARY_COLOR; ?>;">Featured</span> Products
        </h2>
        
        <?php if (!empty($featured_products)): ?>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card product-card h-100 shadow-sm">
                    <!-- Product Image -->
                    <div class="product-image" style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 250px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box fa-5x text-muted"></i>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 80) . '...'); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="h5 mb-0" style="color: <?php echo PRIMARY_COLOR; ?>;">
                                <?php echo formatCurrency($product['price']); ?>
                            </span>
                            <span class="badge <?php echo $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm w-100" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">No featured products available at the moment.</p>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <a href="shop.php" class="btn btn-lg" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                View All Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <div style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%); height: 400px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user-tie fa-10x text-white opacity-25"></i>
                </div>
            </div>
            <div class="col-md-6 ps-md-4">
                <h2 class="fw-bold mb-4">
                    About <span style="color: <?php echo PRIMARY_COLOR; ?>;">IoTdelivers</span>
                </h2>
                <p class="lead mb-3">
                    Meet <strong>James</strong>, our CEO & Founder
                </p>
                <p class="mb-3">
                    IoTdelivers is Kenya's leading provider of smart technology solutions. Founded by James, we are committed to bringing cutting-edge IoT devices, high-performance laptops, and professional CCTV security solutions to businesses and individuals across Kenya.
                </p>
                <p class="mb-3">
                    Our mission is to make advanced technology accessible and affordable for everyone. We provide not just products, but complete solutions including professional installation and after-sales support.
                </p>
                <p class="mb-4">
                    With years of expertise in the tech industry, we understand the unique needs of the Kenyan market and deliver solutions tailored to our customers' requirements.
                </p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check-circle" style="color: <?php echo PRIMARY_COLOR; ?>;"></i> <strong>Quality Products</strong> - Only the best brands</li>
                    <li class="mb-2"><i class="fas fa-check-circle" style="color: <?php echo PRIMARY_COLOR; ?>;"></i> <strong>Expert Advice</strong> - Personalized recommendations</li>
                    <li class="mb-2"><i class="fas fa-check-circle" style="color: <?php echo PRIMARY_COLOR; ?>;"></i> <strong>Professional Installation</strong> - Expert service</li>
                    <li><i class="fas fa-check-circle" style="color: <?php echo PRIMARY_COLOR; ?>;"></i> <strong>After-Sales Support</strong> - We're here for you</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">
            Why Choose <span style="color: <?php echo PRIMARY_COLOR; ?>;">IoTdelivers</span>?
        </h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-shipping-fast fa-3x" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                    </div>
                    <h5 class="fw-bold">Fast Delivery</h5>
                    <p class="text-muted">Quick and reliable delivery across Kenya</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-lock fa-3x" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                    </div>
                    <h5 class="fw-bold">Secure Payment</h5>
                    <p class="text-muted">Safe M-Pesa integration and data protection</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-award fa-3x" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                    </div>
                    <h5 class="fw-bold">Quality Assured</h5>
                    <p class="text-muted">Authentic products with warranty</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-headset fa-3x" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                    </div>
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p class="text-muted">Always here to help you</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);">
    <div class="container text-center text-white">
        <h2 class="mb-4 fw-bold">Ready to Upgrade Your Technology?</h2>
        <p class="lead mb-4">Browse our extensive collection of IoT devices, laptops, and CCTV solutions</p>
        <a href="shop.php" class="btn btn-light btn-lg">
            <i class="fas fa-shopping-cart"></i> Shop Now
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
