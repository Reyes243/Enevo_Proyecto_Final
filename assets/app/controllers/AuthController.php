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
            //error_log("Error en constructor AuthController: " . $e->getMessage());
            $this->redirect('login', 'connection');
        }
    }
    
    /**
     * Método helper para redireccionar correctamente
     */
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
        
       // error_log("Redirigiendo a: {$url}");
        header("Location: {$url}");
        exit();
    }
    
    /**
     * Obtiene la URL base del proyecto
     */
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
           // error_log("=== INICIO LOGIN ===");
           // error_log("Email recibido: " . $email);
            
            $email = trim($email);
            $password = trim($password);

            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
               // error_log("Email inválido: " . $email);
                $this->redirect('login', 'invalid_email');
            }

            error_log("Buscando usuario en BD...");
            // Buscar usuario
            $user = $this->userModel->getUserByEmail($email);

            if (!$user) {
               // error_log("Usuario no encontrado para: " . $email);
                $this->redirect('login', 'login');
            }

           // error_log("Usuario encontrado - ID: " . $user['id'] . ", Nombre: " . $user['nombre']);
            //error_log("Hash en BD: " . substr($user['password'], 0, 20) . "...");
            
            // Verificar contraseña
            $passwordVerified = password_verify($password, $user['password']);
           // error_log("Verificación de contraseña: " . ($passwordVerified ? "EXITOSA" : "FALLIDA"));
            
            if ($passwordVerified) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_rol'] = $user['rol'] ?? 'cliente';
                
               // error_log("Sesión iniciada correctamente");
               // error_log("Rol asignado: " . $_SESSION['user_rol']);
                
                // Redirigir según el rol
                $baseUrl = $this->getBaseUrl();
                
                if ($_SESSION['user_rol'] === 'admin') {
                   // error_log("Redirigiendo a panel admin");
                    echo "<script>
                        localStorage.setItem('user_nombre', '" . addslashes($user['nombre']) . "');
                        localStorage.setItem('user_rol', 'admin');
                        localStorage.setItem('usuarioLogeado', 'true');
                        window.location.href = '{$baseUrl}/views/principalAdmin.php';
                    </script>";
                } else {
                   // error_log("Redirigiendo a principal");
                    echo "<script>
                        localStorage.setItem('user_nombre', '" . addslashes($user['nombre']) . "');
                        localStorage.setItem('user_rol', 'cliente');
                        localStorage.setItem('usuarioLogeado', 'true');
                        window.location.href = '{$baseUrl}/views/principal.php';
                    </script>";
                }
                exit();
            } else {
                //error_log("Contraseña incorrecta");
                $this->redirect('login', 'login');
            }
        } catch (Exception $e) {
           // error_log("Error en login: " . $e->getMessage());
           // error_log("Stack trace: " . $e->getTraceAsString());
            $this->redirect('login', 'server');
        }
    }
    
    public function register($nombre, $email, $password, $confirm) {
        try {
           // error_log("=== INICIO REGISTRO ===");
           // error_log("Nombre: {$nombre}, Email: {$email}");
            
            $nombre = trim($nombre);
            $email = trim($email);
            $password = trim($password);
            $confirm = trim($confirm);

            // Validar campos vacíos
            if ($nombre === '' || $email === '' || $password === '' || $confirm === '') {
            //    error_log("Campos vacíos detectados");
                $this->redirect('register', 'empty');
            }

            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
               // error_log("Email inválido: {$email}");
                $this->redirect('register', 'invalid_email');
            }

            // Validar longitud contraseña
            if (strlen($password) < 6) {
              //  error_log("Contraseña muy corta");
                $this->redirect('register', 'weak_password');
            }

            // Validar coincidencia de contraseñas
            if ($password !== $confirm) {
               // error_log("Contraseñas no coinciden");
                $this->redirect('register', 'password_mismatch');
            }

            // Verificar si el email ya existe
            $existing = $this->userModel->getUserByEmail($email);
            if ($existing) {
              //  error_log("Email ya existe: {$email}");
                $this->redirect('register', 'email_exists');
            }

            // Verificar si el username ya existe
            if ($this->userModel->usernameExists($nombre)) {
              //  error_log("Username ya existe: {$nombre}");
                $this->redirect('register', 'username_exists');
            }

            error_log("Intentando crear usuario...");
            // Intentar crear el usuario
            $result = $this->userModel->createUser($nombre, $email, $password);

            if ($result['success']) {
                // user_id devuelto
                $user_id = $result['user_id'] ?? null;

                // Crear entrada en clientes ligada al usuario
                $clientesModel = new ClientesModel($this->conn);
                $crearCliente = $clientesModel->crearClienteDesdeUsuario($user_id, $nombre, $email);

                if ($crearCliente['success']) {
                    $baseUrl = $this->getBaseUrl();
                    header("Location: {$baseUrl}/views/login.html?registered=1");
                    exit();
                } else {
                    // Si falla crear cliente, eliminar usuario creado para evitar inconsistencias
                    if ($user_id) {
                        $this->userModel->deleteUserById($user_id);
                    }
                    // Redirigir a registro con error
                    $this->redirect('register', 'database');
                }

            } else {
                //error_log("Error al crear usuario: " . $result['error']);
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
          //  error_log("Error en register: " . $e->getMessage());
           // error_log("Stack trace: " . $e->getTraceAsString());
            $this->redirect('register', 'server');
        }
    }
}

// Manejo de peticiones
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