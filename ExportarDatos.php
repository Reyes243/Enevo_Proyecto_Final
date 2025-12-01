<?php
session_start();

require_once __DIR__ . '/assets/app/config/ConnectionController.php';
require_once __DIR__ . '/assets/app/controllers/ExportarController.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

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

    // Crear instancia del controlador y exportar
    $controller = new ExportarController($conn);
    $controller->exportarDatosUsuario($_SESSION['user_id'], $formato);

} catch (Exception $e) {
    // Manejo de errores
    error_log("Error en ExportarDatos.php: " . $e->getMessage());
    
    $_SESSION['error'] = 'Hubo un error al exportar tus datos. Por favor, intenta nuevamente.';
    header('Location: pages/Perfil.php');
    exit();
}
?>