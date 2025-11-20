<?php
class UserModel {
    private $conn;
    private $table = 'usuarios'; 

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Crea un nuevo usuario
     */
    public function createUser($nombre, $email, $password) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            $rol = 'cliente';

            $sql = "INSERT INTO {$this->table} (username, email, password_hash, rol) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare createUser: " . $this->conn->error);
                return [
                    'success' => false, 
                    'error' => 'prepare_failed'
                ];
            }

            $stmt->bind_param("ssss", $nombre, $email, $hash, $rol);
            $result = $stmt->execute();
            
            // Capturar error de duplicación
            if (!$result) {
                $errno = $stmt->errno;
                $error_msg = $stmt->error;
                error_log("Error en execute createUser: {$error_msg}");
                $stmt->close();
                
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
     */
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT 
                        id, 
                        username AS nombre, 
                        email, 
                        password_hash AS password, 
                        COALESCE(NULLIF(rol, ''), 'cliente') AS rol
                    FROM {$this->table}
                    WHERE email = ?
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare getUserByEmail: " . $this->conn->error);
                return null;
            }

            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                error_log("Error ejecutando getUserByEmail: " . $stmt->error);
                $stmt->close();
                return null;
            }

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            if ($user) {
                error_log("Usuario encontrado: " . $user['email'] . " - Rol: " . $user['rol']);
            } else {
                error_log("Usuario no encontrado para email: " . $email);
            }
            
            return $user ?: null;
            
        } catch (Exception $e) {
            error_log("Excepción en getUserByEmail: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verifica si existe un username
     */
    public function usernameExists($username) {
        try {
            $sql = "SELECT id FROM {$this->table} WHERE username = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare usernameExists: " . $this->conn->error);
                return false;
            }
            
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