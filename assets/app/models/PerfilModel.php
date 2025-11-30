<?php
class PerfilModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Obtener datos del perfil del usuario
    public function obtenerPerfil($usuario_id) {
        $sql = "SELECT c.*, u.username, u.email, n.nombre as nivel_nombre, n.puntos_minimos,
                       (SELECT COUNT(*) FROM compras WHERE cliente_id = c.id) as total_compras
                FROM clientes c 
                JOIN usuarios u ON c.usuario_id = u.id 
                LEFT JOIN niveles n ON c.nivel_id = n.id 
                WHERE c.usuario_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    // Obtener el siguiente nivel
    public function obtenerSiguienteNivel($puntos_actuales) {
        $sql = "SELECT * FROM niveles 
                WHERE puntos_minimos > ? 
                ORDER BY puntos_minimos ASC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $puntos_actuales);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    // Actualizar perfil del usuario
    public function actualizarPerfil($usuario_id, $nombre, $email) {
        // Actualizar en la tabla usuarios
        $sql_usuario = "UPDATE usuarios SET username = ?, email = ? WHERE id = ?";
        $stmt_usuario = $this->db->prepare($sql_usuario);
        $stmt_usuario->bind_param("ssi", $nombre, $email, $usuario_id);
        
        // Actualizar en la tabla clientes
        $sql_cliente = "UPDATE clientes SET nombre = ? WHERE usuario_id = ?";
        $stmt_cliente = $this->db->prepare($sql_cliente);
        $stmt_cliente->bind_param("si", $nombre, $usuario_id);
        
        // Ejecutar ambas actualizaciones
        $result_usuario = $stmt_usuario->execute();
        $result_cliente = $stmt_cliente->execute();
        
        return $result_usuario && $result_cliente;
    }

    // Verificar si el email ya existe (para evitar duplicados)
    public function verificarEmailExistente($email, $usuario_id_actual) {
        $sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $email, $usuario_id_actual);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    public function obtenerDatosCompletosPerfil($usuario_id) {
        $sql = "SELECT c.*, u.username, u.email, n.nombre as nivel_nombre,
                       (SELECT COUNT(*) FROM compras WHERE cliente_id = c.id) as total_compras,
                       (SELECT SUM(monto) FROM compras WHERE cliente_id = c.id) as total_gastado
                FROM clientes c 
                JOIN usuarios u ON c.usuario_id = u.id 
                LEFT JOIN niveles n ON c.nivel_id = n.id 
                WHERE c.usuario_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}
?>