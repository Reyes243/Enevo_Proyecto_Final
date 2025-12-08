<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/ConnectionController.php';
require_once __DIR__ . '/../models/NivelModel.php';

class NivelController {
    private $conn;
    public $nivelModel;
    
    public function __construct() {
        try {
            $connection = new ConnectionController();
            $this->conn = $connection->connect();

            if (!$this->conn) {
                throw new Exception("No se pudo conectar a la base de datos");
            }

            $this->nivelModel = new NivelModel($this->conn);

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

    public function getAllNiveles(){
        try{
            $niveles = $this->nivelModel->getAllNiveles();
            $this->sendJson(['success'=>true,'data'=>$niveles]);
        } catch(Exception $e){
            $this->sendJson(['success'=>false,'error'=>'fetch_failed']);
        }
    }

    public function getNivelById($id){
        if(!$id || !is_numeric($id))
            $this->sendJson(['success'=>false,'error'=>'invalid_id']);

        $nivel = $this->nivelModel->getNivelById($id);
        
        if(!$nivel)
            $this->sendJson(['success'=>false,'error'=>'not_found']);

        $this->sendJson(['success'=>true,'data'=>$nivel]);
    }

    public function createNivel($nombre,$puntos,$compras,$descripcion){
        $result = $this->nivelModel->createNivel($nombre,$puntos,$compras,$descripcion);
        $this->sendJson($result);
    }

    public function updateNivel($id,$nombre,$puntos,$compras,$descripcion){
        $result = $this->nivelModel->updateNivel($id,$nombre,$puntos,$compras,$descripcion);
        $this->sendJson($result);
    }

    public function deleteNivel($id){
        $result = $this->nivelModel->deleteNivel($id);
        $this->sendJson($result);
    }

    public function clientesByNivel($id){
        if(!$id || !is_numeric($id)){
            $this->sendJson(['success'=>false,'data'=>[]]);
        }

        $clientes = $this->nivelModel->getClientesByNivel($id);

        $this->sendJson([
            'success'=>true,
            'data'=>$clientes
        ]);
    }
}

////// PETICIONES //////

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$controller = new NivelController();

switch($action){

    case 'getAll':
        $controller->getAllNiveles();
        break;

    case 'getById':
        $controller->getNivelById($_GET['id'] ?? null);
        break;

    case 'create':
        $controller->createNivel($_POST['nombre'],$_POST['puntos'],$_POST['compras'],$_POST['descripcion']);
        break;

    case 'update':
        $controller->updateNivel($_POST['nivelId'],$_POST['nombre'],$_POST['puntos'],$_POST['compras'],$_POST['descripcion']);
        break;

    case 'delete':
        $controller->deleteNivel($_POST['id'] ?? null);
        break;

    case 'clientesByNivel':
        $controller->clientesByNivel($_GET['id'] ?? null);
        break;

    default:
        echo json_encode(['success'=>false,'error'=>'invalid_action']);
        break;
}
