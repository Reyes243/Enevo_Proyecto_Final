<?php
require_once __DIR__ . '/../models/ClientesModel.php';

class ClientesController {
    private $model;

    public function __construct($database) {
        $this->model = new ClientesModel($database);
    }

    public function listarClientes() {
        $clientes = $this->model->obtenerTodosLosClientes();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'clientes' => $clientes
        ]);
    }

    public function obtenerCliente($id) {
        $cliente = $this->model->obtenerClientePorId($id);
        
        if (!$cliente) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]);
            return;
        }

        $siguienteNivel = $this->model->obtenerSiguienteNivel($id);
        $totalCompras = $this->model->contarComprasCliente($id);
        
        $comprasFaltantes = 0;
        if ($siguienteNivel) {
            $comprasFaltantes = max(0, $siguienteNivel['compras_necesarias'] - $totalCompras);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cliente' => $cliente,
            'siguiente_nivel' => $siguienteNivel,
            'total_compras' => $totalCompras,
            'compras_faltantes' => $comprasFaltantes
        ]);
    }

    public function crearCliente($datos) {
        if (empty($datos['nombre']) || empty($datos['email']) || empty($datos['password'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
            return;
        }

        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'El formato del correo electrónico no es válido'
            ]);
            return;
        }

        if (strlen($datos['password']) < 6) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
            return;
        }

        $resultado = $this->model->crearCliente(
            $datos['nombre'],
            $datos['email'],
            $datos['password']
        );

        header('Content-Type: application/json');
        if ($resultado['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'cliente_id' => $resultado['cliente_id']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $resultado['error']
            ]);
        }
    }

    public function actualizarCliente($id, $datos) {
        if (empty($datos['nombre']) || empty($datos['email'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'El nombre y el correo son obligatorios'
            ]);
            return;
        }

        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'El formato del correo electrónico no es válido'
            ]);
            return;
        }

        $resultado = $this->model->actualizarCliente(
            $id,
            $datos['nombre'],
            $datos['email']
        );

        header('Content-Type: application/json');
        if ($resultado['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el cliente: ' . $resultado['error']
            ]);
        }
    }

    public function eliminarCliente($id) {
        $resultado = $this->model->eliminarCliente($id);

        header('Content-Type: application/json');
        if ($resultado['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el cliente: ' . $resultado['error']
            ]);
        }
    }

    public function listarClientesPorNivel($nivel_id) {
        $clientes = $this->model->obtenerClientesPorNivel($nivel_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'clientes' => $clientes
        ]);
    }
}

// Verificar autenticación y rol de admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No autorizado'
        ]);
        exit();
    }

    require_once __DIR__ . '/../config/ConnectionController.php';
    
    $connection = new ConnectionController();
    $conn = $connection->connect();
    
    if (!$conn) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos'
        ]);
        exit();
    }

    $controller = new ClientesController($conn);
    $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

    switch ($accion) {
        case 'listar':
            $controller->listarClientes();
            break;

        case 'obtener':
            $id = $_GET['id'] ?? 0;
            $controller->obtenerCliente($id);
            break;

        case 'crear':
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? ''
            ];
            $controller->crearCliente($datos);
            break;

        case 'actualizar':
            $id = $_POST['id'] ?? 0;
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];
            $controller->actualizarCliente($id, $datos);
            break;

        case 'eliminar':
            $id = $_POST['id'] ?? 0;
            $controller->eliminarCliente($id);
            break;

        case 'listarPorNivel':
            $nivel_id = $_GET['nivel_id'] ?? 0;
            $controller->listarClientesPorNivel($nivel_id);
            break;

        default:
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
}
?>