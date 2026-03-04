<?php
/**
 * IoTdelivers - Services Page
 * CCTV Installation and other services
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

$page_title = 'Services - CCTV Installation & Setup';

// Get services
$services = getAllServices();

// Handle service booking form
$booking_success = false;
$booking_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_service') {
    $service_id = (int)($_POST['service_id'] ?? 0);
    $customer_name = sanitizeInput($_POST['customer_name'] ?? '');
    $customer_email = sanitizeInput($_POST['customer_email'] ?? '');
    $customer_phone = sanitizeInput($_POST['customer_phone'] ?? '');
    $service_location = sanitizeInput($_POST['service_location'] ?? '');
    $county = sanitizeInput($_POST['county'] ?? '');
    $preferred_date = sanitizeInput($_POST['preferred_date'] ?? null);
    $notes = sanitizeInput($_POST['notes'] ?? null);
    
    // Validate
    if (!$service_id || !$customer_name || !$customer_email || !$customer_phone || !$service_location || !$county) {
        $booking_error = 'All fields are required.';
    } else {
        // Create booking
        $result = createServiceBooking($service_id, $customer_name, $customer_email, $customer_phone, $service_location, $county, $preferred_date, $notes);
        
        if ($result['success']) {
            $booking_success = true;
        } else {
            $booking_error = 'Error creating booking: ' . $result['error'];
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);" class="py-5 text-white">
    <div class="container">
        <h1 class="mb-0">
            <i class="fas fa-tools"></i> Our Services
        </h1>
        <p class="lead mb-0 mt-2">Professional Installation & Setup Services</p>
    </div>
</section>

<div class="container my-5">
    <!-- Services Grid -->
    <div class="row g-4 mb-5">
        <?php foreach ($services as $service): ?>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 250px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-cog fa-5x text-muted"></i>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($service['name']); ?></h5>
                    <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($service['description'], 0, 100) . '...'); ?></p>
                    <h4 class="mb-3" style="color: <?php echo PRIMARY_COLOR; ?>;">
                        <?php echo formatCurrency($service['price']); ?>
                    </h4>
                    <button class="btn w-100" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;" data-bs-toggle="modal" data-bs-target="#bookingModal<?php echo $service['id']; ?>">
                        <i class="fas fa-calendar"></i> Book Service
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Booking Modals -->
<?php foreach ($services as $service): ?>
<div class="modal fade" id="bookingModal<?php echo $service['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                <h5 class="modal-title">Book: <?php echo htmlspecialchars($service['name']); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if ($booking_success && isset($_POST['service_id']) && (int)$_POST['service_id'] === $service['id']): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Service booked successfully! We'll contact you soon.
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($booking_error && isset($_POST['service_id']) && (int)$_POST['service_id'] === $service['id']): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($booking_error); ?>
                    </div>
                    <?php endif; ?>

                    <input type="hidden" name="action" value="book_service">
                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name *</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email *</label>
                        <input type="email" class="form-control" name="customer_email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number *</label>
                        <input type="tel" class="form-control" name="customer_phone" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">County *</label>
                        <select class="form-select" name="county" required>
                            <option value="">Select County</option>
                            <option value="Nairobi">Nairobi</option>
                            <option value="Mombasa">Mombasa</option>
                            <option value="Kisumu">Kisumu</option>
                            <option value="Nakuru">Nakuru</option>
                            <option value="Other">Other County</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Service Location *</label>
                        <textarea class="form-control" name="service_location" rows="2" required placeholder="Full address where service is needed"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Preferred Date</label>
                        <input type="date" class="form-control" name="preferred_date">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Additional Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any special requirements or questions"></textarea>
                    </div>

                    <div class="alert alert-info mb-0">
                        <strong>Service Price:</strong> <?php echo formatCurrency($service['price']); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                        <i class="fas fa-check"></i> Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>
