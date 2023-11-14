<?php
// https://github.com/DevJaehaerys/jdonate
require_once 'db.php';
require_once __DIR__ . '/../main/config.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }
    public function processCartRequest() {
        global $whiteListApi, $apiKey;

        if ($_SERVER['REMOTE_ADDR'] !== $whiteListApi) {
            http_response_code(403);
            die("Access Denied");
        }

        if (!isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] !== $apiKey) {
            http_response_code(401);
            die("Access Denied");
        }

        $database = $this->db->getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['steamid'])) {
            $steamid = $_GET['steamid'];

            $userCheckQuery = $database->prepare("SELECT * FROM users WHERE steamid = :steamid");
            $userCheckQuery->bindParam(':steamid', $steamid);
            $userCheckQuery->execute();
            $userExists = $userCheckQuery->rowCount() > 0;

            if ($userExists) {
                $query = $database->prepare("SELECT * FROM cart WHERE steamid = :steamid");
                $query->bindParam(':steamid', $steamid);
                $query->execute();
                $items = $query->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($items)) {
                    echo json_encode($items);
                } else {
                    echo json_encode(['message' => 'Cart empty ']);
                }
            } else {
                echo json_encode(['message' => 'User 404']);
            }
        }


        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = $database->prepare("DELETE FROM cart WHERE id = :id");
            $query->bindParam(':id', $id);
            $query->execute();
            echo "Item $id deleted.";
        }
    }

    public function checkAuthorization() {
        return isset($_SESSION['userData']['name']);
    }

    public function getUserBalance($steamid) {
        $conn = $this->db->getConnection();
        $query = "SELECT balance FROM users WHERE steamid = '$steamid'";
        $result = $conn->query($query);
        $row = $result->fetch();
        return $row['balance'];
    }
    public function updateBalance($steamid, $newBalance) {
        $query = "UPDATE users SET balance = :newBalance WHERE steamid = :steamid";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(':newBalance', $newBalance, PDO::PARAM_STR);
        $stmt->bindParam(':steamid', $steamid, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function addToCart($steamid, $itemName, $commandsString, $itemImage) {
        $query = "INSERT INTO cart (steamid, name, command, image) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $steamid, $itemName, $commandsString, $itemImage);
        return $stmt->execute();
    }

    public function closeConnection() {
        $this->conn->close();
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
