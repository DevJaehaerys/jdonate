<?php
$client_ip = $_SERVER['REMOTE_ADDR'];
$allowed_ips = ['168.119.157.136', '168.119.60.227', '138.201.88.124', '178.154.197.79'];

if (!in_array($client_ip, $allowed_ips)) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized @_@']);
    exit;
}

require_once './app/main/config.php';

$dbHost = $dbHost;
$dbUser = $dbUsername;
$dbPass = $dbPassword;
$dbName = $dbName;

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("db error");
}

$amount = $_POST['AMOUNT'];
$steamid = $_POST['MERCHANT_ORDER_ID'];
$newBalance = 0;

$sqlSelect = "SELECT balance FROM users WHERE steamid = '$steamid'";
$result = $conn->query($sqlSelect);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentBalance = $row["balance"];
    $newBalance = $currentBalance + $amount;

    $sqlUpdate = "UPDATE users SET balance = '$newBalance' WHERE steamid = '$steamid'";
    $conn->query($sqlUpdate);
} else {
    echo json_encode(['message' => 'user 404']);
}

$conn->close();
?>
