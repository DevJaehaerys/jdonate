<?php
$config = include('../main/config.php');

$dbHost = $config['dbHost'];
$dbUser = $config['dbUsername'];
$dbPass = $config['dbPassword'];
$dbName = $config['dbName'];

$allowedIP = $config['allowedIP'];

if ($_SERVER['REMOTE_ADDR'] !== $allowedIP) {
    http_response_code(403);
    die("Access Denied");
}

if (!isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] !== $config['apiKey']) {
    http_response_code(401);
    die("Access Denied");
}

try {
    $database = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
} catch (PDOException $e) {
    echo "DB ERR";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['steamid'])) {
    $steamid = $_GET['steamid'];
    $query = $database->prepare("SELECT * FROM cart WHERE steamid = :steamid");
    $query->bindParam(':steamid', $steamid);
    $query->execute();
    $items = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $database->prepare("DELETE FROM cart WHERE id = :id");
    $query->bindParam(':id', $id);
    $query->execute();
    echo "Item $id deleted.";
}
