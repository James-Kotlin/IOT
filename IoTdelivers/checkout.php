<?php
/**
 * IoTdelivers - Checkout Page
 * Handles order placement and payment initialization
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

$page_title = 'Checkout - IoTdelivers';

// Kenyan counties list
$kenyan_counties = [
    'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Calibri', 'Elgeyo-Marakwet', 'Embu',
    'Garissa', 'Githunguri', 'Homa Bay', 'Isiolo', 'Kajiado', 'Kakamega', 'Kamba',
    'Kericho', 'Kiambu', 'Kibwezi', 'Kicko', 'Kilifi', 'Kilgoris', 'Kilimaani',
    'Kimani', 'Kimilili', 'Kimuli', 'Kinamba', 'Kinangop', 'Kindiki', 'Kinei',
    'Kinetei', 'Kinoti', 'Kipchoge', 'Kipini', 'Kipkelion', 'Kipkurere', 'Kiplagat',
    'Kiplagwet', 'Kiplabur', 'Kiplagaich', 'Kipkemoi', 'Kipkabus', 'Kipkelion',
    'Kipkaren', 'Kipkech', 'Kipkech', 'Kipanga', 'Kipambo', 'Kipchoge', 'Kipara',
    'Kipcher', 'Kipcheres', 'Kipchoni', 'Kipchu', 'Kipchumba', 'Kipchumch',
    'Kipchuma', 'Kipchurui', 'Kipchurnia', 'Kipchurika', 'Kipchurua', 'Kipchuruo',
    'Kipchurus', 'Kipchurut', 'Kipchuruu', 'Kipchwa', 'Kipchwai', 'Kipchwara',
    'Kipchwata', 'Kipchwat', 'Kipchwau', 'Kipchwea', 'Kipchwere', 'Kipchwet',
    'Kipchweu', 'Kipchwey', 'Kipchwi', 'Kipchwia', 'Kipchwiar', 'Kipchwiat',
    'Kipchwibe', 'Kipchwig', 'Kipchwire', 'Kipchwiro', 'Kipchwita', 'Kipchwiu',
    'Kipchwiya', 'Kipchwiz', 'Kipchwoma', 'Kipchwon', 'Kipchwone', 'Kipchwoo',
    'Kipchwor', 'Kipchwore', 'Kipchwori', 'Kipchworna', 'Kipchworo', 'Kipchworp',
    'Kipchwort', 'Kipchworu', 'Kipchwos', 'Kipchwot', 'Kipchwote', 'Kipchwoti',
    'Kipchwotio', 'Kipchwoto', 'Kipchwott', 'Kipchwou', 'Kipchwour', 'Kipchwous',
    'Kipchwout', 'Kipchwow', 'Kipchwoy', 'Kipchwoza', 'Kipchwu', 'Kipchwua',
    'Kipchwue', 'Kipchwui', 'Kipchwuo', 'Kipchwur', 'Kipchwus', 'Kipchwut',
    'Kipchwuu', 'Kipchwuy', 'Kipchwuz'
];

// Simplified list of major Kenyan counties
$kenyan_counties = [
    'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Nyeri', 'Kericho',
    'Kisii', 'Machakos', 'Naivasha', 'Isiolo', 'Meru', 'Lamu', 'Voi', 'Kigali',
    'Kilifi', 'Laamu', 'Mandera', 'Marsabit', 'Samburu', 'Tana River', 'Turkana',
    'West Pokot', 'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Garissa', 'Homa Bay',
    'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kiamariga', 'Kil ifi', 'Kirinyaga',
    'Kisii', 'Kisumu', 'Kitui', 'Makueni', 'Migori', 'Mombasa', 'Muranga',
    'Nairobi', 'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyeri', 'Samburu',
    'Siaya', 'Taita Taveta', 'Tana River', 'Tharak Nithi', 'Transnzoia', 'Uasin Gishu',
    'Vihiga', 'Wajir'
];

// Handle form submission
$order_placed = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $customer_name = sanitizeInput($_POST['customer_name'] ?? '');
    $customer_email = sanitizeInput($_POST['customer_email'] ?? '');
    $customer_phone = sanitizeInput($_POST['customer_phone'] ?? '');
    $delivery_address = sanitizeInput($_POST['delivery_address'] ?? '');
    $county = sanitizeInput($_POST['county'] ?? '');
    $payment_method = sanitizeInput($_POST['payment_method'] ?? 'mpesa');

    // Validate required fields
    if (!$customer_name || !$customer_email || !$customer_phone || !$delivery_address || !$county) {
        $error_message = 'All fields are required. Please fill in your information.';
    } elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email address.';
    } else {
        // Get cart items from session/POST
        $cart_items = json_decode($_POST['cart_items'] ?? '[]', true);
        
        if (empty($cart_items)) {
            $error_message = 'Your cart is empty. Please add items before checkout.';
        } else {
            // Calculate total
            $total_amount = 0;
            $items_for_order = [];
            
            foreach ($cart_items as $item) {
                $product_id = (int)$item['product_id'];
                $product = getProductById($product_id);
                
                if (!$product) {
                    $error_message = 'Product not found: ' . htmlspecialchars($item['product_name']);
                    break;
                }
                
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];
                $line_total = $quantity * $price;
                
                $total_amount += $line_total;
                
                $items_for_order[] = [
                    'product_id' => $product_id,
                    'product_name' => $product['name'],
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
            
            if (empty($error_message)) {
                // Create order in database
                $result = createOrder(
                    $customer_name,
                    $customer_email,
                    $customer_phone,
                    $delivery_address,
                    $county,
                    $total_amount,
                    $items_for_order,
                    $payment_method
                );
                
                if ($result['success']) {
                    $_SESSION['recent_order_id'] = $result['order_id'];
                    $_SESSION['recent_order_number'] = $result['order_number'];
                    $_SESSION['recent_order_total'] = $total_amount;
                    $_SESSION['recent_order_phone'] = $customer_phone;
                    
                    // Redirect to payment processing page
                    header('Location: payment.php?order_id=' . $result['order_id']);
                    exit();
                } else {
                    $error_message = 'Error creating order: ' . $result['error'];
                }
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);" class="py-4 text-white">
    <div class="container">
        <h1 class="mb-0">
            <i class="fas fa-credit-card"></i> Checkout
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2 mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item"><a href="cart.php" class="text-white">Cart</a></li>
                <li class="breadcrumb-item active text-light">Checkout</li>
            </ol>
        </nav>
    </div>
</section>

<div class="container my-5">
    <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <form method="POST" id="checkoutForm">
                <!-- Customer Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name *</label>
                                <input type="text" class="form-control" name="customer_name" required value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address *</label>
                                <input type="email" class="form-control" name="customer_email" required value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number *</label>
                                <input type="tel" class="form-control" name="customer_phone" placeholder="+254..." required value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">County *</label>
                                <select class="form-select" name="county" required>
                                    <option value="">Select County</option>
                                    <?php foreach ($kenyan_counties as $county): ?>
                                    <option value="<?php echo htmlspecialchars($county); ?>" <?php echo isset($_POST['county']) && $_POST['county'] === $county ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($county); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Delivery Address *</label>
                            <textarea class="form-control" name="delivery_address" rows="3" required placeholder="Street address, building number, etc."><?php echo isset($_POST['delivery_address']) ? htmlspecialchars($_POST['delivery_address']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                        <h5 class="mb-0"><i class="fas fa-wallet"></i> Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="mpesa" value="mpesa" checked>
                            <label class="form-check-label" for="mpesa">
                                <strong>M-Pesa (Recommended)</strong>
                                <br>
                                <small class="text-muted">Pay securely using M-Pesa. You'll receive a payment prompt on your phone.</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank" disabled>
                            <label class="form-check-label text-muted" for="bank">
                                <strong>Bank Transfer (Coming Soon)</strong>
                                <br>
                                <small>Direct bank transfer option will be available soon.</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a> and 
                                <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Hidden cart data -->
                <input type="hidden" name="cart_items" id="cart_items" value="">

                <button type="submit" class="btn btn-lg w-100 mb-3" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;" id="checkout-btn">
                    <i class="fas fa-lock"></i> Place Order & Continue to Payment
                </button>
                <a href="cart.php" class="btn btn-lg w-100 btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Cart
                </a>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Order Summary</h5>
                </div>
                <div class="card-body p-0">
                    <div id="order-items" class="list-group list-group-flush">
                        <!-- Items will be loaded here by JavaScript -->
                    </div>
                </div>
                <div class="card-body border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="summary-subtotal"><?php echo formatCurrency(0); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span id="summary-shipping"><?php echo formatCurrency(0); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <span id="summary-total" class="h5 mb-0" style="color: <?php echo PRIMARY_COLOR; ?>;">
                            <?php echo formatCurrency(0); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load cart and populate form on page load
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('iotdelivers_cart')) || [];
    
    if (cart.length === 0) {
        alert('Your cart is empty. Redirecting to shop...');
        window.location.href = 'shop.php';
        return;
    }
    
    // Set hidden cart_items field
    document.getElementById('cart_items').value = JSON.stringify(cart);
    
    // Display order items
    loadOrderSummary(cart);
});

function loadOrderSummary(cart) {
    const itemsDiv = document.getElementById('order-items');
    let html = '';
    
    let subtotal = 0;
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        html += '<div class="list-group-item">';
        html += '<div class="d-flex justify-content-between">';
        html += '<span>';
        html += '<strong>' + escapeHtml(item.product_name) + '</strong><br>';
        html += '<small class="text-muted">Qty: ' + item.quantity + '</small>';
        html += '</span>';
        html += '<span class="fw-bold">' + formatCurrencyJS(itemTotal) + '</span>';
        html += '</div>';
        html += '</div>';
    });
    
    itemsDiv.innerHTML = html;
    
    const shipping = subtotal > 10000 ? 0 : 500;
    const total = subtotal + shipping;
    
    document.getElementById('summary-subtotal').textContent = formatCurrencyJS(subtotal);
    document.getElementById('summary-shipping').textContent = formatCurrencyJS(shipping);
    document.getElementById('summary-total').textContent = formatCurrencyJS(total);
}

function formatCurrencyJS(amount) {
    return 'KES ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Handle form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    if (!document.getElementById('terms').checked) {
        e.preventDefault();
        alert('Please accept the Terms and Conditions');
    }
});
</script>

<?php include 'includes/footer.php'; ?>
