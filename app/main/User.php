<?php
// https://github.com/DevJaehaerys/jdonate
require_once 'db.php';
require_once __DIR__ . '/../main/config.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function processCartRequest()
    {
        global $whiteListApi, $apiKey;

        $this->checkApiAccess($whiteListApi, $apiKey);

        $database = $this->db->getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['steamid'])) {
            $steamid = $_GET['steamid'];

            if ($this->userExists($steamid)) {
                $items = $this->getCartItems($database, $steamid);
                echo json_encode(!empty($items) ? $items : ['message' => 'Cart empty']);
            } else {
                echo json_encode(['message' => 'User 404']);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->deleteCartItem($database, $id);
            echo "Item $id deleted.";
        }
    }

    private function checkApiAccess($whiteListApi, $apiKey)
    {
        if ($_SERVER['REMOTE_ADDR'] !== $whiteListApi || !isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] !== $apiKey) {
            http_response_code(403);
            die("Access Denied");
        }
    }

    private function userExists($steamid)
    {
        $query = "SELECT * FROM users WHERE steamid = ?";
        return $this->executeQuery($query, [$steamid])->rowCount() > 0;
    }

    private function getCartItems($database, $steamid)
    {
        $query = $database->prepare("SELECT * FROM cart WHERE steamid = ?");
        $query->bindParam(':steamid', $steamid);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteCartItem($database, $id)
    {
        $query = $database->prepare("DELETE FROM cart WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
    }

    public function checkAuthorization()
    {
        return isset($_SESSION['userData']['name']);
    }

    public function getUserBalance($steamid)
    {
        $query = "SELECT balance FROM users WHERE steamid = ?";
        return $this->executeQuery($query, [$steamid])->fetchColumn();
    }

    public function updateBalance($steamid, $newBalance)
    {
        $query = "UPDATE users SET balance = ? WHERE steamid = ?";
        return $this->executeQuery($query, [$newBalance, $steamid]);
    }

    public function addToCart($steamid, $itemName, $commandsString, $itemImage)
    {
        $query = "INSERT INTO cart (steamid, name, command, image) VALUES (?, ?, ?, ?)";
        return $this->executeQuery($query, [$steamid, $itemName, $commandsString, $itemImage]);
    }


    public function activatePromocode($promocode, $userid)
    {
        $steamid = $userid;

        $userQuery = "SELECT id FROM users WHERE steamid = ?";
        $userId = $this->executeQuery($userQuery, [$steamid])->fetchColumn();

        if (!$userId) {
            echo json_encode(['message' => '404 user']);
            return;
        }

        if (!$this->checkAuthorization()) {
            echo json_encode(['message' => 'auth please']);
            return;
        }

        $promocodeInfo = $this->getPromocodeInfo($promocode);

        if (!$promocodeInfo) {
            echo json_encode(['message' => 'promo 404']);
            return;
        }

        if ($promocodeInfo['activations_count'] >= $promocodeInfo['max_activations']) {
            echo json_encode(['message' => 'promo max']);
            return;
        }

        $activationCountQuery = "SELECT COUNT(*) AS activation_count FROM user_activations WHERE user_id = ? AND promocode_id = ?";
        $activationCount = $this->executeQuery($activationCountQuery, [$userId, $promocodeInfo['id']])->fetchColumn();

        if ($activationCount >= $promocodeInfo['max_activations_per_user']) {
            echo json_encode(['message' => 'promo max activations per user']);
            return;
        }

        $insertActivationQuery = "INSERT INTO user_activations (user_id, promocode_id) VALUES (?, ?)";
        $this->executeQuery($insertActivationQuery, [$userId, $promocodeInfo['id']]);

        $updatePromocodeQuery = "UPDATE promocodes SET activations_count = activations_count + 1 WHERE id = ?";
        $this->executeQuery($updatePromocodeQuery, [$promocodeInfo['id']]);

        $newBalance = $this->getUserBalance($steamid) + $promocodeInfo['balance'];

        $updateUserBalanceQuery = "UPDATE users SET balance = ? WHERE id = ?";
        $this->executeQuery($updateUserBalanceQuery, [$newBalance, $userId]);

        echo json_encode(['message' => 'success']);
    }

    private function getPromocodeInfo($promocode)
    {
        $query = "SELECT * FROM promocodes WHERE code = ?";
        return $this->executeQuery($query, [$promocode])->fetch(PDO::FETCH_ASSOC);
    }

    private function executeQuery($query, $params)
    {
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function closeConnection()
    {
        $this->db->getConnection()->close();
    }

// soon may be
//    public function updateUserData($userData) {
//        if (!isset($userData['steamid']) || !isset($userData['avatarmedium']) || !isset($userData['personaname'])) {
//            return false;
//        }
//
//        $steamid = $userData['steamid'];
//        $avatar = $userData['avatarmedium'];
//        $username = $userData['personaname'];
//
//        $conn = $this->db->getConnection();
//        $conn->begin_transaction();
//
//        try {
//            $query = "SELECT * FROM users WHERE steamid = '$steamid' FOR UPDATE";
//            $result = $conn->query($query);
//
//            if ($result->num_rows > 0) {
//                $query = "UPDATE users SET avatar = '$avatar' WHERE steamid = '$steamid'";
//                $conn->query($query);
//            } else {
//                $query = "INSERT INTO users (steamid, avatar, username) VALUES ('$steamid', '$avatar', '$username')";
//                $conn->query($query);
//            }
//
//            $conn->commit();
//            return true;
//        } catch (Exception $e) {
//            $conn->rollback();
//            return false;
//        }
//    }

}
