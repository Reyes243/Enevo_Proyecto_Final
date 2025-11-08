<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); 

	include "ConnectionController.php";

	class AuthController { 

		private $conn;

		public function __construct() {
			$connection = new ConnectionController();
			$this->conn = $connection->connect();
		}

        public function login($email, $password) {
            if ($this->conn->connect_error) {
                header('Location: /Enevo_Proyecto_Final/views/login.html?error=connection');
                exit();
            }
            
            $query = "SELECT * FROM usuarios WHERE email = ? AND password_hash = ?";
            $prepared_query = $this->conn->prepare($query);
            $prepared_query->bind_param('ss', $email, $password);
            $prepared_query->execute();
            
            $results = $prepared_query->get_result();
            $users = $results->fetch_all(MYSQLI_ASSOC);

			

            if (count($users) > 0) {
                header('Location: /Enevo_Proyecto_Final/views/principal.html');
                exit();
            } else {
                header('Location: /Enevo_Proyecto_Final/views/login.html?error=login');
                exit();
            }
        }
		public function register($nombre, $email, $password) {
			$stmt = $this->conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
			$stmt->bind_param("sss", $nombre, $email, $password);

			if ($stmt->execute()) {
				$stmt->close();
				header("Location: ../../index.html?registered=1");
				exit();
			} else {
				$stmt->close();
				header("Location: ../../register.html?error=1");
				exit();
			}
		}
	}

	$action = $_POST['action'] ?? '';

	if ($action == "register") {
		$nombre = $_POST['nombre'] ?? '';
		$email = $_POST['email'] ?? '';
		$password = $_POST['password'] ?? '';
		
		$auth = new AuthController();
		$auth->register($nombre, $email, $password);
		
	} elseif ($action == "login") {
		$email = $_POST['email'] ?? '';
		$password = $_POST['password'] ?? '';
		
		$auth = new AuthController();
		$auth->login($email, $password);
	}
	?>