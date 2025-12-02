<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/ConnectionController.php';
require_once __DIR__ . '/../models/JuegoModel.php';

class JuegoController {
    private $conn;
    public $juegoModel;
    
    public function __construct() {
        try {
            $connection = new ConnectionController();
            $this->conn = $connection->connect();

            if (!$this->conn) {
                throw new Exception("No se pudo conectar a la base de datos");
            }

            $this->juegoModel = new JuegoModel($this->conn);

        } catch (Exception $e) {
            error_log("Error constructor: " . $e->getMessage());
            $this->sendJson(['success' => false, 'error' => 'connection_failed']);
        }
    }

    private function sendJson($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /* =======================================
       OBTENER TODOS LOS JUEGOS
    ======================================= */
    public function getAllJuegos(){
        try{
            $juegos = $this->juegoModel->getAllJuegos();
            $this->sendJson(['success'=>true,'data'=>$juegos]);
        } catch(Exception $e){
            $this->sendJson(['success'=>false,'error'=>'fetch_failed']);
        }
    }

    /* =======================================
       OBTENER JUEGO POR ID (NECESARIO PARA CARRITO)
    ======================================= */
    public function getById($id){
        try {
            $juego = $this->juegoModel->getById($id);

            if (!$juego) {
                return ['success' => false, 'error' => 'not_found'];
            }

            return ['success' => true, 'data' => $juego];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'db_error'];
        }
    }
}


/* =======================================
   PETICIONES AJAX
======================================= */
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$controller = new JuegoController();

switch ($action) {

    case 'getAll':
        $controller->getAllJuegos();
        break;

    case 'getById':
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        echo json_encode($controller->getById($id));
        break;

    default:
        echo json_encode(['success'=>false,'error'=>'invalid_action']);
        break;
}

?>
