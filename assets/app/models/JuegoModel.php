<?php
class JuegoModel {
    private $conn;
    private $table = 'juegos';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtiene todos los juegos ordenados por fecha de creación
     */
    public function getAllJuegos() {
        try {
            $sql = "SELECT id, nombre, descripcion, precio, genero, plataforma, fecha_creacion 
                    FROM {$this->table} 
                    ORDER BY fecha_creacion DESC";
            $result = $this->conn->query($sql);
            
            if (!$result) {
                error_log("Error en getAllJuegos: " . $this->conn->error);
                return [];
            }
            
            $juegos = [];
            while ($row = $result->fetch_assoc()) {
                $juegos[] = $row;
            }
            
            return $juegos;
            
        } catch (Exception $e) {
            error_log("Excepción en getAllJuegos: " . $e->getMessage());
            return [];
        }
    }
    public function getById($id) {
    try {
        $sql = "SELECT id, nombre, descripcion, precio, genero, plataforma, fecha_creacion 
                FROM {$this->table} 
                WHERE id = ? 
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;

    } catch (Exception $e) {
        error_log("Error en getById: " . $e->getMessage());
        return null;
    }
}

}

?>