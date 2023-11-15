
<?php

ob_start();
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "post only";
    exit;
}
$data = json_decode(file_get_contents("php://input"));
require_once __DIR__ . '/../main/User.php';
$user = new User();
$userid = htmlspecialchars($_SESSION['userData']['steam_id'] ?? '', ENT_QUOTES, 'UTF-8');
$promocode = filter_var($data->promocode, FILTER_SANITIZE_STRING);

$user->activatePromocode($promocode, $userid);