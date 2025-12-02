<?php
// assets/app/controllers/CompraController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/CompraModel.php';
require_once __DIR__ . '/CarritoClass.php';

class CompraController
{
    /**
     * Procesa la compra del carrito actual
     * @return array Resultado con 'success', 'message' y datos adicionales
     */
    public static function procesarCompra()
    {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'message' => 'Debe iniciar sesión para realizar una compra'
            ];
        }

        // Obtener el carrito
        $carrito = CarritoController::obtenerCarrito();

        // Verificar que el carrito no esté vacío
        if (empty($carrito)) {
            return [
                'success' => false,
                'message' => 'El carrito está vacío'
            ];
        }

        // Calcular el total
        $total = CarritoController::total();

        // Procesar la compra usando el modelo
        $model = new CompraModel();
        $resultado = $model->procesarCompraCarrito(
            $_SESSION['user_id'],
            $carrito,
            $total
        );

        // Si la compra fue exitosa, vaciar el carrito
        if ($resultado['success']) {
            CarritoController::vaciar();
        }

        return $resultado;
    }

    /**
     * Obtiene los datos necesarios para mostrar en el checkout
     * @return array|null Datos del carrito y totales
     */
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

    /**
     * Calcula cuántos puntos se generarán por un monto
     * @param float $monto Monto de la compra
     * @return int Puntos que se generarán
     */
    public static function calcularPuntos($monto)
    {
        return floor($monto / 10);
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
}

// Manejo de peticiones GET para obtener datos del checkout
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