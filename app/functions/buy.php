<?php
ob_start();
session_start();

require_once '../main/User.php';

$user = new User();
$itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
if (!ctype_digit($itemId)) {
    return;
}

if ($user->checkAuthorization()) {
    $config = include('../main/config.php');

    $itemId = $_POST['itemId'];
    $steamid = $_SESSION['userData']['steam_id'];
    $balance = $user->getUserBalance($steamid);

    $itemPrice = $user->getItemPrice($itemId);

    if ($balance >= $itemPrice) {
        $newBalance = $balance - $itemPrice;
        $user->updateBalance($steamid, $newBalance);

        $commandInfo = $user->getCommandInfo($itemId);
        $command = $commandInfo['command'];
        $itemName = $commandInfo['name'];
        $itemImage = $commandInfo['image'];
        $commands = explode(",", $command);
        $commandsString = implode(", ", $commands);

        $user->addToCart($steamid, $itemName, $commandsString, $itemImage);

        $response = array("success" => 'Успех блин!');
        echo json_encode($response);
    } else {
        $response = array("error" => 'Нехвата баланса.');
        echo json_encode($response);
    }
} else {
    $response = array("auth" => 'Вы не авторизованы');
    echo json_encode($response);
}
?>
