<?php
error_reporting(E_ALL & ~E_NOTICE);

ob_start();
session_start();

require_once './app/main/config.php';
require_once './app/main/User.php';

$username = htmlspecialchars($_SESSION['userData']['name'] ?? '', ENT_QUOTES, 'UTF-8');
$avatar = htmlspecialchars($_SESSION['userData']['avatar'] ?? '', ENT_QUOTES, 'UTF-8');
$steamid = htmlspecialchars($_SESSION['userData']['steam_id'] ?? '', ENT_QUOTES, 'UTF-8');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <title>JDonate</title>
</head>
<body class="is-boxed has-animations">
<div class="min-h-screen" x-data="{ open: false, modelOpen: false }">
    <?php
    $navFile = __DIR__ . '/templates/navbar.php';
    if (file_exists($navFile)) {
        include($navFile);
    } else {
        die('navfile 404');
    }
    ?>
    <main class="max-w-screen-2xl mx-auto mx-auto p-4">
        <h2 class="text-4xl font-semibold sm:pr-8 xl:pr-12 text-left mb-3">
            JDonate <br class="hidden sm:block">
        </h2>
        <p class="paragraph text-left mb-10 w-96">
            A free automated donation store website for any game with Steam authentication, allowing users to purchase
            items and an API for interaction with the cart. Pure PHP.
        </p>Под ней добавить
<script type="text/javascript" src="/></script>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-4">
            <?php
            require_once 'app/main/ShopCard.php';
            $shopCard = new ShopCard();
            echo $shopCard->displayProducts();
            ?>
        </div>
    </main>
    <?php
    $footerFile = __DIR__ . '/templates/footer.php';
    $modalFile = __DIR__ . '/templates/depositmodal.php';
    if (file_exists($footerFile)) {
        if (file_exists($modalFile)) {
            include($modalFile);
        } else {
            die('Modal file 404');
        }
        include($footerFile);
    } else {
        die('Footer file 404');
    }
    ?>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="/assets/js/second.js"></script>
</body>
</html>
