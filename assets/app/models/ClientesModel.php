<?php

class ClientesModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    //Obtener todos los clientes con su información de nivel

    public function obtenerTodosLosClientes() {
        $sql = "SELECT c.id, c.nombre, c.email, c.puntos_acumulados, 
                       c.fecha_registro, c.usuario_id,
                       COALESCE(n.nombre, 'Sin nivel') as nivel_nombre,
                       n.id as nivel_id
                FROM clientes c
                LEFT JOIN niveles n ON c.nivel_id = n.id
                ORDER BY c.id DESC";
        
        $result = $this->db->query($sql);
        
        $clientes = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $clientes[] = $row;
            }
        }
        
        return $clientes;
    }

    /**
     * Obtener un cliente por ID
     */
    public function obtenerClientePorId($id) {
        $sql = "SELECT c.*, 
                       COALESCE(n.nombre, 'Sin nivel') as nivel_nombre,
                       n.puntos_minimos as nivel_puntos_min,
                       n.compras_necesarias as nivel_compras_necesarias
                FROM clientes c
                LEFT JOIN niveles n ON c.nivel_id = n.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Obtener clientes por nivel
     */
    public function obtenerClientesPorNivel($nivel_id) {
        $sql = "SELECT c.id, c.nombre, c.email, c.puntos_acumulados, c.fecha_registro
                FROM clientes c
                WHERE c.nivel_id = ?
                ORDER BY c.nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $nivel_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        
        return $clientes;
    }

    /**
     * Crear un nuevo cliente (requiere un usuario asociado)
     */
    public function crearCliente($nombre, $email, $password) {
        try {
            $this->db->begin_transaction();

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $rol = 'cliente';
            
            $sqlUsuario = "INSERT INTO usuarios (username, email, password_hash, rol) 
                          VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sqlUsuario);
            $stmt->bind_param("ssss", $nombre, $email, $password_hash, $rol);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear el usuario: " . $stmt->error);
            }
            
            $usuario_id = $this->db->insert_id;

            $sqlNivel = "SELECT id FROM niveles ORDER BY puntos_minimos ASC LIMIT 1";
            $resultNivel = $this->db->query($sqlNivel);
            $nivel = $resultNivel->fetch_assoc();
            $nivel_id = $nivel['id'];

            $sqlCliente = "INSERT INTO clientes (nombre, email, nivel_id, puntos_acumulados, usuario_id) 
                          VALUES (?, ?, ?, 0, ?)";
            $stmt = $this->db->prepare($sqlCliente);
            $stmt->bind_param("ssii", $nombre, $email, $nivel_id, $usuario_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear el cliente: " . $stmt->error);
            }
            
            $cliente_id = $this->db->insert_id;

            $this->db->commit();
            
            return [
                'success' => true,
                'cliente_id' => $cliente_id,
                'usuario_id' => $usuario_id
            ];

        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear un nuevo cliente vinculado a un usuario existente
     */
    public function crearClienteDesdeUsuario($usuario_id, $nombre, $email)
    {
        try {
            $this->db->begin_transaction();

            $sqlNivel = "SELECT id FROM niveles ORDER BY puntos_minimos ASC LIMIT 1";
            $resultNivel = $this->db->query($sqlNivel);
            $nivel = $resultNivel->fetch_assoc();
            $nivel_id = $nivel['id'] ?? 1;

            $sqlCliente = "INSERT INTO clientes (nombre, email, nivel_id, puntos_acumulados, usuario_id) VALUES (?, ?, ?, 0, ?)";
            $stmt = $this->db->prepare($sqlCliente);
            if (!$stmt) {
                throw new Exception('prepare_failed: ' . $this->db->error);
            }

            $stmt->bind_param("ssii", $nombre, $email, $nivel_id, $usuario_id);
            if (!$stmt->execute()) {
                throw new Exception('execute_failed: ' . $stmt->error);
            }

            $cliente_id = $this->db->insert_id;
            $this->db->commit();
            $stmt->close();

            return ['success' => true, 'cliente_id' => $cliente_id];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Actualizar información de un cliente
     */
    public function actualizarCliente($id, $nombre, $email) {
        try {
            $this->db->begin_transaction();

            $sql = "UPDATE clientes SET nombre = ?, email = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $nombre, $email, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar cliente");
            }

            $sqlUsuario = "SELECT usuario_id FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sqlUsuario);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $usuario_id = $row['usuario_id'];

            if ($usuario_id) {
                $sqlUpdateUsuario = "UPDATE usuarios SET username = ?, email = ? WHERE id = ?";
                $stmt = $this->db->prepare($sqlUpdateUsuario);
                $stmt->bind_param("ssi", $nombre, $email, $usuario_id);
                $stmt->execute();
            }

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Eliminar un cliente (también elimina el usuario asociado)
     */
    public function eliminarCliente($id) {
        try {
            $this->db->begin_transaction();

            $sql = "SELECT usuario_id FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $usuario_id = $row['usuario_id'] ?? null;

            $sql = "DELETE FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar cliente");
            }

            if ($usuario_id) {
                $sql = "DELETE FROM usuarios WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("i", $usuario_id);
                $stmt->execute();
            }

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener siguiente nivel para un cliente
     */
    public function obtenerSiguienteNivel($cliente_id) {
        $sql = "SELECT puntos_acumulados, nivel_id FROM clientes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();

        if (!$cliente) {
            return null;
        }

        $sql = "SELECT * FROM niveles 
                WHERE puntos_minimos > ? 
                ORDER BY puntos_minimos ASC 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cliente['puntos_acumulados']);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Contar compras de un cliente
     */
    public function contarComprasCliente($cliente_id) {
        $sql = "SELECT COUNT(*) as total FROM compras WHERE cliente_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
}
?>