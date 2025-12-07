<?php
session_start();

// IMPORTANTE: No usar echo ni print antes de los headers
error_reporting(E_ALL);
ini_set('display_errors', 0); // Cambiar a 0 para producción

require_once __DIR__ . '/assets/app/config/ConnectionController.php';
require_once __DIR__ . '/assets/app/controllers/ExportarController.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Obtener el tipo de exportación
$tipo = $_GET['tipo'] ?? 'perfil';

// Obtener el formato solicitado (por defecto PDF)
$formato = $_GET['formato'] ?? 'pdf';

// Validar formato
if (!in_array($formato, ['pdf', 'json'])) {
    $formato = 'pdf';
}

try {
    // Crear la conexión
    $connection = new ConnectionController();
    $conn = $connection->connect();

    if (!$conn) {
        throw new Exception("No se pudo establecer conexión con la base de datos.");
    }

    // Crear instancia del controlador
    $controller = new ExportarController($conn);
    
    // Exportar según el tipo solicitado
    if ($tipo === 'historial') {
        // Exportar historial de compras (siempre en PDF)
        $controller->exportarHistorialCompras($_SESSION['user_id']);
    } else {
        // Exportar perfil de usuario (puede ser PDF o JSON)
        $controller->exportarDatosUsuario($_SESSION['user_id'], $formato);
    }

} catch (Exception $e) {
    // Manejo de errores
    error_log("Error en ExportarDatos.php: " . $e->getMessage());
    
    $_SESSION['error'] = 'Hubo un error al exportar tus datos. Por favor, intenta nuevamente.';
    
    // Redirigir según el tipo
    if ($tipo === 'historial') {
        header('Location: views/HistorialCompras.php');
    } else {
        header('Location: views/Perfil.php');
    }
    exit();
}
?>