<?php
/**
 * IoTdelivers Admin - Dashboard
 */

require_once '../config.php';
require_once '../includes/db_functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Admin Dashboard - IoTdelivers';

// Get statistics
$stats = [];

// Total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['total_products'] = $result->fetch_assoc()['count'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['count'];

// Pending orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'Pending'");
$stats['pending_orders'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE order_status = 'Paid'");
$row = $result->fetch_assoc();
$stats['total_revenue'] = $row['total'] ?? 0;

// Recent orders
$recent_orders = [];
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $recent_orders[] = $row;
}

// Get categories
$categories = getAllCategories();
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
        .admin-sidebar {
            background: linear-gradient(135deg, #6A0DAD 0%, #5a0a9d 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .admin-sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            padding-left: 2rem;
        }
        
        .admin-topbar {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 2rem;
        }
        
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="row g-0 min-vh-100">
        <!-- Sidebar -->
        <div class="col-lg-3 admin-sidebar">
            <div class="text-center mb-4">
                <h3 class="text-white"><i class="fas fa-router"></i> IoTdelivers</h3>
                <small class="text-white-50">Admin Panel</small>
            </div>
            
            <nav>
                <a href="dashboard.php" class="active"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-box me-2"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-bag me-2"></i> Orders</a>
                <a href="customers.php"><i class="fas fa-users me-2"></i> Customers</a>
                <a href="services.php"><i class="fas fa-tools me-2"></i> Services</a>
                <a href="bookings.php"><i class="fas fa-calendar me-2"></i> Bookings</a>
                <a href="categories.php"><i class="fas fa-folder me-2"></i> Categories</a>
                
                <hr class="bg-white-50">
                
                <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Top Bar -->
            <div class="admin-topbar">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0">Dashboard</h4>
                    </div>
                    <div class="col text-end">
                        <span class="me-3">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                        </span>
                        <a href="logout.php" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="p-4">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <p class="text-muted mb-0">Total Orders</p>
                                        <h4 class="mb-0"><?php echo $stats['total_orders']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #ffd93d 0%, #ffb500 100%);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <p class="text-muted mb-0">Pending Orders</p>
                                        <h4 class="mb-0"><?php echo $stats['pending_orders']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);">
                                        <i class="fas fa-money-bill"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <p class="text-muted mb-0">Total Revenue</p>
                                        <h4 class="mb-0"><?php echo formatCurrency($stats['total_revenue']); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #6A0DAD 0%, #5a0a9d 100%);">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <p class="text-muted mb-0">Total Products</p>
                                        <h4 class="mb-0"><?php echo $stats['total_products']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col">
                        <h5 class="fw-bold mb-3">Quick Actions</h5>
                        <a href="products.php?action=add" class="btn btn-sm me-2" style="background-color: #6A0DAD; color: white;">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                        <a href="orders.php" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-list"></i> View Orders
                        </a>
                        <a href="customers.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-users"></i> View Customers
                        </a>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #6A0DAD; color: white;">
                        <h5 class="mb-0"><i class="fas fa-recent"></i> Recent Orders</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $order['order_status'] === 'Paid' ? 'bg-success' : ($order['order_status'] === 'Pending' ? 'bg-warning' : 'bg-secondary'); ?>">
                                                <?php echo htmlspecialchars($order['order_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Info -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Admin Info:</strong> 
                    You are logged in as <code><?php echo htmlspecialchars($_SESSION['admin_username']); ?></code>. 
                    For security, always log out when leaving your computer.
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
