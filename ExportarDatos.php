<?php
session_start();

require_once __DIR__ . '/assets/app/config/ConnectionController.php';
require_once __DIR__ . '/assets/app/controllers/ExportarContoller.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Crear la conexión usando tu ConnectionController
$connection = new ConnectionController();
$conn = $connection->connect();

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

$controller = new ExportarController($conn);
$controller->exportarDatosUsuario($_SESSION['user_id']);
?>