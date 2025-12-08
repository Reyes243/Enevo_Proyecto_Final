<?php

require_once __DIR__ . '/../config/ConnectionController.php';

class CompraModel
{
    private $conn;

    public function __construct()
    {
        $connection = new ConnectionController();
        $this->conn = $connection->connect();
    }

    
     // Registra una compra en la base de datos
   
    public function registrarCompra($cliente_id, $juego_id, $cantidad, $monto, $puntos_generados)
    {
        $sql = "INSERT INTO compras (cliente_id, juego_id, cantidad, monto, puntos_generados) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiidi", $cliente_id, $juego_id, $cantidad, $monto, $puntos_generados);
        
        return $stmt->execute();
    }

    
     // Obtiene el ID del cliente basado en el user_id
     
    public function obtenerClienteId($user_id)
    {
        $sql = "SELECT id FROM clientes WHERE usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['id'];
        }
        
        return null;
    }

    
     //Actualiza los puntos acumulados del cliente
     
    public function actualizarPuntos($cliente_id, $puntos_nuevos)
    {
        $sql = "UPDATE clientes 
                SET puntos_acumulados = puntos_acumulados + ? 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $puntos_nuevos, $cliente_id);
        
        return $stmt->execute();
    }

    
     //Actualiza el nivel del cliente basado en sus puntos acumulados
     
    public function actualizarNivel($cliente_id)
    {
        $sql = "SELECT puntos_acumulados FROM clientes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();
        
        if (!$cliente) {
            return false;
        }
        
        $puntos = $cliente['puntos_acumulados'];
        
        $sql_nivel = "SELECT id FROM niveles 
                      WHERE puntos_minimos <= ? 
                      ORDER BY puntos_minimos DESC 
                      LIMIT 1";
        
        $stmt_nivel = $this->conn->prepare($sql_nivel);
        $stmt_nivel->bind_param("i", $puntos);
        $stmt_nivel->execute();
        $result_nivel = $stmt_nivel->get_result();
        $nivel = $result_nivel->fetch_assoc();
        
        if (!$nivel) {
            return false;
        }
        
        $sql_update = "UPDATE clientes SET nivel_id = ? WHERE id = ?";
        $stmt_update = $this->conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $nivel['id'], $cliente_id);
        
        return $stmt_update->execute();
    }

    
     //Obtener puntos acumulados del cliente
   
    public function obtenerPuntosCliente($cliente_id)
    {
        $sql = "SELECT puntos_acumulados FROM clientes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return (int)$row['puntos_acumulados'];
        }
        return null;
    }

    
     // Descontar puntos del cliente (usar cuando se compra con puntos)
    
    public function descontarPuntos($cliente_id, $puntos)
    {
        $sql = "UPDATE clientes SET puntos_acumulados = puntos_acumulados - ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $puntos, $cliente_id);
        return $stmt->execute();
    }

    
     // Obtiene el nombre del nivel actual del cliente
    
    public function obtenerNivelCliente($cliente_id)
    {
        $sql = "SELECT n.nombre 
                FROM clientes c 
                INNER JOIN niveles n ON c.nivel_id = n.id 
                WHERE c.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['nombre'];
        }
        
        return null;
    }

    
     // Obtiene el historial de compras de un cliente
   
    public function obtenerHistorialCompras($user_id)
    {
        $cliente_id = $this->obtenerClienteId($user_id);
        
        if (!$cliente_id) {
            return [];
        }
        
        $sql = "SELECT c.id, c.fecha, c.monto, c.puntos_generados, c.cantidad,
                       j.nombre as juego_nombre
                FROM compras c
                LEFT JOIN juegos j ON c.juego_id = j.id
                WHERE c.cliente_id = ?
                ORDER BY c.fecha DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $historial = [];
        while ($row = $result->fetch_assoc()) {
            $historial[] = $row;
        }
        
        return $historial;
    }

    
     // Obtiene estadísticas de ventas por juego (para admin)
     
    public function obtenerEstadisticasVentas()
    {
        $sql = "SELECT 
                    j.id,
                    j.nombre,
                    COALESCE(SUM(c.cantidad), 0) as copias_vendidas,
                    COALESCE(SUM(c.monto), 0) as ingresos_totales,
                    COUNT(DISTINCT c.cliente_id) as clientes_unicos
                FROM juegos j
                LEFT JOIN compras c ON j.id = c.juego_id
                GROUP BY j.id, j.nombre
                ORDER BY copias_vendidas DESC";
        
        $result = $this->conn->query($sql);
        
        $ventas = [];
        while ($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
        
        return $ventas;
    }

    
     // Procesa una compra completa del carrito
    
    public function procesarCompraCarrito($user_id, $carrito, $total_monto)
    {
        $this->conn->begin_transaction();
        
        try {
            $cliente_id = $this->obtenerClienteId($user_id);
            
            if (!$cliente_id) {
                throw new Exception("Cliente no encontrado");
            }
            
            $puntos_generados = floor($total_monto / 10);
            
            foreach ($carrito as $juego_id => $item) {
                $monto_item = $item['precio'] * $item['cantidad'];
                $puntos_item = floor($monto_item / 10);
                
                $this->registrarCompra(
                    $cliente_id, 
                    $juego_id, 
                    $item['cantidad'], 
                    $monto_item, 
                    $puntos_item
                );
            }
            
            $this->actualizarPuntos($cliente_id, $puntos_generados);
            
            $this->actualizarNivel($cliente_id);
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Compra realizada exitosamente',
                'puntos_generados' => $puntos_generados
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            
            return [
                'success' => false,
                'message' => 'Error al procesar la compra: ' . $e->getMessage()
            ];
        }
    }

    
     //Procesar compra usando puntos del cliente
     
    public function procesarCompraCarritoConPuntos($user_id, $carrito, $puntos_requeridos)
    {
        $this->conn->begin_transaction();

        try {
            $cliente_id = $this->obtenerClienteId($user_id);
            if (!$cliente_id) {
                throw new Exception("Cliente no encontrado");
            }

            foreach ($carrito as $juego_id => $item) {
                $monto_item = 0;
                $puntos_item = 0;

                $this->registrarCompra(
                    $cliente_id,
                    $juego_id,
                    $item['cantidad'],
                    $monto_item,
                    $puntos_item
                );
            }

            $this->descontarPuntos($cliente_id, $puntos_requeridos);

            $this->actualizarNivel($cliente_id);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Compra realizada con puntos exitosamente',
                'puntos_usados' => $puntos_requeridos
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Error al procesar la compra con puntos: ' . $e->getMessage()
            ];
        }
    }
}
?>