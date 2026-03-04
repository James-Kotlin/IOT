<?php
/**
 * Database Functions and Queries
 * Handles all database operations
 */

if (!isset($conn)) {
    require_once __DIR__ . '/../config.php';
}

// ==================== PRODUCT FUNCTIONS ====================

/**
 * Get all categories
 */
function getAllCategories() {
    global $conn;
    $sql = "SELECT * FROM categories WHERE is_active = TRUE ORDER BY name ASC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get category by ID
 */
function getCategoryById($id) {
    global $conn;
    $sql = "SELECT * FROM categories WHERE id = ? AND is_active = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

/**
 * Get all products with filters
 */
function getProducts($category_id = null, $limit = null, $offset = 0, $featured_only = false) {
    global $conn;
    
    $sql = "SELECT p.* FROM products p WHERE p.is_active = TRUE";
    
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
    }
    
    if ($featured_only) {
        $sql .= " AND p.is_featured = TRUE";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($category_id && $limit) {
        $stmt->bind_param("iii", $category_id, $limit, $offset);
    } elseif ($category_id) {
        $stmt->bind_param("i", $category_id);
    } elseif ($limit) {
        $stmt->bind_param("ii", $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get product by ID
 */
function getProductById($id) {
    global $conn;
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ? AND p.is_active = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

/**
 * Get product count for pagination
 */
function getProductCount($category_id = null) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM products WHERE is_active = TRUE";
    
    if ($category_id) {
        $sql .= " AND category_id = ?";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($category_id) {
        $stmt->bind_param("i", $category_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// ==================== SERVICE FUNCTIONS ====================

/**
 * Get all services
 */
function getAllServices() {
    global $conn;
    $sql = "SELECT * FROM services WHERE is_active = TRUE ORDER BY name ASC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get service by ID
 */
function getServiceById($id) {
    global $conn;
    $sql = "SELECT * FROM services WHERE id = ? AND is_active = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

// ==================== ORDER FUNCTIONS ====================

/**
 * Create new order
 */
function createOrder($customer_name, $customer_email, $customer_phone, $delivery_address, $county, $total_amount, $items, $payment_method = 'mpesa') {
    global $conn;
    
    $order_number = generateOrderNumber();
    
    $sql = "INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, delivery_address, county, total_amount, payment_method, order_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssen", $order_number, $customer_name, $customer_email, $customer_phone, $delivery_address, $county, $total_amount, $payment_method);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Insert order items
        foreach ($items as $item) {
            insertOrderItem($order_id, $item['product_id'], $item['product_name'], $item['quantity'], $item['price']);
        }
        
        return [
            'success' => true,
            'order_id' => $order_id,
            'order_number' => $order_number
        ];
    } else {
        return [
            'success' => false,
            'error' => $conn->error
        ];
    }
}

/**
 * Insert order item
 */
function insertOrderItem($order_id, $product_id, $product_name, $quantity, $unit_price) {
    global $conn;
    
    $total_price = $quantity * $unit_price;
    
    $sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, total_price) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisiddi", $order_id, $product_id, $product_name, $quantity, $unit_price, $total_price);
    
    return $stmt->execute();
}

/**
 * Get order by ID
 */
function getOrderById($order_id) {
    global $conn;
    
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

/**
 * Get order by order number
 */
function getOrderByNumber($order_number) {
    global $conn;
    
    $sql = "SELECT * FROM orders WHERE order_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_number);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

/**
 * Get order items by order ID
 */
function getOrderItems($order_id) {
    global $conn;
    
    $sql = "SELECT * FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// ==================== USER FUNCTIONS ====================

/**
 * Register new user
 */
function registerUser($full_name, $email, $phone, $password) {
    global $conn;
    
    // Check if email already exists
    $checkSql = "SELECT id FROM users WHERE email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        return [
            'success' => false,
            'error' => 'Email already registered'
        ];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    $sql = "INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $full_name, $email, $phone, $hashedPassword);
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'user_id' => $conn->insert_id
        ];
    } else {
        return [
            'success' => false,
            'error' => $conn->error
        ];
    }
}

/**
 * Login user
 */
function loginUser($email, $password) {
    global $conn;
    
    $sql = "SELECT id, full_name, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            
            return [
                'success' => true,
                'user' => $user
            ];
        }
    }
    
    return [
        'success' => false,
        'error' => 'Invalid email or password'
    ];
}

/**
 * Get user by ID
 */
function getUserById($user_id) {
    global $conn;
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

// ==================== ADMIN FUNCTIONS ====================

/**
 * Login admin
 */
function loginAdmin($username, $password) {
    global $conn;
    
    $sql = "SELECT id, username, full_name, email, password FROM admins WHERE username = ? AND is_active = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_username'] = $admin['username'];
            
            return [
                'success' => true,
                'admin' => $admin
            ];
        }
    }
    
    return [
        'success' => false,
        'error' => 'Invalid username or password'
    ];
}

// ==================== SERVICE BOOKING FUNCTIONS ====================

/**
 * Create service booking
 */
function createServiceBooking($service_id, $customer_name, $customer_email, $customer_phone, $service_location, $county, $preferred_date = null, $notes = null) {
    global $conn;
    
    $booking_number = generateBookingNumber();
    
    $sql = "INSERT INTO service_bookings (booking_number, service_id, customer_name, customer_email, customer_phone, service_location, county, preferred_date, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssss", $booking_number, $service_id, $customer_name, $customer_email, $customer_phone, $service_location, $county, $preferred_date, $notes);
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'booking_id' => $conn->insert_id,
            'booking_number' => $booking_number
        ];
    } else {
        return [
            'success' => false,
            'error' => $conn->error
        ];
    }
}
