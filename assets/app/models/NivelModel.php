<?php
class NivelModel {
    private $conn;
    private $table = 'niveles';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtiene todos los niveles ordenados por puntos mínimos
     */
    public function getAllNiveles() {
        try {
            $sql = "SELECT id, nombre, puntos_minimos, compras_necesarias, beneficios FROM {$this->table} ORDER BY puntos_minimos ASC";
            $result = $this->conn->query($sql);
            
            if (!$result) {
                error_log("Error en getAllNiveles: " . $this->conn->error);
                return [];
            }
            
            $niveles = [];
            while ($row = $result->fetch_assoc()) {
                $niveles[] = $row;
            }
            
            return $niveles;
            
        } catch (Exception $e) {
            error_log("Excepción en getAllNiveles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un nivel por ID
     */
    public function getNivelById($id) {
        try {
            $sql = "SELECT id, nombre, puntos_minimos, compras_necesarias, beneficios FROM {$this->table} WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare getNivelById: " . $this->conn->error);
                return null;
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                error_log("Error ejecutando getNivelById: " . $stmt->error);
                $stmt->close();
                return null;
            }
            
            $result = $stmt->get_result();
            $nivel = $result->fetch_assoc();
            $stmt->close();
            
            return $nivel ?: null;
            
        } catch (Exception $e) {
            error_log("Excepción en getNivelById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crea un nuevo nivel
     */
    public function createNivel($nombre, $puntos_minimos, $compras_necesarias, $beneficios) {
        try {
            $sql = "INSERT INTO {$this->table} (nombre, puntos_minimos, compras_necesarias, beneficios) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare createNivel: " . $this->conn->error);
                return [
                    'success' => false,
                    'error' => 'prepare_failed'
                ];
            }
            
            $stmt->bind_param("siis", $nombre, $puntos_minimos, $compras_necesarias, $beneficios);
            $result = $stmt->execute();
            
            if (!$result) {
                $errno = $stmt->errno;
                $error_msg = $stmt->error;
                error_log("Error en execute createNivel: {$error_msg}");
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
            
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return [
                'success' => true,
                'error' => null,
                'id' => $insertId
            ];
            
        } catch (Exception $e) {
            error_log("Error en createNivel: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'exception'
            ];
        }
    }

    /**
     * Actualiza un nivel existente
     */
    public function updateNivel($id, $nombre, $puntos_minimos, $compras_necesarias, $beneficios) {
        try {
            $sql = "UPDATE {$this->table} SET nombre = ?, puntos_minimos = ?, compras_necesarias = ?, beneficios = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare updateNivel: " . $this->conn->error);
                return [
                    'success' => false,
                    'error' => 'prepare_failed'
                ];
            }
            
            $stmt->bind_param("siisi", $nombre, $puntos_minimos, $compras_necesarias, $beneficios, $id);
            $result = $stmt->execute();
            
            if (!$result) {
                $error_msg = $stmt->error;
                error_log("Error en execute updateNivel: {$error_msg}");
                $stmt->close();
                
                return [
                    'success' => false,
                    'error' => 'execution_failed'
                ];
            }
            
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return [
                'success' => true,
                'error' => null,
                'affected_rows' => $affected_rows
            ];
            
        } catch (Exception $e) {
            error_log("Error en updateNivel: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'exception'
            ];
        }
    }

    /**
     * Elimina un nivel
     */
    public function deleteNivel($id) {
        try {
            // Primero verificar si hay clientes asociados a este nivel
            $sqlCheck = "SELECT COUNT(*) as count FROM clientes WHERE nivel_id = ?";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            
            if (!$stmtCheck) {
                error_log("Error en prepare deleteNivel (check): " . $this->conn->error);
                return [
                    'success' => false,
                    'error' => 'prepare_failed'
                ];
            }
            
            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $row = $resultCheck->fetch_assoc();
            $stmtCheck->close();
            
            if ($row['count'] > 0) {
                return [
                    'success' => false,
                    'error' => 'has_clients'
                ];
            }
            
            // Si no hay clientes, proceder a eliminar
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error en prepare deleteNivel: " . $this->conn->error);
                return [
                    'success' => false,
                    'error' => 'prepare_failed'
                ];
            }
            
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            
            if (!$result) {
                $error_msg = $stmt->error;
                error_log("Error en execute deleteNivel: {$error_msg}");
                $stmt->close();
                
                return [
                    'success' => false,
                    'error' => 'execution_failed'
                ];
            }
            
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected_rows === 0) {
                return [
                    'success' => false,
                    'error' => 'not_found'
                ];
            }
            
            return [
                'success' => true,
                'error' => null
            ];
            
        } catch (Exception $e) {
            error_log("Error en deleteNivel: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'exception'
            ];
        }
    }

    /**
     * Verifica si existe un nombre de nivel
     */
    public function nombreExists($nombre, $excludeId = null) {
        try {
            if ($excludeId) {
                $sql = "SELECT id FROM {$this->table} WHERE nombre = ? AND id != ? LIMIT 1";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("si", $nombre, $excludeId);
            } else {
                $sql = "SELECT id FROM {$this->table} WHERE nombre = ? LIMIT 1";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $nombre);
            }
            
            if (!$stmt) {
                error_log("Error en prepare nombreExists: " . $this->conn->error);
                return false;
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();
            
            return $exists;
            
        } catch (Exception $e) {
            error_log("Error en nombreExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los clientes asociados a un nivel específico
     */
    public function getClientesByNivel($nivelId) {
    try {
        $sql = "SELECT id, nombre, email 
                FROM clientes 
                WHERE nivel_id = ? 
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Error en prepare getClientesByNivel: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param("i", $nivelId);
        $stmt->execute();
        $result = $stmt->get_result();

        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }

        $stmt->close();
        return $clientes;

    } catch (Exception $e) {
        error_log("Error en getClientesByNivel: " . $e->getMessage());
        return [];
    }
}
}
?>