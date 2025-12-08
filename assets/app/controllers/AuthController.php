<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/ConnectionController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ClientesModel.php';

class AuthController {
    private $conn;
    private $userModel;
    
    public function __construct() {
        try {
            $connection = new ConnectionController();
            $this->conn = $connection->connect();
            
            if (!$this->conn) {
                throw new Exception("No se pudo conectar a la base de datos");
            }
            
            $this->userModel = new UserModel($this->conn);
        } catch (Exception $e) {
            $this->redirect('login', 'connection');
        }
    }
    
    private function redirect($page, $error = null) {
        $baseUrl = $this->getBaseUrl();
        
        $pageMap = [
            'register' => 'registro.html',
            'login' => 'login.html',
            'principal' => 'principal.php'
        ];
        
        $fileName = $pageMap[$page] ?? $page . '.html';
        $url = "{$baseUrl}/views/{$fileName}";
        
        if ($error) {
            $url .= "?error={$error}";
        } elseif ($page === 'login' && isset($_GET['registered'])) {
            $url .= "?registered=1";
        }
        
        header("Location: {$url}");
        exit();
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $scriptPath = $_SERVER['SCRIPT_NAME'];
        $assetsPos = strpos($scriptPath, '/assets/');
        
        if ($assetsPos !== false) {
            $projectPath = substr($scriptPath, 0, $assetsPos);
        } else {
            $projectPath = dirname(dirname(dirname(dirname($scriptPath))));
        }
        
        return "{$protocol}://{$host}{$projectPath}";
    }
    
    public function login($email, $password) {
        try {
            $email = trim($email);
            $password = trim($password);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->redirect('login', 'invalid_email');
            }

            $user = $this->userModel->getUserByEmail($email);

            if (!$user) {
                $this->redirect('login', 'login');
            }
            
            $passwordVerified = password_verify($password, $user['password']);
            
            if ($passwordVerified) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_rol'] = $user['rol'] ?? 'cliente';
                
                $baseUrl = $this->getBaseUrl();
                
                if ($_SESSION['user_rol'] === 'admin') {
                    echo "<script>
                        localStorage.setItem('user_nombre', '" . addslashes($user['nombre']) . "');
                        localStorage.setItem('user_rol', 'admin');
                        localStorage.setItem('usuarioLogeado', 'true');
                        window.location.href = '{$baseUrl}/views/principalAdmin.php';
                    </script>";
                } else {
                    echo "<script>
                        localStorage.setItem('user_nombre', '" . addslashes($user['nombre']) . "');
                        localStorage.setItem('user_rol', 'cliente');
                        localStorage.setItem('usuarioLogeado', 'true');
                        window.location.href = '{$baseUrl}/views/principal.php';
                    </script>";
                }
                exit();
            } else {
                $this->redirect('login', 'login');
            }
        } catch (Exception $e) {
            $this->redirect('login', 'server');
        }
    }
    
    public function register($nombre, $email, $password, $confirm) {
        try {
            $nombre = trim($nombre);
            $email = trim($email);
            $password = trim($password);
            $confirm = trim($confirm);

            if ($nombre === '' || $email === '' || $password === '' || $confirm === '') {
                $this->redirect('register', 'empty');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->redirect('register', 'invalid_email');
            }

            if (strlen($password) < 6) {
                $this->redirect('register', 'weak_password');
            }

            if ($password !== $confirm) {
                $this->redirect('register', 'password_mismatch');
            }

            $existing = $this->userModel->getUserByEmail($email);
            if ($existing) {
                $this->redirect('register', 'email_exists');
            }

            if ($this->userModel->usernameExists($nombre)) {
                $this->redirect('register', 'username_exists');
            }

            $result = $this->userModel->createUser($nombre, $email, $password);

            if ($result['success']) {
                $user_id = $result['user_id'] ?? null;

                $clientesModel = new ClientesModel($this->conn);
                $crearCliente = $clientesModel->crearClienteDesdeUsuario($user_id, $nombre, $email);

                if ($crearCliente['success']) {
                    $baseUrl = $this->getBaseUrl();
                    header("Location: {$baseUrl}/views/login.html?registered=1");
                    exit();
                } else {
                    // Rollback: eliminar usuario si falla crear cliente
                    if ($user_id) {
                        $this->userModel->deleteUserById($user_id);
                    }
                    $this->redirect('register', 'database');
                }

            } else {
                switch ($result['error']) {
                    case 'duplicate':
                        $this->redirect('register', 'duplicate_data');
                        break;
                    case 'prepare_failed':
                    case 'execution_failed':
                        $this->redirect('register', 'database');
                        break;
                    default:
                        $this->redirect('register', 'register_failed');
                }
            }
        } catch (Exception $e) {
            $this->redirect('register', 'server');
        }
    }
}

try {
    $action = $_POST['action'] ?? '';
    $auth = new AuthController();

    if ($action === 'register') {
        $nombre   = $_POST['nombre'] ?? $_POST['name'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $auth->register($nombre, $email, $password, $confirm);

    } elseif ($action === 'login') {
        $email  = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $auth->login($email, $password);

    } else {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $parts = explode('/', $_SERVER['SCRIPT_NAME']);
        array_pop($parts);
        array_pop($parts);
        array_pop($parts);
        array_pop($parts);
        $base = implode('/', $parts);
        
        header("Location: {$protocol}://{$host}{$base}/views/login.html?error=invalid_action");
        exit();
    }
} catch (Exception $e) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $parts = explode('/', $_SERVER['SCRIPT_NAME']);
    array_pop($parts);
    array_pop($parts);
    array_pop($parts);
    array_pop($parts);
    $base = implode('/', $parts);
    
    header("Location: {$protocol}://{$host}{$base}/views/login.html?error=fatal");
    exit();
}
?>