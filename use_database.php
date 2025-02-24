<?php
require 'includes/password.php';

class Database {
    private $pdo;

    public function __construct($db_name, $db_username, $db_password) {
        $this->pdo = new PDO('mysql:host=localhost;dbname=' . $db_name, $db_username, $db_password);
    }

    public function userExists($chatId) {
        $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE user_id = :user_id;");
        $stmt->execute(['user_id' => $chatId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function addUser($chatId) {
        $stmt = $this->pdo->prepare("INSERT INTO users (user_id) VALUES (:user_id);");
        $stmt->execute(['user_id' => $chatId]);
    }

    public function setCurrency($currency) {
        $stmt = $this->pdo->prepare("UPDATE currency SET currency = :currency;");
        $stmt->execute(['currency' => $currency]);
    }

    public function getCurrency() {
        $stmt = $this->pdo->prepare("SELECT currency FROM currency;");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['currency'];
    }

    public function getUsers() {
        $stmt = $this->pdo->prepare("SELECT COUNT(user_id) AS user_count FROM users;");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];
    }
}
?>
