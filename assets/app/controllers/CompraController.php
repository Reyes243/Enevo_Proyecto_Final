<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/CompraModel.php';
require_once __DIR__ . '/CarritoClass.php';

class CompraController
{
    
     // Procesa la compra del carrito actual
    
    public static function procesarCompra()
    {
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'message' => 'Debe iniciar sesión para realizar una compra'
            ];
        }

        $carrito = CarritoController::obtenerCarrito();

        if (empty($carrito)) {
            return [
                'success' => false,
                'message' => 'El carrito está vacío'
            ];
        }

        $total = CarritoController::total();

        $model = new CompraModel();
        $resultado = $model->procesarCompraCarrito(
            $_SESSION['user_id'],
            $carrito,
            $total
        );

        if ($resultado['success']) {
            CarritoController::vaciar();
        }

        return $resultado;
    }

    
     // Obtiene los datos necesarios para mostrar en el checkout
     
    public static function obtenerDatosCheckout()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $carrito = CarritoController::obtenerCarrito();
        
        if (empty($carrito)) {
            return null;
        }

        $total = CarritoController::total();
        $puntos_a_generar = floor($total / 10);

        return [
            'carrito' => $carrito,
            'total' => $total,
            'puntos_a_generar' => $puntos_a_generar,
            'cantidad_items' => count($carrito)
        ];
    }

    
    // Calcula cuántos puntos se generarán por un monto
    
    public static function calcularPuntos($monto)
    {
        return floor($monto / 10);
    }

    
     //Procesar la compra usando puntos acumulados del cliente
     
    public static function procesarCompraConPuntos()
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'Debe iniciar sesión para realizar una compra'];
        }

        $carrito = CarritoController::obtenerCarrito();
        if (empty($carrito)) {
            return ['success' => false, 'message' => 'El carrito está vacío'];
        }

        $puntos_requeridos = 0;
        foreach ($carrito as $item) {
            $puntos_unit = floor($item['precio'] / 50);
            $puntos_requeridos += $puntos_unit * $item['cantidad'];
        }

        $model = new CompraModel();
        $cliente_id = $model->obtenerClienteId($_SESSION['user_id']);
        if (!$cliente_id) {
            return ['success' => false, 'message' => 'Cliente no encontrado'];
        }

        $puntos_actuales = $model->obtenerPuntosCliente($cliente_id);
        if ($puntos_actuales === null) {
            return ['success' => false, 'message' => 'No se pudo obtener los puntos del cliente'];
        }

        if ($puntos_actuales < $puntos_requeridos) {
            return ['success' => false, 'message' => 'Puntos insuficientes', 'puntos_actuales' => $puntos_actuales, 'puntos_requeridos' => $puntos_requeridos];
        }

        $resultado = $model->procesarCompraCarritoConPuntos($_SESSION['user_id'], $carrito, $puntos_requeridos);

        if ($resultado['success']) {
            CarritoController::vaciar();
        }

        return $resultado;
    }
}

// Manejo de peticiones AJAX/POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['action']) && $_POST['action'] === 'procesar_compra') {
        $resultado = CompraController::procesarCompra();
        
        // Devolver respuesta JSON
        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit();
    }

    if (isset($_POST['action']) && $_POST['action'] === 'procesar_compra_puntos') {
        $resultado = CompraController::procesarCompraConPuntos();

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    if (isset($_GET['action']) && $_GET['action'] === 'datos_checkout') {
        $datos = CompraController::obtenerDatosCheckout();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $datos !== null,
            'data' => $datos
        ]);
        exit();
    }
}
?>