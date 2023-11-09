<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['dbHost'];
    $dbUsername = $_POST['dbUsername'];
    $dbPassword = $_POST['dbPassword'];
    $dbName = $_POST['dbName'];
    $steamApiKey = $_POST['steamApiKey'];
    $whiteListApi = $_POST['whiteListApi'];
    $apiKey = $_POST['apiKey'];

    $configData = <<<EOD
<?php
\$dbHost = '$dbHost';
\$dbUsername = '$dbUsername';
\$dbPassword = '$dbPassword';
\$dbName = '$dbName';
\$steamApiKey = '$steamApiKey';
\$whiteListApi = '$whiteListApi';
\$apiKey = '$apiKey';
EOD;

    file_put_contents('./app/main/config.php', $configData);

    unlink(__FILE__);

    echo 'Успех блин';
    exit;
}
?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Установка JDonate</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }

            .container {
                width: 400px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            h1 {
                text-align: center;
            }

            label {
                display: block;
                margin-top: 10px;
            }

            input {
                width: 100%;
                padding: 10px;
                margin-top: 5px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            input[type="submit"] {
                background-color: #007BFF;
                color: #fff;
                border: none;
                cursor: pointer;
            }

            input[type="submit"]:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Установка JDonate</h1>
        <form method="post">
            <label for="dbHost">Хост базы данных:</label>
            <input type="text" name="dbHost" required>

            <label for="dbUsername">Имя пользователя базы данных:</label>
            <input type="text" name="dbUsername" required>

            <label for="dbPassword">Пароль базы данных:</label>
            <input type="password" name="dbPassword" required>

            <label for="dbName">Имя базы данных:</label>
            <input type="text" name="dbName" required>

            <label for="steamApiKey">Steam API Key:</label>
            <input type="text" name="steamApiKey" required>

            <label for="whiteListApi">White List API:</label>
            <input type="text" name="whiteListApi" required>

            <label for="apiKey">API Key:</label>
            <input type="text" name="apiKey" required>

            <input type="submit" value="Установить">
        </form>
    </div>
    </body>
    </html>
<?php
