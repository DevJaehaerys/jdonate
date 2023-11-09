<?php
// https://github.com/DevJaehaerys/jdonate
require_once 'db.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
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
        $query = "UPDATE users SET balance = ? WHERE steamid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $newBalance, $steamid);
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
