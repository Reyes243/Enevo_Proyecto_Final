<?php
require_once __DIR__ . '/../models/PerfilModel.php';

class ExportarController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function exportarDatosUsuario($usuario_id) {
        // Usar el modelo existente para obtener datos
        $model = new PerfilModel($this->db);
        $perfil = $model->obtenerPerfil($usuario_id);

        if (!$perfil) {
            // Si no hay perfil, obtener datos básicos del usuario
            $sql = "SELECT u.*, 'Bronce' as nivel_nombre, 0 as puntos_acumulados, 
                    0 as total_compras, 0 as total_gastado
                    FROM usuarios u 
                    WHERE u.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $perfil = $stmt->get_result()->fetch_assoc();
        }

        if (!$perfil) {
            header('HTTP/1.1 404 Not Found');
            echo "Usuario no encontrado";
            exit();
        }

        // Obtener historial de compras
        $compras = $this->obtenerHistorialCompras($usuario_id);
        
        // Obtener recompensas canjeadas
        $recompensas = $this->obtenerRecompensasCanjeadas($usuario_id);

        // Preparar datos para exportación
        $datosExportar = [
            'informacion_personal' => [
                'nombre' => $perfil['nombre'] ?? $perfil['username'] ?? '',
                'email' => $perfil['email'] ?? '',
                'nivel_actual' => $perfil['nivel_nombre'] ?? 'Bronce',
                'puntos_acumulados' => $perfil['puntos_acumulados'] ?? 0,
                'fecha_registro' => $perfil['fecha_registro'] ?? ''
            ],
            'estadisticas' => [
                'total_compras' => $perfil['total_compras'] ?? 0,
                'puntos_totales' => $perfil['puntos_acumulados'] ?? 0
            ],
            'historial_compras' => $compras,
            'recompensas_canjeadas' => $recompensas,
            'fecha_exportacion' => date('Y-m-d H:i:s')
        ];

        // Generar JSON con los datos
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="mis_datos_enevo_' . date('Y-m-d') . '.json"');
        echo json_encode($datosExportar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function obtenerHistorialCompras($usuario_id) {
        $sql = "SELECT comp.* 
                FROM compras comp
                JOIN clientes c ON comp.cliente_id = c.id
                WHERE c.usuario_id = ?
                ORDER BY comp.fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $compras = [];
        while ($row = $result->fetch_assoc()) {
            $compras[] = $row;
        }
        
        return $compras;
    }

    private function obtenerRecompensasCanjeadas($usuario_id) {
        $sql = "SELECT rc.*, r.nombre as recompensa_nombre, r.descripcion, r.costo_puntos
                FROM recompensas_canjeadas rc
                JOIN recompensas r ON rc.recompensa_id = r.id
                JOIN clientes c ON rc.cliente_id = c.id
                WHERE c.usuario_id = ?
                ORDER BY rc.fecha_canjeo DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recompensas = [];
        while ($row = $result->fetch_assoc()) {
            $recompensas[] = $row;
        }
        
        return $recompensas;
    }
}
?>