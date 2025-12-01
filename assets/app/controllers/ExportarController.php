<?php
require_once __DIR__ . '/../models/PerfilModel.php';

class ExportarController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function exportarDatosUsuario($usuario_id, $formato = 'json') {
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

        // Exportar según el formato solicitado
        if ($formato === 'pdf') {
            $this->generarPDF($datosExportar);
        } else {
            $this->generarJSON($datosExportar);
        }
    }

    private function generarJSON($datos) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="mis_datos_enevo_' . date('Y-m-d') . '.json"');
        echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function generarPDF($datos) {
        // Generar contenido HTML optimizado para imprimir como PDF
        $html = $this->generarHTMLParaPDF($datos);
        
        // Configurar headers para HTML
        header('Content-Type: text/html; charset=utf-8');
        
        // Mostrar HTML con script de impresión automática
        echo $html;
        echo '<script>
            window.onload = function() {
                window.print();
            };
        </script>';
        exit();
    }

    private function generarHTMLParaPDF($datos) {
        $nombre = htmlspecialchars($datos['informacion_personal']['nombre']);
        $email = htmlspecialchars($datos['informacion_personal']['email']);
        $nivel = htmlspecialchars($datos['informacion_personal']['nivel_actual']);
        $puntos = htmlspecialchars($datos['informacion_personal']['puntos_acumulados']);
        $fechaRegistro = date('d/m/Y', strtotime($datos['informacion_personal']['fecha_registro']));
        $totalCompras = htmlspecialchars($datos['estadisticas']['total_compras']);
        $fechaExportacion = date('d/m/Y H:i:s', strtotime($datos['fecha_exportacion']));

        $html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Datos - Enevo</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            padding: 8px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .info-value {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .no-data {
            font-style: italic;
            color: #777;
            padding: 15px;
            text-align: center;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛍️ ENEVO - Mis Datos Personales</h1>
        <p>Exportación generada el: ' . $fechaExportacion . '</p>
    </div>

    <!-- INFORMACIÓN PERSONAL -->
    <div class="section">
        <h2>📋 Información Personal</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value">' . $nombre . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Correo Electrónico:</div>
                <div class="info-value">' . $email . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Registro:</div>
                <div class="info-value">' . $fechaRegistro . '</div>
            </div>
        </div>
    </div>

    <!-- NIVEL Y PUNTOS -->
    <div class="section">
        <h2>⭐ Nivel y Puntos</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nivel Actual:</div>
                <div class="info-value">' . $nivel . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Puntos Acumulados:</div>
                <div class="info-value">' . $puntos . ' puntos</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total de Compras:</div>
                <div class="info-value">' . $totalCompras . '</div>
            </div>
        </div>
    </div>

    <!-- HISTORIAL DE COMPRAS -->
    <div class="section">
        <h2>🛒 Historial de Compras</h2>';

        if (!empty($datos['historial_compras'])) {
            $html .= '
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Puntos Ganados</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>';
            
            foreach ($datos['historial_compras'] as $compra) {
                $fecha = date('d/m/Y', strtotime($compra['fecha']));
                $total = '$' . number_format($compra['total'], 2);
                $puntos = $compra['puntos_ganados'] ?? 0;
                $estado = htmlspecialchars($compra['estado'] ?? 'completada');
                
                $html .= '
                <tr>
                    <td>' . $fecha . '</td>
                    <td>' . $total . '</td>
                    <td>' . $puntos . ' pts</td>
                    <td>' . $estado . '</td>
                </tr>';
            }
            
            $html .= '
            </tbody>
        </table>';
        } else {
            $html .= '<div class="no-data">No hay compras registradas</div>';
        }

        $html .= '
    </div>

    <!-- RECOMPENSAS CANJEADAS -->
    <div class="section">
        <h2>🎁 Recompensas Canjeadas</h2>';

        if (!empty($datos['recompensas_canjeadas'])) {
            $html .= '
        <table>
            <thead>
                <tr>
                    <th>Recompensa</th>
                    <th>Descripción</th>
                    <th>Puntos Usados</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>';
            
            foreach ($datos['recompensas_canjeadas'] as $recompensa) {
                $nombre = htmlspecialchars($recompensa['recompensa_nombre']);
                $desc = htmlspecialchars($recompensa['descripcion']);
                $puntos = $recompensa['costo_puntos'];
                $fecha = date('d/m/Y', strtotime($recompensa['fecha_canjeo']));
                
                $html .= '
                <tr>
                    <td>' . $nombre . '</td>
                    <td>' . $desc . '</td>
                    <td>' . $puntos . ' pts</td>
                    <td>' . $fecha . '</td>
                </tr>';
            }
            
            $html .= '
            </tbody>
        </table>';
        } else {
            $html .= '<div class="no-data">No hay recompensas canjeadas</div>';
        }

        $html .= '
    </div>

    <div class="footer">
        <p><strong>ENEVO</strong> - Sistema de Gestión de Clientes</p>
        <p>Este documento contiene información confidencial. Por favor, manténgalo seguro.</p>
    </div>
</body>
</html>';

        return $html;
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