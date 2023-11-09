<?php
ob_start();
session_start();

require_once './app/maim/User.php'; 

$user = new User();

if ($user->checkAuthorization()) {
    $config = include('../main/config.php');

    $itemId = mysqli_real_escape_string($user->getConnection(), $_POST['itemId']);
    $steamid = mysqli_real_escape_string($user->getConnection(), $_SESSION['userData']['steam_id']);
    $balance = $user->getUserBalance($steamid);

    $balanceQuery = "SELECT balance FROM users WHERE steamid = '$steamid'";
    $balanceResult = mysqli_query($user->getConnection(), $balanceQuery);
    if (!$balanceResult) {
        die("db error");
    }

    $balanceRow = mysqli_fetch_assoc($balanceResult);
    $balance = $balanceRow['balance'];

    $itemQuery = "SELECT price FROM shops WHERE id = '$itemId'";
    $itemResult = mysqli_query($user->getConnection(), $itemQuery);
    if (!$itemResult) {
        die("db error");
    }
    $itemRow = mysqli_fetch_assoc($itemResult);
    $itemPrice = $itemRow['price'];

    if ($balance >= $itemPrice) {
        $newBalance = $balance - $itemPrice;
        $user->updateBalance($steamid, $newBalance);

        $commandQuery = "SELECT command, name, image FROM shops WHERE id = ?";
        $stmt = $user->getConnection()->prepare($commandQuery);
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $commandResult = $stmt->get_result();
        $stmt->close();

        if (!$commandResult) {
            die("db error");
        }

        $commandRow = $commandResult->fetch_assoc();
        $command = $commandRow['command'];
        $itemName = $commandRow['name'];
        $itemImage = $commandRow['image'];
        $command = str_replace("%player%", $steamid, $command);
        $commands = explode(",", $command);
        $commandsString = implode(", ", $commands);

        $sql = "INSERT INTO cart (steamid, name, command, image) VALUES ('$steamid', '$itemName', '$commandsString', '$itemImage')";
        if (!$user->getConnection()) {
            die("db error");
        }

        if ($user->getConnection()->query($sql) === true) {
            $response = array("success" => 'success');
        } else {
            die("db error");
        }

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

$user->closeConnection(); 
?>
