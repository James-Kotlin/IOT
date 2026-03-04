<?php
/**
 * Check Payment Status
 * Returns payment status for an order
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db_functions.php';

header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (!$order_id) {
    echo json_encode(['success' => false, 'paid' => false]);
    exit();
}

$order = getOrderById($order_id);

if (!$order) {
    echo json_encode(['success' => false, 'paid' => false]);
    exit();
}

$is_paid = ($order['order_status'] === 'Paid');

echo json_encode([
    'success' => true,
    'paid' => $is_paid,
    'order_status' => $order['order_status'],
    'order_number' => $order['order_number']
]);
