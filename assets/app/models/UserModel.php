<?php
class UserModel {
    private $conn;
    private $table = 'usuarios'; 

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
    public function createUser($nombre, $email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO {$this->table} (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("sss", $nombre, $email, $hash);
        $res = $stmt->execute();
        $stmt->close();

        return $res;
    }

   
    public function getUserByEmail($email) {
        $sql = "SELECT id, username AS nombre, email, password_hash AS password
                FROM {$this->table}
                WHERE email = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
        return $user ?: null;
    }
}
?>

