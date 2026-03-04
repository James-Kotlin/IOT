<?php
/**
 * IoTdelivers - Payment Processing (M-Pesa Integration)
 * Handles M-Pesa STK Push and payment initialization
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

$page_title = 'Payment - IoTdelivers';

// Get order details
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (!$order_id) {
    header('Location: shop.php');
    exit();
}

$order = getOrderById($order_id);

if (!$order) {
    header('Location: shop.php');
    exit();
}

$order_items = getOrderItems($order_id);
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);" class="py-4 text-white">
    <div class="container">
        <h1 class="mb-0">
            <i class="fas fa-credit-card"></i> Payment Processing
        </h1>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Payment Instructions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Payment Instructions</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading">How to Complete Your Payment:</h6>
                        <ol class="mb-0">
                            <li><strong>Accept the M-Pesa STK Push:</strong> You will receive a prompt on your phone to enter your M-Pesa PIN</li>
                            <li><strong>Enter Your M-Pesa PIN:</strong> Complete the payment securely</li>
                            <li><strong>Confirmation:</strong> You will receive an SMS confirmation and order details</li>
                            <li><strong>Delivery:</strong> We will process your order immediately and arrange delivery</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- M-Pesa Payment Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fab fa-m"></i> M-Pesa Payment</h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm" onsubmit="initiateStkPush(event)">
                        <div class="mb-3">
                            <label class="form-label fw-bold">M-Pesa Phone Number *</label>
                            <div class="input-group">
                                <span class="input-group-text">+254</span>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="xxxxxxxxx" required value="<?php echo isset($order['customer_phone']) ? substr($order['customer_phone'], -9) : ''; ?>">
                            </div>
                            <small class="text-muted">Enter your M-Pesa registered phone number (without +254 or 0)</small>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirm" required>
                            <label class="form-check-label" for="confirm">
                                I confirm that <strong><?php echo htmlspecialchars($order['customer_phone']); ?></strong> is my M-Pesa registered number
                            </label>
                        </div>

                        <button type="submit" class="btn btn-lg w-100" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;" id="payBtn">
                            <i class="fas fa-mobile-alt"></i> Initiate M-Pesa Payment
                        </button>
                    </form>

                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-shield-alt"></i> <strong>Secure Payment:</strong> Your payment is secured by Safaricom M-Pesa's industry-leading encryption. We never store your M-Pesa PIN.
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="card shadow-sm" id="statusCard" style="display: none;">
                <div class="card-body text-center">
                    <div class="spinner-border mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Processing Payment...</h5>
                    <p class="text-muted">Check your phone for the M-Pesa prompt<br>Do not close this page</p>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order #<?php echo htmlspecialchars($order['order_number']); ?></h5>
                </div>
                <div class="card-body p-0">
                    <!-- Order Items -->
                    <?php foreach ($order_items as $item): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>
                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                            </span>
                            <span class="fw-bold"><?php echo formatCurrency($item['total_price']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="card-body border-top">
                    <p class="mb-2 text-muted">
                        <strong>Customer:</strong><br>
                        <?php echo htmlspecialchars($order['customer_name']); ?><br>
                        <?php echo htmlspecialchars($order['customer_email']); ?>
                    </p>
                    <p class="mb-2 text-muted">
                        <strong>Delivery Address:</strong><br>
                        <?php echo htmlspecialchars($order['delivery_address']); ?><br>
                        <?php echo htmlspecialchars($order['county']); ?>
                    </p>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><?php echo formatCurrency($order['total_amount']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span><?php echo formatCurrency(0); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total Due:</strong>
                        <span class="h5 mb-0" style="color: <?php echo PRIMARY_COLOR; ?>;">
                            <?php echo formatCurrency($order['total_amount']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- M-Pesa Integration Scripts -->
<script>
// M-Pesa STK Push Implementation
function initiateStkPush(event) {
    event.preventDefault();
    
    const phone = document.getElementById('phone').value;
    const orderId = <?php echo json_encode($order_id); ?>;
    const amount = <?php echo json_encode($order['total_amount']); ?>;
    
    // Show processing status
    document.getElementById('paymentForm').style.display = 'none';
    document.getElementById('statusCard').style.display = 'block';
    
    // Make AJAX call to initiate STK Push
    fetch('includes/mpesa_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=initiate_stk&order_id=' + orderId + '&phone=' + phone + '&amount=' + amount
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show confirmation message
            alert('Payment prompt sent to ' + phone + '\nPlease check your phone and enter your M-Pesa PIN');
            
            // Poll for payment status
            checkPaymentStatus(orderId);
        } else {
            alert('Error: ' + data.message);
            document.getElementById('paymentForm').style.display = 'block';
            document.getElementById('statusCard').style.display = 'none';
        }
    })
    .catch(error => {
        alert('Error initiating payment: ' + error);
        document.getElementById('paymentForm').style.display = 'block';
        document.getElementById('statusCard').style.display = 'none';
    });
}

function checkPaymentStatus(orderId) {
    let checkCount = 0;
    const maxChecks = 60; // Check for 5 minutes (5s x 60)
    
    const checkInterval = setInterval(() => {
        fetch('includes/check_payment.php?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            if (data.paid) {
                clearInterval(checkInterval);
                window.location.href = 'order_confirmation.php?order_id=' + orderId;
            }
            
            checkCount++;
            if (checkCount >= maxChecks) {
                clearInterval(checkInterval);
                alert('Payment confirmation timeout. Your order is pending. You can check status in your account.');
                window.location.href = 'index.php';
            }
        });
    }, 5000); // Check every 5 seconds
}

// Populate phone field with order customer phone
document.addEventListener('DOMContentLoaded', function() {
    const orderPhone = '<?php echo isset($order['customer_phone']) ? htmlspecialchars($order['customer_phone']) : ''; ?>';
    if (orderPhone) {
        document.getElementById('phone').value = orderPhone.replace(/^(\+254|0)/, '');
    }
});
</script>

<?php include 'includes/footer.php'; ?>
