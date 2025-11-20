<?php
class UserModel {
    private $conn;
    private $table = 'usuarios'; 

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Crea un nuevo usuario
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function createUser($nombre, $email, $password) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO {$this->table} (username, email, password_hash) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                return [
                    'success' => false, 
                    'error' => 'prepare_failed'
                ];
            }

            $stmt->bind_param("sss", $nombre, $email, $hash);
            $result = $stmt->execute();
            
            // Capturar error de duplicación
            if (!$result) {
                $errno = $stmt->errno;
                $stmt->close();
                
                // Error 1062 = Duplicate entry
                if ($errno === 1062) {
                    return [
                        'success' => false,
                        'error' => 'duplicate'
                    ];
                }
                
                return [
                    'success' => false,
                    'error' => 'execution_failed'
                ];
            }

            $stmt->close();
            return ['success' => true, 'error' => null];
            
        } catch (Exception $e) {
            error_log("Error en createUser: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'exception'
            ];
        }
    }

    /**
     * Obtiene usuario por email
     * @return array|null
     */
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT id, username AS nombre, email, password_hash AS password, rol
                    FROM {$this->table}
                    WHERE email = ?
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare getUserByEmail");
                return null;
            }

            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                error_log("Error ejecutando getUserByEmail");
                $stmt->close();
                return null;
            }

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            return $user ?: null;
            
        } catch (Exception $e) {
            error_log("Excepción en getUserByEmail: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verifica si existe un username
     * @return bool
     */
    public function usernameExists($username) {
        try {
            $sql = "SELECT id FROM {$this->table} WHERE username = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) return false;
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();
            
            return $exists;
            
        } catch (Exception $e) {
            error_log("Error en usernameExists: " . $e->getMessage());
            return false;
        }
    }
}
?>