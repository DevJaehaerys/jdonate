<?php
session_start();
$order_amount = $_POST['amount'];
$user = htmlspecialchars($_SESSION['userData']['steam_id'] ?? '', ENT_QUOTES, 'UTF-8');
if (is_numeric($order_amount) && floatval($order_amount) >= 10) {
    $merchant_id = ''; // ID мерчанта
    $secret_word = ''; // секретный ключ

    $order_id = $user;
    $currency = 'RUB';

    $sign = md5($merchant_id . ':' . $order_amount . ':' . $secret_word . ':' . $currency . ':' . $order_id);
    $url = 'https://pay.freekassa.ru/?m=' . $merchant_id . '&oa=' . $order_amount . '&o=' . $order_id . '&s=' . $sign . '&currency=RUB';

    // Return the URL as JSON response
    echo json_encode(['redirectUrl' => $url]);
    exit;
} else {
    echo "n<10";
}
