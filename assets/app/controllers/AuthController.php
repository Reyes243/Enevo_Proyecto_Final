<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/ConnectionController.php';
require_once __DIR__ . '/../models/UserModel.php';

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
            error_log("Error en constructor AuthController: " . $e->getMessage());
            $this->redirect('login', 'connection');
        }
    }
    
    /**
     * Método helper para redireccionar correctamente
     */
    private function redirect($page, $error = null) {
        // Detectar la ruta base correctamente
        $baseUrl = $this->getBaseUrl();
        
        // Mapear nombres y extensiones
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
    
    /**
     * Obtiene la URL base del proyecto
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        
        // SCRIPT_NAME = /Enevo_Proyecto_Final/assets/app/controllers/AuthController.php
        // Necesitamos: /Enevo_Proyecto_Final
        
        $scriptPath = $_SERVER['SCRIPT_NAME'];
        
        // Encontrar la posición de /assets/
        $assetsPos = strpos($scriptPath, '/assets/');
        
        if ($assetsPos !== false) {
            // Tomar todo antes de /assets/
            $projectPath = substr($scriptPath, 0, $assetsPos);
        } else {
            // Fallback
            $projectPath = dirname(dirname(dirname(dirname($scriptPath))));
        }
        
        return "{$protocol}://{$host}{$projectPath}";
    }
    
    public function login($email, $password) {
    try {
        $email = trim($email);
        $password = trim($password);

        // 1. Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('login', 'invalid_email');
        }

        // 2. Buscar usuario
        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            $this->redirect('login', 'login_failed'); // Usuario no existe
        }

        // 3. Verificar contraseña
        if (password_verify($password, $user['password'])) {
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            session_regenerate_id(true);
            
            // Guardar datos en Sesión PHP
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_rol'] = $user['rol'] ?? 'cliente'; 

            // 4. Lógica de Redirección por ROL (SOLUCIÓN A TU PROBLEMA)
            if ($_SESSION['user_rol'] == 1 || $_SESSION['user_rol'] == 'admin') {
                // Si es admin, mándalo a su dashboard
                $baseUrl = $this->getBaseUrl();
                header("Location: {$baseUrl}/views/admin_dashboard.php"); 
            } else {
                
                $this->redirect('principal'); 
            }
            
            exit();

        } else {
            // Contraseña incorrecta
            $this->redirect('login', 'login_failed');
        }
    } catch (Exception $e) {
        error_log("Error en login: " . $e->getMessage());
        $this->redirect('login', 'server');
    }
}
    
    public function register($nombre, $email, $password, $confirm) {
        try {
            $nombre = trim($nombre);
            $email = trim($email);
            $password = trim($password);
            $confirm = trim($confirm);

            // Validar campos vacíos
            if ($nombre === '' || $email === '' || $password === '' || $confirm === '') {
                $this->redirect('register', 'empty');
            }

            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->redirect('register', 'invalid_email');
            }

            // Validar longitud contraseña
            if (strlen($password) < 6) {
                $this->redirect('register', 'weak_password');
            }

            // Validar coincidencia de contraseñas
            if ($password !== $confirm) {
                $this->redirect('register', 'password_mismatch');
            }

            // Verificar si el email ya existe
            $existing = $this->userModel->getUserByEmail($email);
            if ($existing) {
                $this->redirect('register', 'email_exists');
            }

            // Verificar si el username ya existe
            if ($this->userModel->usernameExists($nombre)) {
                $this->redirect('register', 'username_exists');
            }

            // Intentar crear el usuario
            $result = $this->userModel->createUser($nombre, $email, $password);

            if ($result['success']) {
                // Redirigir a login con mensaje de éxito
                $baseUrl = $this->getBaseUrl();
                header("Location: {$baseUrl}/views/login.html?registered=1");
                exit();
            } else {
                // Manejar diferentes tipos de error
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
            error_log("Error en register: " . $e->getMessage());
            $this->redirect('register', 'server');
        }
    }
}

// Manejo de peticiones
try {
    $action = $_POST['action'] ?? '';
    $auth = new AuthController();

    if ($action === 'register') {
        // IMPORTANTE: Cambiar 'name' por 'nombre' para que coincida con tu HTML
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
        // Calcular ruta base manualmente para este caso
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $parts = explode('/', $_SERVER['SCRIPT_NAME']);
        array_pop($parts); // AuthController.php
        array_pop($parts); // controllers
        array_pop($parts); // app
        array_pop($parts); // assets
        $base = implode('/', $parts);
        
        header("Location: {$protocol}://{$host}{$base}/views/login.html?error=invalid_action");
        exit();
    }
} catch (Exception $e) {
    error_log("Error general en AuthController: " . $e->getMessage());
    
    // Calcular ruta base manualmente para este caso
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $parts = explode('/', $_SERVER['SCRIPT_NAME']);
    array_pop($parts); // AuthController.php
    array_pop($parts); // controllers
    array_pop($parts); // app
    array_pop($parts); // assets
    $base = implode('/', $parts);
    
    header("Location: {$protocol}://{$host}{$base}/views/login.html?error=fatal");
    exit();
}
?>