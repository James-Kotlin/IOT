<?php
/**
 * IoTdelivers - Product Details Page
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$product_id) {
    header('Location: shop.php');
    exit();
}

// Get product details
$product = getProductById($product_id);

if (!$product) {
    header('Location: shop.php');
    exit();
}

$page_title = htmlspecialchars($product['name']) . ' - IoTdelivers';

// Get related products (same category)
$related_products = getProducts($product['category_id'], 4);

// Remove current product from related products
$related_products = array_filter($related_products, function($p) use ($product_id) {
    return $p['id'] != $product_id;
});
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);" class="py-4 text-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2 mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php" class="text-white">Shop</a></li>
                <li class="breadcrumb-item"><a href="shop.php?category=<?php echo $product['category_id']; ?>" class="text-white"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                <li class="breadcrumb-item active text-light"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <!-- Product Image -->
        <div class="col-lg-5 mb-4">
            <div style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 500px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                <i class="fas fa-box fa-10x text-muted"></i>
            </div>
            <div class="row mt-3 g-2">
                <div class="col-3">
                    <div style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 100px; border-radius: 5px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                </div>
                <div class="col-3">
                    <div style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 100px; border-radius: 5px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                </div>
                <div class="col-3">
                    <div style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 100px; border-radius: 5px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                </div>
                <div class="col-3">
                    <div style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 100px; border-radius: 5px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-7">
            <!-- Product Title & Category -->
            <div class="mb-4">
                <span class="badge" style="background-color: <?php echo PRIMARY_COLOR; ?>;"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1 class="mt-3 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
            </div>

            <!-- Price & Stock -->
            <div class="border-top border-bottom py-3 mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <h3 style="color: <?php echo PRIMARY_COLOR; ?>;" class="mb-0">
                            <?php echo formatCurrency($product['price']); ?>
                        </h3>
                        <small class="text-muted">Price in <?php echo CURRENCY_CODE; ?></small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="mb-1">Availability</h5>
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <span class="badge bg-success fs-6">In Stock (<?php echo $product['stock_quantity']; ?> units)</span>
                        <?php else: ?>
                        <span class="badge bg-danger fs-6">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Add to Cart -->
            <div class="mb-4">
                <label class="form-label fw-bold">Quantity</label>
                <div class="input-group mb-3" style="max-width: 150px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- Add to Cart Button -->
            <div class="mb-4">
                <?php if ($product['stock_quantity'] > 0): ?>
                <button class="btn btn-lg w-100 me-2 mb-2" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>)">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <?php else: ?>
                <button class="btn btn-lg w-100 mb-2" disabled>
                    <i class="fas fa-ban"></i> Out of Stock
                </button>
                <?php endif; ?>
                <button class="btn btn-lg btn-outline-secondary w-100">
                    <i class="fas fa-heart"></i> Add to Wishlist
                </button>
            </div>

            <!-- Trust Badges -->
            <div class="row g-3 py-3 border-top">
                <div class="col-6">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt text-success"></i> Authentic Product
                    </small>
                </div>
                <div class="col-6">
                    <small class="text-muted">
                        <i class="fas fa-undo text-success"></i> 30 Days Return
                    </small>
                </div>
                <div class="col-6">
                    <small class="text-muted">
                        <i class="fas fa-lock text-success"></i> Secure Payment
                    </small>
                </div>
                <div class="col-6">
                    <small class="text-muted">
                        <i class="fas fa-truck text-success"></i> Fast Delivery
                    </small>
                </div>
            </div>

            <!-- Share -->
            <div class="mt-4 pt-3 border-top">
                <p class="fw-bold mb-2">Share this product:</p>
                <a href="#" class="btn btn-sm btn-outline-secondary me-2"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#" class="btn btn-sm btn-outline-secondary me-2"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            </div>
        </div>
    </div>
</div>

<!-- Product Description & Specifications -->
<section class="py-5 bg-light">
    <div class="container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                    Description
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">
                    Specifications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                    Reviews
                </button>
            </li>
        </ul>

        <div class="tab-content mt-4">
            <div class="tab-pane fade show active" id="description" role="tabpanel">
                <h4 class="mb-3">Product Description</h4>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <div class="tab-pane fade" id="specs" role="tabpanel">
                <h4 class="mb-3">Specifications</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?> units</p>
                        <p><strong>Warranty:</strong> 12 Months</p>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <h4 class="mb-3">Customer Reviews</h4>
                <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                <button class="btn" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    Write a Review
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($related_products)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 fw-bold">
            Related <span style="color: <?php echo PRIMARY_COLOR; ?>;">Products</span>
        </h2>
        <div class="row g-4">
            <?php foreach (array_slice($related_products, 0, 4) as $rel_product): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card product-card h-100 shadow-sm">
                    <div class="product-image" style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 200px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box fa-4x text-muted"></i>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-bold"><?php echo htmlspecialchars($rel_product['name']); ?></h6>
                        <span class="h6 mb-0 mt-2" style="color: <?php echo PRIMARY_COLOR; ?>;">
                            <?php echo formatCurrency($rel_product['price']); ?>
                        </span>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="product.php?id=<?php echo $rel_product['id']; ?>" class="btn btn-sm w-100" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                            View
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max) || 999;
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function addToCart(productId, productName, price) {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    // Get cart from localStorage
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    
    // Check if product already in cart
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: quantity
        });
    }
    
    // Save to localStorage
    localStorage.setItem('iotdelivers_cart', JSON.stringify(cart));
    
    // Show notification
    alert('Added to cart! Quantity: ' + quantity);
    updateCartCount();
    
    // Reset quantity
    document.getElementById('quantity').value = 1;
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    const count = cart.length;
    const countElement = document.getElementById('cart-count');
    if (countElement) {
        countElement.textContent = count;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
