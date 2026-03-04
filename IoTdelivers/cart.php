<?php
/**
 * IoTdelivers - Shopping Cart Page
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

$page_title = 'Shopping Cart - IoTdelivers';

// Handle AJAX requests for cart operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = sanitizeInput($_POST['action']);
    
    if ($action === 'remove') {
        $product_id = (int)$_POST['product_id'];
        // Cart is handled in JavaScript/localStorage
        echo json_encode(['success' => true]);
    } elseif ($action === 'update') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        // Cart is handled in JavaScript/localStorage
        echo json_encode(['success' => true]);
    }
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);" class="py-4 text-white">
    <div class="container">
        <h1 class="mb-0">
            <i class="fas fa-shopping-cart"></i> Shopping Cart
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2 mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-light">Cart</li>
            </ol>
        </nav>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-inbox"></i> Cart Items</h5>
                </div>
                <div class="card-body p-0">
                    <div id="cart-items" class="table-responsive">
                        <!-- Cart items will be loaded here by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span id="subtotal"><?php echo formatCurrency(0); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span id="shipping"><?php echo formatCurrency(0); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tax (0%):</span>
                        <span id="tax"><?php echo formatCurrency(0); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="total" style="color: <?php echo PRIMARY_COLOR; ?>;">
                            <?php echo formatCurrency(0); ?>
                        </strong>
                    </div>

                    <a href="checkout.php" class="btn w-100 mb-2" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </a>
                    <a href="shop.php" class="btn w-100 btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Offers -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-tag"></i> Special Offers</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Free shipping on orders over KES 10,000</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Empty Cart Message (Initially Hidden) -->
<div id="empty-cart-message" class="container my-5" style="display: none;">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <i class="fas fa-shopping-bag fa-5x text-muted mb-3"></i>
            <h3>Your Cart is Empty</h3>
            <p class="text-muted mb-4">Start shopping to add items to your cart!</p>
            <a href="shop.php" class="btn btn-lg" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                <i class="fas fa-shopping-bag"></i> Start Shopping
            </a>
        </div>
    </div>
</div>

<script>
// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});

function loadCart() {
    const cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    const cartItemsDiv = document.getElementById('cart-items');
    
    if (cart.length === 0) {
        document.getElementById('empty-cart-message').style.display = 'block';
        cartItemsDiv.innerHTML = '';
        updateCartSummary([]);
        return;
    }
    
    document.getElementById('empty-cart-message').style.display = 'none';
    
    let html = '<table class="table table-hover mb-0">';
    html += '<thead style="background-color: #f8f9fa;">';
    html += '<tr>';
    html += '<th>Product</th>';
    html += '<th>Price</th>';
    html += '<th>Quantity</th>';
    html += '<th>Total</th>';
    html += '<th></th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        html += '<tr>';
        html += '<td>';
        html += '<strong>' + escapeHtml(item.product_name) + '</strong>';
        html += '</td>';
        html += '<td>' + formatCurrencyJS(item.price) + '</td>';
        html += '<td>';
        html += '<div class="input-group" style="width: 100px;">';
        html += '<button class="btn btn-sm btn-outline-secondary" type="button" onclick="updateQuantity(' + index + ', ' + item.quantity + ' - 1)">-</button>';
        html += '<input type="number" class="form-control form-control-sm text-center" value="' + item.quantity + '" readonly>';
        html += '<button class="btn btn-sm btn-outline-secondary" type="button" onclick="updateQuantity(' + index + ', ' + item.quantity + ' + 1)">+</button>';
        html += '</div>';
        html += '</td>';
        html += '<td><strong>' + formatCurrencyJS(itemTotal) + '</strong></td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-danger" onclick="removeFromCart(' + index + ')">';
        html += '<i class="fas fa-trash"></i>';
        html += '</button>';
        html += '</td>';
        html += '</tr>';
    });
    
    html += '</tbody>';
    html += '</table>';
    
    cartItemsDiv.innerHTML = html;
    updateCartSummary(cart);
    updateCartCount();
}

function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('iotdelivers_cart', JSON.stringify(cart));
    loadCart();
}

function updateQuantity(index, newQuantity) {
    if (newQuantity < 1) return;
    
    let cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    cart[index].quantity = newQuantity;
    localStorage.setItem('iotdelivers_cart', JSON.stringify(cart));
    loadCart();
}

function updateCartSummary(cart) {
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    
    const shipping = subtotal > 10000 ? 0 : 500;
    const tax = 0;
    const total = subtotal + shipping + tax;
    
    document.getElementById('subtotal').textContent = formatCurrencyJS(subtotal);
    document.getElementById('shipping').textContent = formatCurrencyJS(shipping);
    document.getElementById('tax').textContent = formatCurrencyJS(tax);
    document.getElementById('total').textContent = formatCurrencyJS(total);
}

function formatCurrencyJS(amount) {
    return 'KES ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
