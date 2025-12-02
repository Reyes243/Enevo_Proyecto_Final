<?php
// assets/app/controllers/CarritoController.php
// SOLO PARA PETICIONES AJAX

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir la clase
require_once "CarritoClass.php";

// ========================================================
// MANEJADOR DE PETICIONES AJAX
// ========================================================
if (isset($_GET['action'])) {
    
    // Limpiar buffer
    if (ob_get_length()) ob_clean();
    
    // Forzar JSON
    header('Content-Type: application/json; charset=utf-8');
    
    // Desactivar errores visibles
    error_reporting(0);
    
    $response = ['success' => false, 'message' => 'Acción no válida'];
    
    try {
        switch ($_GET['action']) {
            case 'add':
                $id = $_POST['id_juego'] ?? null;
                
                if (!$id) {
                    $response = ['success' => false, 'message' => 'No se recibió ID del juego'];
                    break;
                }
                
                // Incluir controlador de juegos
                require_once "JuegoController.php";
                
                if (class_exists('JuegoController')) {
                    $controllerJuego = new JuegoController();
                    $datosJuego = $controllerJuego->getById($id);
                    
                    if ($datosJuego['success'] && isset($datosJuego['data'])) {
                        $juego = $datosJuego['data'];
                        CarritoController::agregar($juego['id'], $juego['nombre'], $juego['precio']);
                    } else {
                        // Datos por defecto
                        CarritoController::agregar($id, "Juego " . $id, 99.99);
                    }
                } else {
                    // Si no existe JuegoController, usar datos básicos
                    CarritoController::agregar($id, "Juego " . $id, 99.99);
                }
                
                $response = [
                    'success' => true,
                    'message' => '✅ Juego agregado al carrito correctamente',
                    'carrito' => $_SESSION['carrito'],
                    'total_items' => array_sum(array_column($_SESSION['carrito'], 'cantidad'))
                ];
                break;
                
            case 'get':
                CarritoController::initCarrito();
                $response = [
                    'success' => true,
                    'carrito' => $_SESSION['carrito'],
                    'total_items' => array_sum(array_column($_SESSION['carrito'], 'cantidad'))
                ];
                break;
                
            default:
                $response = ['success' => false, 'message' => 'Acción no válida'];
        }
        
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error interno'];
    }
    
    // ENVIAR SOLO JSON
    echo json_encode($response);
    exit;
}

// Si se accede directamente sin acción AJAX, no hacer nada
// (Este archivo es solo para AJAX)