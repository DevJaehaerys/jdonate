<?php
$client_ip = $_SERVER['REMOTE_ADDR'];
$allowed_ips = ['168.119.157.136', '168.119.60.227', '138.201.88.124', '178.154.197.79'];

if (!in_array($client_ip, $allowed_ips)) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized @_@']);
    exit;
}

require_once __DIR__ . '/../../main/User.php';
$user = new User();
$amount = $_POST['AMOUNT'];
$steamid = $_POST['MERCHANT_ORDER_ID'];
$newBalance = 0;
$currentBalance = $user->getUserBalance($steamid);

if ($currentBalance !== null) {
    $newBalance = $currentBalance + $amount;
    $user->updateBalance($steamid, $newBalance);
} else {
    echo json_encode(['message' => 'user 404']);
}
?>
