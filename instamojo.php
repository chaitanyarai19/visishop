<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

session_start();
$user = $_SESSION['username'] ?? null;

if (!$user) {
    die('User is not logged in');
}

$db = new Database();
$db->select('options', 'site_name', null, null, null, null);
$site_name = $db->getResult();

if (!$site_name) {
    die('Failed to retrieve site name');
}

// Generate a unique transaction ID
$txn_id = uniqid('txn_');

// Gather the necessary information
$product_id = $_POST['product_id'] ?? null;
$product_qty = $_POST['product_qty'] ?? null;
$product_total = $_POST['product_total'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$product_id || !$product_qty || !$product_total || !$user_id) {
    die('Missing required POST data');
}

$params1 = [
    'item_number' => $product_id,
    'txn_id' => $txn_id,
    'payment_gross' => $product_total,
    'payment_status' => 'credit',
];

$params2 = [
    'product_id' => $product_id,
    'product_qty' => $product_qty,
    'total_amount' => $product_total,
    'product_user' => $user_id,
    'order_date' => date('Y-m-d'),
    'pay_req_id' => $txn_id,
];

// Insert payment details into 'payments' table
$db->insert('payments', $params1);

// Insert order details into 'order_products' table
$db->insert('order_products', $params2);

$result = $db->getResult();

if ($result) {
    // Redirect to success page with payment request ID
    header('Location: '.$hostname.'success.php?payment_request_id='.$txn_id.'&payment_status=Credit');
} else {
    // Handle error (e.g., log the error, show error message to the user)
    echo "Error in placing the order. Please try again.";
}
?>
