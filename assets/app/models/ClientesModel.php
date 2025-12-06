<?php
/**
 * ClientesModel.php
 * Modelo para manejar operaciones de clientes en la base de datos
 */

class ClientesModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Obtener todos los clientes con su información de nivel
     */
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
            // Iniciar transacción
            $this->db->begin_transaction();

            // 1. Crear usuario primero
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

            // 2. Obtener el nivel inicial (Bronce - el de menor puntos)
            $sqlNivel = "SELECT id FROM niveles ORDER BY puntos_minimos ASC LIMIT 1";
            $resultNivel = $this->db->query($sqlNivel);
            $nivel = $resultNivel->fetch_assoc();
            $nivel_id = $nivel['id'];

            // 3. Crear cliente vinculado al usuario
            $sqlCliente = "INSERT INTO clientes (nombre, email, nivel_id, puntos_acumulados, usuario_id) 
                          VALUES (?, ?, ?, 0, ?)";
            $stmt = $this->db->prepare($sqlCliente);
            $stmt->bind_param("ssii", $nombre, $email, $nivel_id, $usuario_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear el cliente: " . $stmt->error);
            }
            
            $cliente_id = $this->db->insert_id;

            // Confirmar transacción
            $this->db->commit();
            
            return [
                'success' => true,
                'cliente_id' => $cliente_id,
                'usuario_id' => $usuario_id
            ];

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear un cliente a partir de un usuario ya creado
     * @param int $usuario_id
     * @param string $nombre
     * @param string $email
     * @return array ['success'=>bool,'cliente_id'=>int|'error'=>string]
     */
    public function crearClienteDesdeUsuario($usuario_id, $nombre, $email)
    {
        try {
            $this->db->begin_transaction();

            // Obtener el nivel inicial (el de menor puntos)
            $sqlNivel = "SELECT id FROM niveles ORDER BY puntos_minimos ASC LIMIT 1";
            $resultNivel = $this->db->query($sqlNivel);
            $nivel = $resultNivel->fetch_assoc();
            $nivel_id = $nivel['id'] ?? 1;

            // Insertar cliente
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

            // 1. Actualizar tabla clientes
            $sql = "UPDATE clientes SET nombre = ?, email = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $nombre, $email, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar cliente");
            }

            // 2. Obtener usuario_id del cliente
            $sqlUsuario = "SELECT usuario_id FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sqlUsuario);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $usuario_id = $row['usuario_id'];

            // 3. Actualizar tabla usuarios si existe usuario vinculado
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

            // 1. Obtener usuario_id antes de eliminar el cliente
            $sql = "SELECT usuario_id FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $usuario_id = $row['usuario_id'] ?? null;

            // 2. Eliminar cliente (las compras se eliminarán por CASCADE)
            $sql = "DELETE FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar cliente");
            }

            // 3. Eliminar usuario asociado si existe
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
        // Obtener puntos actuales del cliente
        $sql = "SELECT puntos_acumulados, nivel_id FROM clientes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();

        if (!$cliente) {
            return null;
        }

        // Buscar el siguiente nivel
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