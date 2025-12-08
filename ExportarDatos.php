<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/assets/app/config/ConnectionController.php';
require_once __DIR__ . '/assets/app/controllers/ExportarController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$tipo = $_GET['tipo'] ?? 'perfil';
$formato = $_GET['formato'] ?? 'pdf';

// Validar formato permitido
if (!in_array($formato, ['pdf', 'json'])) {
    $formato = 'pdf';
}

try {
    $connection = new ConnectionController();
    $conn = $connection->connect();

    if (!$conn) {
        throw new Exception("No se pudo establecer conexión con la base de datos.");
    }

    $controller = new ExportarController($conn);
    
    if ($tipo === 'historial') {
        $controller->exportarHistorialCompras($_SESSION['user_id']);
    } else {
        $controller->exportarDatosUsuario($_SESSION['user_id'], $formato);
    }

} catch (Exception $e) {
    error_log("Error en ExportarDatos.php: " . $e->getMessage());
    
    $_SESSION['error'] = 'Hubo un error al exportar tus datos. Por favor, intenta nuevamente.';
    
    if ($tipo === 'historial') {
        header('Location: views/HistorialCompras.php');
    } else {
        header('Location: views/Perfil.php');
    }
    exit();
}
?>