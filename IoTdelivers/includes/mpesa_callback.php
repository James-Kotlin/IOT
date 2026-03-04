<?php
/**
 * M-Pesa Callback Handler
 * Receives payment confirmations from M-Pesa Daraja API
 * 
 * Place this file on your production server and update the
 * MPESA_CALLBACK_URL in config.php
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db_functions.php';

// Get the callback data
$input = file_get_contents('php://input');
logError("M-Pesa Callback Received: " . $input);

$data = json_decode($input, true);

if (!isset($data['Body']['stkCallback'])) {
    http_response_code(400);
    exit('Invalid callback data');
}

$callback = $data['Body']['stkCallback'];

$result_code = $callback['ResultCode'] ?? null;
$result_description = $callback['ResultDesc'] ?? '';
$checkout_request_id = $callback['CheckoutRequestID'] ?? '';
$merchant_request_id = $callback['MerchantRequestID'] ?? '';

// Parse order ID from account reference
$callback_metadata = $callback['CallbackMetadata']['Item'] ?? [];
$mpesa_receipt = '';
$mpesa_phone = '';

foreach ($callback_metadata as $item) {
    if ($item['Name'] === 'MpesaReceiptNumber') {
        $mpesa_receipt = $item['Value'];
    }
    if ($item['Name'] === 'PhoneNumber') {
        $mpesa_phone = $item['Value'];
    }
}

// If payment successful (result code 0)
if ($result_code === 0) {
    // Extract order ID from merchant request or session
    // For now, we'll need to query by transaction details
    
    logError("Payment SUCCESS - Receipt: $mpesa_receipt, Phone: $mpesa_phone");
    
    // Update order with M-Pesa transaction details
    global $conn;
    
    $sql = "UPDATE orders SET order_status = 'Paid', mpesa_receipt_number = ? WHERE mpesa_transaction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $mpesa_receipt, $checkout_request_id);
    $stmt->execute();
    
} else {
    logError("Payment FAILED - Result Code: $result_code, Description: $result_description");
    
    // Update order status to failed
    global $conn;
    
    $sql = "UPDATE orders SET order_status = 'Failed' WHERE mpesa_transaction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $checkout_request_id);
    $stmt->execute();
}

// Send acknowledgement response to M-Pesa
http_response_code(200);
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Received'
]);
