<?php
/**
 * M-Pesa Handler - STK Push Implementation
 * Handles M-Pesa Daraja API integration
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db_functions.php';

header('Content-Type: application/json');

// Get action
$action = isset($_POST['action']) ? sanitizeInput($_POST['action']) : null;

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit();
}

// ==================== STK PUSH FUNCTION ====================

if ($action === 'initiate_stk') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : null;
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : null;
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : null;
    
    if (!$order_id || !$phone || !$amount) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit();
    }
    
    // Format phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 9) {
        $phone = '254' . $phone;
    } elseif (strlen($phone) === 10 && $phone[0] === '0') {
        $phone = '254' . substr($phone, 1);
    } elseif (strlen($phone) !== 12) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
        exit();
    }
    
    // Get access token
    $access_token = getMpesaAccessToken();
    
    if (!$access_token) {
        echo json_encode(['success' => false, 'message' => 'Failed to get M-Pesa access token']);
        exit();
    }
    
    // Prepare STK Push request
    $timestamp = date('YmdHis');
    $business_shortcode = MPESA_BUSINESS_SHORTCODE;
    $passkey = MPESA_PASSKEY;
    
    $password = base64_encode($business_shortcode . $passkey . $timestamp);
    
    $curl_post_data = [
        'BusinessShortCode' => $business_shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => (int)$amount,
        'PartyA' => $phone,
        'PartyB' => $business_shortcode,
        'PhoneNumber' => $phone,
        'CallBackURL' => MPESA_CALLBACK_URL,
        'AccountReference' => 'ORD-' . $order_id,
        'TransactionDesc' => 'IoTdelivers Order #' . $order_id
    ];
    
    // Make request to M-Pesa
    $response = makeMpesaRequest(
        MPESA_BASE_URL . '/mpesa/stkpush/v1/processrequest',
        $curl_post_data,
        $access_token
    );
    
    if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
        // Save M-Pesa response details
        $checkoutRequestId = $response['CheckoutRequestID'];
        $requestId = $response['RequestId'];
        
        // Store in session for callback matching
        $_SESSION['mpesa_checkout_request_id_' . $order_id] = $checkoutRequestId;
        
        echo json_encode([
            'success' => true,
            'message' => 'STK Push sent successfully',
            'checkout_request_id' => $checkoutRequestId
        ]);
    } else {
        $error_message = isset($response['errorMessage']) ? $response['errorMessage'] : 'Unknown error';
        echo json_encode([
            'success' => false,
            'message' => 'M-Pesa Error: ' . $error_message
        ]);
    }
    exit();
}

// ==================== QUERY STK STATUS ====================

if ($action === 'query_stk') {
    $checkout_request_id = isset($_POST['checkout_request_id']) ? sanitizeInput($_POST['checkout_request_id']) : null;
    
    if (!$checkout_request_id) {
        echo json_encode(['success' => false, 'message' => 'Missing checkout request ID']);
        exit();
    }
    
    $access_token = getMpesaAccessToken();
    
    if (!$access_token) {
        echo json_encode(['success' => false, 'message' => 'Failed to get access token']);
        exit();
    }
    
    $timestamp = date('YmdHis');
    $business_shortcode = MPESA_BUSINESS_SHORTCODE;
    $passkey = MPESA_PASSKEY;
    $password = base64_encode($business_shortcode . $passkey . $timestamp);
    
    $curl_post_data = [
        'BusinessShortCode' => $business_shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'CheckoutRequestID' => $checkout_request_id
    ];
    
    $response = makeMpesaRequest(
        MPESA_BASE_URL . '/mpesa/stkpushquery/v1/query',
        $curl_post_data,
        $access_token
    );
    
    echo json_encode($response);
    exit();
}

// ==================== HELPER FUNCTIONS ====================

/**
 * Get M-Pesa Access Token from Daraja API
 */
function getMpesaAccessToken() {
    $consumer_key = MPESA_CONSUMER_KEY;
    $consumer_secret = MPESA_CONSUMER_SECRET;
    
    $url = MPESA_BASE_URL . '/oauth/v1/generate?grant_type=client_credentials';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $consumer_key . ':' . $consumer_secret);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    return isset($data['access_token']) ? $data['access_token'] : null;
}

/**
 * Make M-Pesa API Request
 */
function makeMpesaRequest($url, $data, $access_token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
