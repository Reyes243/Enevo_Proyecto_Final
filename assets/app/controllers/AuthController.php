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
        $connection = new ConnectionController();
        $this->conn = $connection->connect();
        $this->userModel = new UserModel($this->conn);
    }

    public function login($email, $password) {
        $email = trim($email);
        $password = trim($password);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /Enevo_Proyecto_Final/views/login.html?error=invalid_email');
            exit();
        }

        $user = $this->userModel->getUserByEmail($email);
        if (!$user) {
            header('Location: /Enevo_Proyecto_Final/views/login.html?error=login');
            exit();
        }

        if (password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: /Enevo_Proyecto_Final/views/principal.html');
            exit();
        } else {
            header('Location: /Enevo_Proyecto_Final/views/login.html?error=login');
            exit();
        }
    }

    public function register($nombre, $email, $password) {
        $nombre = trim($nombre);
        $email = trim($email);
        $password = trim($password);

        if ($nombre === '' || $email === '' || $password === '') {
            header('Location: /Enevo_Proyecto_Final/views/register.html?error=empty');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /Enevo_Proyecto_Final/views/register.html?error=invalid_email');
            exit();
        }

        if (strlen($password) < 6) {
            header('Location: /Enevo_Proyecto_Final/views/register.html?error=weak_password');
            exit();
        }

        $existing = $this->userModel->getUserByEmail($email);
        if ($existing) {
            header('Location: /Enevo_Proyecto_Final/views/register.html?error=email_exists');
            exit();
        }

        $created = $this->userModel->createUser($nombre, $email, $password);
        if ($created) {
            header('Location: /Enevo_Proyecto_Final/views/login.html?registered=1');
            exit();
        } else {
            header('Location: /Enevo_Proyecto_Final/views/register.html?error=register_failed');
            exit();
        }
    }
}

$action = $_POST['action'] ?? '';

$auth = new AuthController();

if ($action === 'register') {
    $nombre = $_POST['nombre'] ?? '';
    $email  = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $auth->register($nombre, $email, $password);

} elseif ($action === 'login') {
    $email  = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $auth->login($email, $password);
} else {
    header('Location: /Enevo_Proyecto_Final/views/login.html');
    exit();
}
?>
