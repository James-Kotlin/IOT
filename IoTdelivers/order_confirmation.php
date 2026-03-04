<?php
/**
 * Order Confirmation Page
 * Displays order confirmation after successful payment
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

$page_title = 'Order Confirmation - IoTdelivers';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (!$order_id) {
    header('Location: index.php');
    exit();
}

$order = getOrderById($order_id);

if (!$order) {
    header('Location: index.php');
    exit();
}

$order_items = getOrderItems($order_id);

// Clear cart
?>

<?php include 'includes/header.php'; ?>

<!-- Success Message -->
<section style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);" class="py-5 text-white">
    <div class="container text-center">
        <i class="fas fa-check-circle fa-4x mb-3"></i>
        <h1 class="mb-3">Order Confirmed!</h1>
        <p class="lead">Thank you for your purchase at IoTdelivers</p>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Order Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Order Number:</strong><br>
                                <span style="color: <?php echo PRIMARY_COLOR; ?>; font-size: 1.2em; font-weight: bold;">
                                    <?php echo htmlspecialchars($order['order_number']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Order Date:</strong><br>
                                <?php echo date('d M Y, g:i A', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Order Items:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo formatCurrency($item['unit_price']); ?></td>
                                    <td><?php echo formatCurrency($item['total_price']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr style="background-color: #f8f9fa;">
                                    <td colspan="3" class="text-end"><strong>Order Total:</strong></td>
                                    <td><strong style="color: <?php echo PRIMARY_COLOR; ?>;"><?php echo formatCurrency($order['total_amount']); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Shipping Information</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Customer Name:</strong><br>
                        <?php echo htmlspecialchars($order['customer_name']); ?>
                    </p>
                    <p>
                        <strong>Delivery Address:</strong><br>
                        <?php echo htmlspecialchars($order['delivery_address']); ?><br>
                        <?php echo htmlspecialchars($order['county']); ?>, Kenya
                    </p>
                    <p>
                        <strong>Contact Number:</strong><br>
                        <?php echo htmlspecialchars($order['customer_phone']); ?>
                    </p>
                    <p>
                        <strong>Email:</strong><br>
                        <?php echo htmlspecialchars($order['customer_email']); ?>
                    </p>
                </div>
            </div>

            <!-- Order Status -->
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Status</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <strong>Current Status:</strong> 
                        <span class="badge bg-info" style="font-size: 1em;">
                            <?php echo htmlspecialchars($order['order_status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i> What's Next?</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-3">
                            <strong>Order Confirmation</strong>
                            <p class="text-muted mb-0">You'll receive a confirmation email shortly</p>
                        </li>
                        <li class="mb-3">
                            <strong>Processing</strong>
                            <p class="text-muted mb-0">We'll process and pack your order</p>
                        </li>
                        <li class="mb-3">
                            <strong>Delivery</strong>
                            <p class="text-muted mb-0">Your order will be shipped within 2-3 business days</p>
                        </li>
                        <li>
                            <strong>Delivery Updates</strong>
                            <p class="text-muted mb-0">Track your package via email and SMS</p>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-headset"></i> Need Help?</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        <i class="fas fa-phone"></i> 
                        <a href="tel:+254700000000" class="text-decoration-none">Call Support</a>
                    </p>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-envelope"></i> 
                        <a href="mailto:james@iotdelivers.com" class="text-decoration-none">Email Support</a>
                    </p>
                    <p class="text-muted small">
                        <i class="fab fa-whatsapp"></i> 
                        <a href="https://wa.me/254700000000" target="_blank" class="text-decoration-none">WhatsApp Chat</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="text-center">
                <a href="index.php" class="btn btn-lg me-2" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="shop.php" class="btn btn-lg btn-outline-secondary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Clear cart
localStorage.removeItem('iotdelivers_cart');
// Update cart count
if (document.getElementById('cart-count')) {
    document.getElementById('cart-count').textContent = '0';
}
</script>

<?php include 'includes/footer.php'; ?>
