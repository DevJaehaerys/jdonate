<?php
global $steamApiKey, $dbHost;
ob_start();
session_start();
require_once '../main/config.php';


function p($arr){
    return '<pre>'.print_r($arr,true).'</pre>';
}


$params = [
    'openid.assoc_handle' => $_GET['openid_assoc_handle'],
    'openid.signed'       => $_GET['openid_signed'],
    'openid.sig'          => $_GET['openid_sig'],
    'openid.ns'           => 'http://specs.openid.net/auth/2.0',
    'openid.mode'         => 'check_authentication',
];

$signed = explode(',', $_GET['openid_signed']);

foreach ($signed as $item) {
    $val = $_GET['openid_'.str_replace('.', '_', $item)];
    $params['openid.'.$item] = stripslashes($val);
}

$data = http_build_query($params);
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Accept-language: en\r\n".
            "Content-type: application/x-www-form-urlencoded\r\n".
            'Content-Length: '.strlen($data)."\r\n",
        'content' => $data,
    ],
]);

$result = file_get_contents('https://steamcommunity.com/openid/login', false, $context);

if(preg_match("#is_valid\s*:\s*true#i", $result)){
    preg_match('#^https://steamcommunity.com/openid/id/([0-9]{17,25})#', $_GET['openid_claimed_id'], $matches);
    $steamID64 = is_numeric($matches[1]) ? $matches[1] : 0;
    echo 'request has been validated by open id, returning the client id (steam id) of: ' . $steamID64;

}else{
    echo 'error: unable to validate your request';
    exit();
}

$steam_api_key = $steamApiKey;

$response = file_get_contents('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$steam_api_key.'&steamids='.$steamID64);
$response = json_decode($response,true);


$userData = $response['response']['players'][0];

$_SESSION['logged_in'] = true;
$_SESSION['userData'] = [
    'steam_id'=>$userData['steamid'],
    'name'=>$userData['personaname'],
    'avatar'=>$userData['avatarmedium'],
];

$servername = $dbHost;
$username = $dbUsername;
$password = $dbPassword;
$dbname = $dbName;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('db error');
}

function saveOrUpdateUser($steamid, $avatar, $username) {
    global $conn;
    $selectQuery = $conn->prepare("SELECT * FROM users WHERE steamid = ?");
    $selectQuery->bind_param("s", $steamid);
    $selectQuery->execute();
    $result = $selectQuery->get_result();

    if ($result->num_rows > 0) {
        $updateQuery = $conn->prepare("UPDATE users SET avatar = ? WHERE steamid = ?");
        $updateQuery->bind_param("ss", $avatar, $steamid);
        $updateQuery->execute();
        $updateQuery->close();

    } else {
        $insertQuery = $conn->prepare("INSERT INTO users (steamid, avatar, username) VALUES (?, ?, ?)");
        $insertQuery->bind_param("sss", $steamid, $avatar, $username);
        $insertQuery->execute();
        $insertQuery->close();

    }

    $selectQuery->close();
}

saveOrUpdateUser($userData['steamid'], $userData['avatarmedium'], $userData['personaname'] );

$redirect_url = "/";
header("Location: $redirect_url");
exit();