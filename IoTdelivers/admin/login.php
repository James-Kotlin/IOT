<?php
/**
 * IoTdelivers Admin - Login Page
 */

require_once '../config.php';
require_once '../includes/db_functions.php';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!$username || !$password) {
        $login_error = 'Username and password are required.';
    } else {
        $result = loginAdmin($username, $password);
        
        if ($result['success']) {
            header('Location: dashboard.php');
            exit();
        } else {
            $login_error = htmlspecialchars($result['error']);
        }
    }
}

$page_title = 'Admin Login - IoTdelivers';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6A0DAD 0%, #5a0a9d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #6A0DAD 0%, #5a0a9d 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-control:focus {
            border-color: #6A0DAD;
            box-shadow: 0 0 0 0.2rem rgba(106, 13, 173, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #6A0DAD 0%, #5a0a9d 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #5a0a9d 0%, #4a0882 100%);
            color: white;
        }
        
        .login-footer {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-router"></i> IoTdelivers</h1>
            <p class="mb-0">Admin Panel</p>
        </div>
        
        <div class="login-body">
            <?php if ($login_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $login_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control form-control-lg" name="username" placeholder="Enter username" required autofocus>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Enter password" required>
                </div>
                
                <button type="submit" class="btn btn-login btn-lg w-100 mb-3">
                    <i class="fas fa-sign-in-alt"></i> Login to Admin Panel
                </button>
            </form>
            
            <small class="text-muted d-block text-center">
                <strong>Demo Credentials:</strong><br>
                Username: <code>james</code><br>
                Password: <code>admin123</code>
            </small>
        </div>
        
        <div class="login-footer">
            <small class="text-muted">
                <a href="../index.php" class="text-decoration-none">← Back to Store</a>
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
