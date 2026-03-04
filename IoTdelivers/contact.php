<?php
/**
 * IoTdelivers - Contact Page
 */

require_once 'config.php';

$page_title = 'Contact Us - IoTdelivers';

$contact_success = false;
$contact_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (!$name || !$email || !$subject || !$message) {
        $contact_error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contact_error = 'Invalid email address.';
    } else {
        // Send email (configure your mail settings)
        $to = BUSINESS_EMAIL;
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $email_body = "
        <html>
        <body>
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
        </body>
        </html>
        ";
        
        // Uncomment to enable email sending
        // mail($to, "New Contact: " . $subject, $email_body, $headers);
        
        $contact_success = true;
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);" class="py-5 text-white">
    <div class="container">
        <h1 class="mb-0">
            <i class="fas fa-envelope"></i> Contact Us
        </h1>
        <p class="lead mb-0 mt-2">We'd love to hear from you</p>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <!-- Contact Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-3x mb-3" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                    <h5 class="card-title">Location</h5>
                    <p class="card-text text-muted">Nairobi, Kenya</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-phone fa-3x mb-3" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                    <h5 class="card-title">Phone</h5>
                    <p class="card-text text-muted">
                        <a href="tel:+254700000000" class="text-decoration-none">+254 700 000 000</a>
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-3x mb-3" style="color: <?php echo PRIMARY_COLOR; ?>;"></i>
                    <h5 class="card-title">Email</h5>
                    <p class="card-text text-muted">
                        <a href="mailto:james@iotdelivers.com" class="text-decoration-none">james@iotdelivers.com</a>
                    </p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fab fa-whatsapp fa-3x mb-3" style="color: #25D366;"></i>
                    <h5 class="card-title">WhatsApp</h5>
                    <p class="card-text text-muted">
                        <a href="https://wa.me/254700000000" target="_blank" class="text-decoration-none">Chat with us</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-comment"></i> Send us a Message</h5>
                </div>
                <div class="card-body">
                    <?php if ($contact_success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Thank you for your message! We'll get back to you soon.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if ($contact_error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($contact_error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Your Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Your Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject *</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Message *</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-lg w-100" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Section (Optional) -->
<section class="py-5 bg-light mt-5">
    <div class="container">
        <h2 class="text-center mb-4 fw-bold">Find Us on the Map</h2>
        <div class="row">
            <div class="col-12">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8231486929263!2d36.81666981177436!3d-1.286388999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f1d1234567890%3A0xeaf6c6c6c6c6c6c!2sNairobi%2C%20Kenya!5e0!3m2!1sen!2sKE!4v1234567890" width="100%" height="400" style="border:0; border-radius: 10px;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
