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

        // Preparar datos para exportación (solo información básica)
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
            'fecha_exportacion' => date('Y-m-d H:i:s')
        ];

        // Exportar según el formato solicitado
        if ($formato === 'pdf') {
            $this->generarPDF($datosExportar);
        } else {
            $this->generarJSON($datosExportar);
        }
    }

    // NUEVO MÉTODO PARA EXPORTAR HISTORIAL DE COMPRAS
    public function exportarHistorialCompras($usuario_id) {
        // Obtener información del usuario
        $sqlUsuario = "SELECT nombre, email FROM usuarios WHERE id = ?";
        $stmtUsuario = $this->db->prepare($sqlUsuario);
        $stmtUsuario->bind_param("i", $usuario_id);
        $stmtUsuario->execute();
        $usuario = $stmtUsuario->get_result()->fetch_assoc();

        if (!$usuario) {
            header('HTTP/1.1 404 Not Found');
            echo "Usuario no encontrado";
            exit();
        }

        // Obtener historial de compras
        $sqlCompras = "SELECT 
                c.id as compra_id,
                c.fecha_compra,
                c.monto_pagado,
                c.puntos_ganados,
                j.nombre as juego_nombre,
                j.precio as juego_precio
            FROM compras c
            LEFT JOIN juegos j ON c.juego_id = j.id
            WHERE c.usuario_id = ?
            ORDER BY c.fecha_compra DESC";
        
        $stmtCompras = $this->db->prepare($sqlCompras);
        $stmtCompras->bind_param("i", $usuario_id);
        $stmtCompras->execute();
        $compras = $stmtCompras->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calcular totales
        $totalGastado = 0;
        $totalPuntos = 0;
        foreach ($compras as $compra) {
            $totalGastado += $compra['monto_pagado'];
            $totalPuntos += $compra['puntos_ganados'];
        }

        $datosExportar = [
            'usuario' => [
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email']
            ],
            'compras' => $compras,
            'resumen' => [
                'total_compras' => count($compras),
                'total_gastado' => $totalGastado,
                'total_puntos' => $totalPuntos
            ],
            'fecha_exportacion' => date('Y-m-d H:i:s')
        ];

        // Generar PDF del historial
        $this->generarPDFHistorial($datosExportar);
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

    private function generarPDFHistorial($datos) {
        // Generar contenido HTML del historial
        $html = $this->generarHTMLHistorial($datos);
        
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Datos - Enevo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 0;
            size: A4;
        }
        
        body {
            font-family: "Poppins", sans-serif;
            background-color: #363d57;
            color: #fff;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        
        .export-container {
            width: 100%;
            max-width: 800px;
            background-color: #1f2833;
            padding: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }
        
        .header {
            text-align: center;
            margin-bottom: 50px;
            padding-bottom: 25px;
            border-bottom: 3px solid #ffd700;
        }
        
        .header h1 {
            font-size: 36px;
            color: #ffd700;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            color: #c5c6c7;
            font-size: 16px;
            margin-top: 8px;
        }
        
        .section {
            margin-bottom: 35px;
        }
        
        .section-title {
            color: #ffd700;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 215, 0, 0.3);
        }
        
        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
            background-color: rgba(255, 255, 255, 0.03);
        }
        
        .info-label {
            color: #c5c6c7;
            font-size: 15px;
            font-weight: 400;
        }
        
        .info-value {
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
        }
        
        .highlight {
            background: rgba(255, 215, 0, 0.15);
            padding: 5px 15px;
            border-radius: 4px;
            color: #ffd700;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            padding-top: 25px;
            border-top: 2px solid rgba(255, 215, 0, 0.3);
            color: #c5c6c7;
            font-size: 13px;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .footer strong {
            color: #ffd700;
        }
        
        @media print {
            body {
                background-color: #363d57;
                padding: 0;
            }
            
            .export-container {
                box-shadow: none;
                padding: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="export-container">
        
        <div class="header">
            <h1>ENEVO</h1>
            <p class="subtitle">Mis Datos Personales</p>
            <p class="subtitle" style="margin-top: 10px; font-size: 14px;">Exportado el: ' . $fechaExportacion . '</p>
        </div>

        <!-- INFORMACIÓN PERSONAL -->
        <div class="section">
            <div class="section-title">Información Personal</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">' . $nombre . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Correo Electrónico:</span>
                    <span class="info-value">' . $email . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Registro:</span>
                    <span class="info-value">' . $fechaRegistro . '</span>
                </div>
            </div>
        </div>

        <!-- NIVEL Y PUNTOS -->
        <div class="section">
            <div class="section-title">Nivel y Puntos</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Nivel Actual:</span>
                    <span class="info-value highlight">' . $nivel . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Puntos Acumulados:</span>
                    <span class="info-value highlight">' . $puntos . ' puntos</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total de Compras Realizadas:</span>
                    <span class="info-value">' . $totalCompras . '</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>ENEVO</strong> - Sistema de Gestión de Clientes</p>
            <p style="margin-top: 8px;">© 2025 – Todos los derechos reservados</p>
            <p style="margin-top: 8px; font-size: 12px;">Este documento contiene información confidencial</p>
        </div>
        
    </div>
</body>
</html>';

        return $html;
    }

    private function generarHTMLHistorial($datos) {
        $nombre = htmlspecialchars($datos['usuario']['nombre']);
        $email = htmlspecialchars($datos['usuario']['email']);
        $fechaExportacion = date('d/m/Y H:i:s', strtotime($datos['fecha_exportacion']));
        
        $totalCompras = $datos['resumen']['total_compras'];
        $totalGastado = number_format($datos['resumen']['total_gastado'], 2);
        $totalPuntos = $datos['resumen']['total_puntos'];

        // Generar filas de la tabla
        $filasCompras = '';
        if (empty($datos['compras'])) {
            $filasCompras = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #c5c6c7;">No hay compras registradas</td></tr>';
        } else {
            foreach ($datos['compras'] as $compra) {
                $fecha = date('d/m/Y', strtotime($compra['fecha_compra']));
                $monto = number_format($compra['monto_pagado'], 2);
                $juego = htmlspecialchars($compra['juego_nombre'] ?? 'N/A');
                $puntos = $compra['puntos_ganados'];
                $compraId = $compra['compra_id'];

                $filasCompras .= "
                <tr>
                    <td style='text-align: center;'>{$compraId}</td>
                    <td>{$juego}</td>
                    <td style='text-align: center;'>{$fecha}</td>
                    <td style='text-align: right;'>Mex$ {$monto}</td>
                    <td style='text-align: center;'>{$puntos}</td>
                </tr>";
            }
        }

        $html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Compras - Enevo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body {
            font-family: "Poppins", sans-serif;
            background-color: #363d57;
            color: #fff;
            padding: 40px;
        }
        
        .export-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background-color: #1f2833;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #ffd700;
        }
        
        .header h1 {
            font-size: 36px;
            color: #ffd700;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            color: #c5c6c7;
            font-size: 16px;
            margin-top: 8px;
        }

        .user-info {
            background-color: rgba(255, 215, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #ffd700;
        }

        .user-info p {
            color: #c5c6c7;
            margin: 5px 0;
            font-size: 14px;
        }

        .user-info strong {
            color: #ffd700;
        }
        
        .section-title {
            color: #ffd700;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 215, 0, 0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        thead {
            background-color: rgba(255, 215, 0, 0.15);
        }

        th {
            padding: 15px;
            text-align: left;
            color: #ffd700;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid #ffd700;
        }

        td {
            padding: 12px 15px;
            color: #c5c6c7;
            font-size: 13px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .resumen {
            background-color: rgba(255, 215, 0, 0.1);
            padding: 25px;
            margin-top: 30px;
            border-radius: 8px;
        }

        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .resumen-item {
            text-align: center;
        }

        .resumen-label {
            color: #c5c6c7;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .resumen-value {
            color: #ffd700;
            font-size: 24px;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid rgba(255, 215, 0, 0.3);
            color: #c5c6c7;
            font-size: 12px;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .footer strong {
            color: #ffd700;
        }
        
        @media print {
            body {
                background-color: #363d57;
                padding: 0;
            }
            
            .export-container {
                box-shadow: none;
            }

            tbody tr:hover {
                background-color: transparent;
            }
        }
    </style>
</head>
<body>
    <div class="export-container">
        
        <div class="header">
            <h1>ENEVO</h1>
            <p class="subtitle">Historial de Compras</p>
            <p class="subtitle" style="margin-top: 10px; font-size: 14px;">Exportado el: ' . $fechaExportacion . '</p>
        </div>

        <div class="user-info">
            <p><strong>Usuario:</strong> ' . $nombre . '</p>
            <p><strong>Email:</strong> ' . $email . '</p>
        </div>

        <div class="section-title">Detalle de Compras</div>

        <table>
            <thead>
                <tr>
                    <th style="text-align: center;">ID</th>
                    <th>Juego</th>
                    <th style="text-align: center;">Fecha</th>
                    <th style="text-align: right;">Monto</th>
                    <th style="text-align: center;">Puntos</th>
                </tr>
            </thead>
            <tbody>
                ' . $filasCompras . '
            </tbody>
        </table>

        <div class="resumen">
            <div class="section-title" style="margin-bottom: 20px;">Resumen General</div>
            <div class="resumen-grid">
                <div class="resumen-item">
                    <div class="resumen-label">Total de Compras</div>
                    <div class="resumen-value">' . $totalCompras . '</div>
                </div>
                <div class="resumen-item">
                    <div class="resumen-label">Total Gastado</div>
                    <div class="resumen-value">$' . $totalGastado . '</div>
                </div>
                <div class="resumen-item">
                    <div class="resumen-label">Puntos Generados</div>
                    <div class="resumen-value">' . $totalPuntos . '</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>ENEVO</strong> - Sistema de Gestión de Clientes</p>
            <p style="margin-top: 8px;">© 2025 – Todos los derechos reservados</p>
        </div>
        
    </div>
</body>
</html>';

        return $html;
    }
}
?>