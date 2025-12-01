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

    /** GET ALL */
    public function getAllJuegos(){
        try{
            $juegos = $this->juegoModel->getAllJuegos();
            $this->sendJson(['success'=>true,'data'=>$juegos]);
        } catch(Exception $e){
            $this->sendJson(['success'=>false,'error'=>'fetch_failed']);
        }
    }
}

////// PETICIONES //////

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$controller = new JuegoController();

switch($action){

    case 'getAll':
        $controller->getAllJuegos();
        break;

    default:
        echo json_encode(['success'=>false,'error'=>'invalid_action']);
        break;
}
?>