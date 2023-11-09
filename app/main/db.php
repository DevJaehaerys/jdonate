<?php
require_once 'config.php';
class Database {
    private $conn;

    public function __construct() {
        global $dbHost, $dbUsername, $dbPassword, $dbName;
        $this->conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getProducts() {
        $query = "SELECT * FROM shops";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function closeConnection() {
        $this->conn = null;
    }
}

