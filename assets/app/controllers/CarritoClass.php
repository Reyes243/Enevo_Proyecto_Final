<?php
// assets/app/controllers/CarritoClass.php
// SOLO LA CLASE - Para uso en PHP tradicional

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CarritoController
{
    public static function initCarrito()
    {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    public static function agregar($id, $nombre, $precio)
    {
        self::initCarrito();

        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += 1;
        } else {
            $_SESSION['carrito'][$id] = [
                'nombre'   => $nombre,
                'precio'   => $precio,
                'cantidad' => 1
            ];
        }
    }

    public static function eliminar($id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            if ($_SESSION['carrito'][$id]['cantidad'] > 1) {
                $_SESSION['carrito'][$id]['cantidad'] -= 1;
            } else {
                unset($_SESSION['carrito'][$id]);
            }
        }
    }

    public static function eliminarTodo($id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }
    }

    public static function vaciar()
    {
        $_SESSION['carrito'] = [];
    }

    public static function obtenerCarrito()
    {
        self::initCarrito();
        return $_SESSION['carrito'];
    }

    public static function total()
    {
        $total = 0;
        if (isset($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
        }
        return number_format($total, 2, '.', '');
    }
    
}
